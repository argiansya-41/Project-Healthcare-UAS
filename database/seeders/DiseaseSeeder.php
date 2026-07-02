<?php

namespace Database\Seeders;

use App\Models\DiseaseType;
use App\Models\DiseaseReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiseaseSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'DBD', 'name' => 'Demam Berdarah Dengue', 'description' => 'Penyakit virus yang ditularkan oleh nyamuk Aedes aegypti'],
            ['code' => 'TBC', 'name' => 'Tuberkulosis', 'description' => 'Penyakit bakteri menular paru-paru'],
            ['code' => 'DIA', 'name' => 'Diare Akut', 'description' => 'Kondisi buang air besar yang encer atau cair dalam frekuensi sering'],
            ['code' => 'ISPA', 'name' => 'Infeksi Saluran Pernapasan Akut', 'description' => 'Infeksi akut pada saluran napas atas atau bawah'],
            ['code' => 'COVID', 'name' => 'COVID-19', 'description' => 'Infeksi virus SARS-CoV-2'],
        ];
        foreach ($types as $t) {
            DiseaseType::create($t);
        }

        $reporter = User::where('role', 'petugas_medis')->first() ?? User::first();
        $doctor = User::where('role', 'petugas_medis')->orderBy('id', 'desc')->first();

        if ($reporter) {
            // report 1 (verified)
            DiseaseReport::create([
                'reporter_id' => $reporter->id,
                'patient_name' => 'Joko Susilo',
                'patient_nik' => '3201010101010002',
                'patient_age' => 28,
                'patient_gender' => 'L',
                'patient_address' => 'Jl. Kebon Jeruk No. 12 RT 01/RW 02, Jakarta',
                'latitude' => -6.1894,
                'longitude' => 106.7628,
                'disease_type_id' => 1, // DBD
                'symptoms' => 'Demam tinggi mendadak, sakit kepala, nyeri di belakang mata, bintik merah di kulit.',
                'severity' => 'sedang',
                'report_date' => '2026-06-12',
                'status' => 'verified',
                'verified_by' => $reporter->id,
                'verification_notes' => 'Pasien telah menunjukkan hasil tes trombosit di bawah 100.000.',
                'treatment_recommendation' => $doctor ? 'Istirahat total, minum air putih minimal 2 liter per hari, dan konsumsi paracetamol bila demam.' : null,
            ]);

            // report 2 (pending)
            DiseaseReport::create([
                'reporter_id' => $reporter->id,
                'patient_name' => 'Slamet Riadi',
                'patient_nik' => '3201010101010003',
                'patient_age' => 45,
                'patient_gender' => 'L',
                'patient_address' => 'Kampung Rawa Indah RT 05/RW 10, Jakarta',
                'latitude' => -6.1950,
                'longitude' => 106.7700,
                'disease_type_id' => 2, // TBC
                'symptoms' => 'Batuk berdahak lebih dari 2 minggu, batuk berdarah, penurunan berat badan drastis, keringat malam.',
                'severity' => 'berat',
                'report_date' => '2026-06-14',
                'status' => 'pending',
            ]);

            // report 3 (verified)
            DiseaseReport::create([
                'reporter_id' => $reporter->id,
                'patient_name' => 'Siti Aminah',
                'patient_nik' => '3201010101010004',
                'patient_age' => 3,
                'patient_gender' => 'P',
                'patient_address' => 'Pekojan Raya RT 02/RW 03, Jakarta',
                'latitude' => -6.1820,
                'longitude' => 106.7550,
                'disease_type_id' => 3, // Diare
                'symptoms' => 'Muntaber lebih dari 5 kali sehari, lemas, tidak mau minum susu.',
                'severity' => 'sedang',
                'report_date' => '2026-06-15',
                'status' => 'verified',
                'verified_by' => $reporter->id,
                'verification_notes' => 'Tanda-tanda dehidrasi sedang, direkomendasikan infus cairan.',
            ]);
        }
    }
}
