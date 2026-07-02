<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@healthcare.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'nik' => '1234567890123456',
                'phone_number' => '081234567890',
                'address' => 'Jl. Kesehatan No. 1, Jakarta',
                'gender' => 'L',
            ],
            [
                'name' => 'Budi Apoteker',
                'email' => 'apoteker@healthcare.test',
                'password' => Hash::make('apoteker123'),
                'role' => 'apoteker',
                'nik' => '1234567890123457',
                'phone_number' => '081234567891',
                'address' => 'Jl. Apotek No. 2, Jakarta',
                'gender' => 'L',
            ],
            [
                'name' => 'Siti Kesehatan',
                'email' => 'petugas_kes@healthcare.test',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas_medis',
                'nik' => '1234567890123458',
                'phone_number' => '081234567892',
                'address' => 'Jl. Puskesmas No. 3, Jakarta',
                'gender' => 'P',
            ],
            [
                'name' => 'Dr. Andi Pratama',
                'email' => 'dokter@healthcare.test',
                'password' => Hash::make('dokter123'),
                'role' => 'petugas_medis',
                'nik' => '1234567890123459',
                'phone_number' => '081234567893',
                'address' => 'Perum Lestari Block C/4, Jakarta',
                'gender' => 'L',
            ],
            [
                'name' => 'Rina Imunisasi',
                'email' => 'petugas_imun@healthcare.test',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas_medis',
                'nik' => '1234567890123460',
                'phone_number' => '081234567894',
                'address' => 'Jl. Kasih Ibu No. 4, Jakarta',
                'gender' => 'P',
            ],
            [
                'name' => 'Dr. H. Ahmad Fauzi (Kepala)',
                'email' => 'kepala@healthcare.test',
                'password' => Hash::make('kepala123'),
                'role' => 'admin',
                'nik' => '1234567890123461',
                'phone_number' => '081234567895',
                'address' => 'Jl. Puskesmas No. 1, Jakarta',
                'gender' => 'L',
            ],
            [
                'name' => 'Ani Wijaya (Warga)',
                'email' => 'warga@healthcare.test',
                'password' => Hash::make('warga123'),
                'role' => 'warga',
                'nik' => '3201010101010001',
                'phone_number' => '089876543210',
                'address' => 'Kampung Melati RT 03/RW 04, Jakarta',
                'gender' => 'P',
            ],
            [
                'name' => 'Doni Operator',
                'email' => 'operator@healthcare.test',
                'password' => Hash::make('operator123'),
                'role' => 'petugas_medis',
                'nik' => '1234567890123462',
                'phone_number' => '081234567896',
                'address' => 'Jl. Operator No. 10, Jakarta',
                'gender' => 'L',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
