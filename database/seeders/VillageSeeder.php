<?php

namespace Database\Seeders;

use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villages = [
            [
                'name' => 'Kedoya Selatan',
                'kecamatan' => 'Kebon Jeruk',
                'kabupaten' => 'Jakarta Barat',
                'latitude' => -6.18940000,
                'longitude' => 106.76280000,
            ],
            [
                'name' => 'Meruya Utara',
                'kecamatan' => 'Kembangan',
                'kabupaten' => 'Jakarta Barat',
                'latitude' => -6.19850000,
                'longitude' => 106.73500000,
            ],
            [
                'name' => 'Pekojan',
                'kecamatan' => 'Tambora',
                'kabupaten' => 'Jakarta Barat',
                'latitude' => -6.13840000,
                'longitude' => 106.80410000,
            ],
            [
                'name' => 'Tomang',
                'kecamatan' => 'Grogol Petamburan',
                'kabupaten' => 'Jakarta Barat',
                'latitude' => -6.17890000,
                'longitude' => 106.79720000,
            ],
        ];

        foreach ($villages as $village) {
            Village::updateOrCreate(
                ['name' => $village['name'], 'kecamatan' => $village['kecamatan'], 'kabupaten' => $village['kabupaten']],
                $village
            );
        }
    }
}
