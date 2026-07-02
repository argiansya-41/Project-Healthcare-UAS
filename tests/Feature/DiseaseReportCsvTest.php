<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DiseaseReport;
use App\Models\DiseaseType;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class DiseaseReportCsvTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diseaseType;
    protected $village;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->diseaseType = DiseaseType::create([
            'code' => 'DBD',
            'name' => 'Demam Berdarah Dengue',
            'description' => 'Demam Berdarah Dengue'
        ]);

        $this->village = Village::create([
            'name' => 'Meruya Utara',
            'kecamatan' => 'Kembangan',
            'kabupaten' => 'Jakarta Barat',
            'latitude' => -6.208,
            'longitude' => 106.745
        ]);
    }

    public function test_user_can_download_csv_template()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('kesehatan.reports.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_laporan_kasus.csv');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Nama Pasien', $content);
        $this->assertStringContainsString('NIK', $content);
        $this->assertStringContainsString('Umur', $content);
        $this->assertStringContainsString('Jenis Kelamin', $content);
    }

    public function test_user_can_export_reports_to_csv()
    {
        // Seed a report
        DiseaseReport::create([
            'patient_name' => 'Anandi',
            'patient_nik' => '1234567890123456',
            'patient_age' => 20,
            'patient_gender' => 'P',
            'patient_address' => 'Meruya Utara No. 1',
            'disease_type_id' => $this->diseaseType->id,
            'symptoms' => 'Demam tinggi',
            'severity' => 'ringan',
            'report_date' => now(),
            'village_id' => $this->village->id,
            'reporter_id' => $this->admin->id,
            'status' => 'verified'
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('kesehatan.reports.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('Anandi', $response->streamedContent());
        $this->assertStringContainsString('1234567890123456', $response->streamedContent());
    }

    public function test_user_can_import_valid_csv()
    {
        $this->actingAs($this->admin);

        $csvContent = "Nama Pasien,NIK,Umur,Jenis Kelamin (L/P),Alamat,Desa/Kelurahan,Kode Penyakit,Gejala,Tingkat Keparahan (ringan/sedang/berat),Tanggal Lapor (YYYY-MM-DD),Latitude,Longitude\n" .
                      "Budi Santoso,3201010101010099,30,L,Meruya Utara,Meruya Utara,DBD,Demam tinggi,sedang,2026-06-18,-6.208,106.745\n";

        $file = UploadedFile::fake()->createWithContent('laporan.csv', $csvContent);

        $response = $this->post(route('kesehatan.reports.import'), [
            'file' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Berhasil mengimpor 1 laporan kasus penyakit.');

        $this->assertDatabaseHas('disease_reports', [
            'patient_name' => 'Budi Santoso',
            'patient_nik' => '3201010101010099',
            'patient_gender' => 'L',
            'village_id' => $this->village->id,
            'disease_type_id' => $this->diseaseType->id,
            'severity' => 'sedang'
        ]);
    }

    public function test_import_validation_fails_and_rolls_back_on_invalid_data()
    {
        $this->actingAs($this->admin);

        // NIK is invalid (15 chars instead of 16), Village is unregistered, disease is unregistered
        $csvContent = "Nama Pasien,NIK,Umur,Jenis Kelamin (L/P),Alamat,Desa/Kelurahan,Kode Penyakit,Gejala,Tingkat Keparahan (ringan/sedang/berat),Tanggal Lapor (YYYY-MM-DD),Latitude,Longitude\n" .
                      "Budi Santoso,320101010101009,30,L,Meruya Utara,Unknown Village,XYZ,Demam tinggi,sedang,2026-06-18,-6.208,106.745\n";

        $file = UploadedFile::fake()->createWithContent('laporan_error.csv', $csvContent);

        $response = $this->post(route('kesehatan.reports.import'), [
            'file' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('import_errors');
        $response->assertSessionHas('error', 'Gagal mengimpor file CSV karena terdapat kesalahan data.');

        $errors = session('import_errors');
        $this->assertCount(1, $errors); 
        $this->assertStringContainsString('NIK harus tepat 16 karakter', $errors[0]);
        $this->assertStringContainsString("Desa/Kelurahan 'Unknown Village' tidak terdaftar", $errors[0]);
        $this->assertStringContainsString("Penyakit 'XYZ' tidak terdaftar", $errors[0]);

        // Verify database has no new records (rolled back)
        $this->assertDatabaseMissing('disease_reports', [
            'patient_name' => 'Budi Santoso'
        ]);
    }
}
