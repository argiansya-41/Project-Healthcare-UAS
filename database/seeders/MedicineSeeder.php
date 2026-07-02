<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineUnit;
use App\Models\Supplier;
use App\Models\MedicineTransaction;
use App\Models\RestockRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        // categories
        $categories = [
            ['name' => 'Analgesik & Antipiretik', 'description' => 'Pereda nyeri dan demam'],
            ['name' => 'Antibiotik', 'description' => 'Mengobati infeksi bakteri'],
            ['name' => 'Antihistamin', 'description' => 'Mengobati alergi'],
            ['name' => 'Vitamin & Suplemen', 'description' => 'Menjaga kesehatan tubuh'],
            ['name' => 'Antasida', 'description' => 'Pereda asam lambung'],
        ];
        foreach ($categories as $cat) {
            MedicineCategory::create($cat);
        }

        // units
        $units = [
            ['name' => 'Tablet', 'abbreviation' => 'Tab'],
            ['name' => 'Kapsul', 'abbreviation' => 'Kap'],
            ['name' => 'Botol Syrup', 'abbreviation' => 'Btl'],
            ['name' => 'Tube Salep', 'abbreviation' => 'Tub'],
            ['name' => 'Pcs', 'abbreviation' => 'Pcs'],
        ];
        foreach ($units as $unit) {
            MedicineUnit::create($unit);
        }

        // suppliers
        $suppliers = [
            ['name' => 'PT. Kimia Farma Trading', 'contact_name' => 'Budi Santoso', 'phone' => '021-555667', 'address' => 'Kawasan Industri Pulo Gadung, Jakarta'],
            ['name' => 'PT. Bina San Prima', 'contact_name' => 'Yanti Rahayu', 'phone' => '021-888990', 'address' => 'Jl. TB Simatupang, Jakarta'],
        ];
        foreach ($suppliers as $sup) {
            Supplier::create($sup);
        }

        // medicines
        $medicines = [
            [
                'category_id' => 1,
                'unit_id' => 1,
                'code' => 'OBT-001',
                'name' => 'Paracetamol 500mg',
                'description' => 'Pereda demam dan nyeri ringan',
                'stock' => 120,
                'min_stock' => 50,
                'purchase_price' => 200.00,
                'selling_price' => 500.00,
                'expiration_date' => '2027-12-31',
            ],
            [
                'category_id' => 1,
                'unit_id' => 3,
                'code' => 'OBT-002',
                'name' => 'Paracetamol Syrup 60ml',
                'description' => 'Pereda demam anak-anak',
                'stock' => 15, // hampir habis
                'min_stock' => 20,
                'purchase_price' => 4500.00,
                'selling_price' => 6000.00,
                'expiration_date' => '2026-11-30',
            ],
            [
                'category_id' => 2,
                'unit_id' => 2,
                'code' => 'OBT-003',
                'name' => 'Amoxicillin 500mg',
                'description' => 'Antibiotik infeksi bakteri',
                'stock' => 200,
                'min_stock' => 50,
                'purchase_price' => 600.00,
                'selling_price' => 1000.00,
                'expiration_date' => '2027-06-30',
            ],
            [
                'category_id' => 4,
                'unit_id' => 1,
                'code' => 'OBT-004',
                'name' => 'Vitamin C 500mg',
                'description' => 'Suplemen daya tahan tubuh',
                'stock' => 5, // kritis
                'min_stock' => 30,
                'purchase_price' => 150.00,
                'selling_price' => 300.00,
                'expiration_date' => '2026-05-15', // sudah kadaluarsa (local time is 2026-06-15)
            ],
        ];
        foreach ($medicines as $med) {
            Medicine::create($med);
        }

        // transactions & restock requests
        $apoteker = User::where('role', 'apoteker')->first();
        $admin = User::where('role', 'admin')->first();

        if ($apoteker) {
            // transaction in
            MedicineTransaction::create([
                'medicine_id' => 1,
                'supplier_id' => 1,
                'type' => 'in',
                'quantity' => 120,
                'notes' => 'Stok Awal Sistem',
                'transaction_date' => '2026-06-10',
                'user_id' => $apoteker->id,
            ]);

            MedicineTransaction::create([
                'medicine_id' => 2,
                'supplier_id' => 2,
                'type' => 'in',
                'quantity' => 15,
                'notes' => 'Stok Awal Sistem',
                'transaction_date' => '2026-06-10',
                'user_id' => $apoteker->id,
            ]);

            // request restock
            RestockRequest::create([
                'user_id' => $apoteker->id,
                'medicine_id' => 2,
                'quantity' => 50,
                'status' => 'pending',
            ]);

            RestockRequest::create([
                'user_id' => $apoteker->id,
                'medicine_id' => 4,
                'quantity' => 100,
                'status' => 'approved',
                'approved_by' => $admin ? $admin->id : null,
                'approved_at' => now(),
            ]);
        }
    }
}
