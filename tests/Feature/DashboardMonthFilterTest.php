<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DiseaseReport;
use App\Models\DiseaseType;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardMonthFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_month_filter_works()
    {
        $diseaseType = DiseaseType::create([
            'code' => 'DBD',
            'name' => 'Demam Berdarah Dengue',
            'description' => 'DBD'
        ]);

        $village = Village::create([
            'name' => 'Tomang',
            'kecamatan' => 'Grogol',
            'kabupaten' => 'Jakbar',
            'latitude' => -6.17,
            'longitude' => 106.79
        ]);

        $petugas = User::create([
            'name' => 'Siti Kesehatan',
            'email' => 'petugas@test.com',
            'password' => bcrypt('password'),
            'role' => 'petugas_medis'
        ]);

        // Report in June (Month 6)
        DiseaseReport::create([
            'patient_name' => 'Patient June',
            'patient_nik' => '1234567890123456',
            'patient_age' => 30,
            'patient_gender' => 'L',
            'patient_address' => 'Jl. Tomang',
            'disease_type_id' => $diseaseType->id,
            'symptoms' => 'Demam',
            'severity' => 'berat',
            'report_date' => '2026-06-12',
            'village_id' => $village->id,
            'reporter_id' => $petugas->id,
            'status' => 'verified'
        ]);

        // Report in May (Month 5)
        DiseaseReport::create([
            'patient_name' => 'Patient May',
            'patient_nik' => '1234567890123457',
            'patient_age' => 25,
            'patient_gender' => 'P',
            'patient_address' => 'Jl. Tomang',
            'disease_type_id' => $diseaseType->id,
            'symptoms' => 'Demam',
            'severity' => 'ringan',
            'report_date' => '2026-05-15',
            'village_id' => $village->id,
            'reporter_id' => $petugas->id,
            'status' => 'verified'
        ]);

        $this->actingAs($petugas);

        // Request dashboard with June filter
        $response = $this->get(route('dashboard', ['severity_month' => 6]));
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['severity_stats']['ringan'] === 0 &&
                   $stats['severity_stats']['berat'] === 1;
        });

        // Request dashboard with May filter
        $response = $this->get(route('dashboard', ['severity_month' => 5]));
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['severity_stats']['ringan'] === 1 &&
                   $stats['severity_stats']['berat'] === 0;
        });

        // Request dashboard with no filter (should show both)
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['severity_stats']['ringan'] === 1 &&
                   $stats['severity_stats']['berat'] === 1;
        });
    }
}
