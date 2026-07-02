<?php

namespace Database\Seeders;

use App\Models\ImmunizationVaccine;
use App\Models\Child;
use App\Models\ImmunizationRecord;
use App\Models\ImmunizationReminder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImmunizationSeeder extends Seeder
{
    public function run(): void
    {
        $vaccines = [
            ['name' => 'Hepatitis B (HB-0)', 'code' => 'HB-0', 'target_age_months' => 0, 'description' => 'Mencegah infeksi Hepatitis B, diberikan dalam 24 jam setelah lahir.'],
            ['name' => 'BCG', 'code' => 'BCG', 'target_age_months' => 1, 'description' => 'Mencegah penyakit Tuberkulosis paru dan meningitis.'],
            ['name' => 'Polio 1', 'code' => 'OPV-1', 'target_age_months' => 1, 'description' => 'Mencegah kelumpuhan akibat virus Polio.'],
            ['name' => 'DPT-HB-Hib 1', 'code' => 'DPT-1', 'target_age_months' => 2, 'description' => 'Mencegah Difteri, Pertusis (Batuk Rejan), Tetanus, Hepatitis B, meningitis & pneumonia.'],
            ['name' => 'Campak-Rubella (MR)', 'code' => 'MR', 'target_age_months' => 9, 'description' => 'Mencegah Campak dan Rubella.'],
        ];
        foreach ($vaccines as $v) {
            ImmunizationVaccine::create($v);
        }

        $warga = User::where('role', 'warga')->first();
        $officer = User::where('role', 'petugas_medis')->first();

        if ($warga) {
            // child
            $child = Child::create([
                'parent_id' => $warga->id,
                'name' => 'Rian Wijaya',
                'nik' => '3201010101010005',
                'gender' => 'L',
                'date_of_birth' => '2025-10-15', // Usia sekitar 8 bulan pada juni 2026
                'place_of_birth' => 'Jakarta',
                'birth_weight' => 3.20,
            ]);

            // record 1: completed
            $rec1 = ImmunizationRecord::create([
                'child_id' => $child->id,
                'vaccine_id' => 1, // HB-0
                'officer_id' => $officer ? $officer->id : null,
                'status' => 'completed',
                'scheduled_date' => '2025-10-15',
                'administered_date' => '2025-10-15',
                'batch_number' => 'B-HB0-9988',
                'notes' => 'Diberikan segera setelah lahir di RS.',
            ]);

            // record 2: completed
            $rec2 = ImmunizationRecord::create([
                'child_id' => $child->id,
                'vaccine_id' => 2, // BCG
                'officer_id' => $officer ? $officer->id : null,
                'status' => 'completed',
                'scheduled_date' => '2025-11-15',
                'administered_date' => '2025-11-17',
                'batch_number' => 'B-BCG-1122',
                'notes' => 'Reaksi normal bekas suntikan muncul setelah 2 minggu.',
            ]);

            // record 3: scheduled (upcoming vaccine MR at 9 months)
            // MR is target age 9 months. 2025-10-15 + 9 months = 2026-07-15. Today is 2026-06-15.
            $rec3 = ImmunizationRecord::create([
                'child_id' => $child->id,
                'vaccine_id' => 5, // MR
                'status' => 'scheduled',
                'scheduled_date' => '2026-07-15',
            ]);

            // reminder
            ImmunizationReminder::create([
                'record_id' => $rec3->id,
                'parent_id' => $warga->id,
                'send_date' => '2026-07-10', // 5 hari sebelum imunisasi
                'status' => 'pending',
                'channel' => 'dashboard',
            ]);
        }
    }
}
