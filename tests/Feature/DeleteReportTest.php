<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DiseaseReport;
use App\Models\DiseaseType;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_medis_can_delete_report()
    {
        // Setup data
        $diseaseType = DiseaseType::create([
            'code' => 'DBD',
            'name' => 'Demam Berdarah Dengue',
            'description' => 'Demam Berdarah Dengue'
        ]);

        $village = Village::create([
            'name' => 'Tomang',
            'kecamatan' => 'Grogol Petamburan',
            'kabupaten' => 'Jakarta Barat',
            'latitude' => -6.175,
            'longitude' => 106.79
        ]);

        $petugas = User::create([
            'name' => 'Siti Kesehatan',
            'email' => 'petugas@test.com',
            'password' => bcrypt('password'),
            'role' => 'petugas_medis'
        ]);

        $report = DiseaseReport::create([
            'patient_name' => 'Budi Santoso',
            'patient_nik' => '1234567890123456',
            'patient_age' => 30,
            'patient_gender' => 'L',
            'patient_address' => 'Jl. Tomang Raya No. 1',
            'disease_type_id' => $diseaseType->id,
            'symptoms' => 'Demam, pusing',
            'severity' => 'sedang',
            'report_date' => now(),
            'village_id' => $village->id,
            'reporter_id' => $petugas->id,
            'status' => 'pending'
        ]);

        $this->actingAs($petugas);

        $response = $this->delete(route('kesehatan.reports.destroy', $report->id));

        $response->assertRedirect(route('kesehatan.reports.index'));
        $this->assertDatabaseMissing('disease_reports', [
            'id' => $report->id
        ]);
    }
}
