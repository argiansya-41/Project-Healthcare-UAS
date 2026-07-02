<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WargaMedicineListTest extends TestCase
{
    use RefreshDatabase;

    protected $warga;
    protected $apoteker;
    protected $medicine1;
    protected $medicine2;
    protected $category1;
    protected $category2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warga = User::create([
            'name' => 'Warga Uji',
            'email' => 'warga@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
        ]);

        $this->apoteker = User::create([
            'name' => 'Apoteker Uji',
            'email' => 'apoteker@test.com',
            'password' => bcrypt('password'),
            'role' => 'apoteker'
        ]);

        $this->category1 = MedicineCategory::create([
            'name' => 'Analgesik',
            'description' => 'Pereda nyeri'
        ]);

        $this->category2 = MedicineCategory::create([
            'name' => 'Antibiotik',
            'description' => 'Mengobati infeksi'
        ]);

        $unit = MedicineUnit::create([
            'name' => 'Tablet',
            'abbreviation' => 'Tab'
        ]);

        $this->medicine1 = Medicine::create([
            'category_id' => $this->category1->id,
            'unit_id' => $unit->id,
            'code' => 'OBT-001',
            'name' => 'Paracetamol',
            'description' => 'Pereda demam',
            'stock' => 100,
            'min_stock' => 10,
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'expiration_date' => '2027-12-31'
        ]);

        $this->medicine2 = Medicine::create([
            'category_id' => $this->category2->id,
            'unit_id' => $unit->id,
            'code' => 'OBT-002',
            'name' => 'Amoxicillin',
            'description' => 'Mengobati infeksi bakteri',
            'stock' => 50,
            'min_stock' => 10,
            'purchase_price' => 2000,
            'selling_price' => 3000,
            'expiration_date' => '2027-12-31'
        ]);
    }

    public function test_guest_cannot_access_warga_medicines()
    {
        $response = $this->get(route('warga.medicines.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_warga_can_access_medicines_list()
    {
        $response = $this->actingAs($this->warga)
            ->get(route('warga.medicines.index'));

        $response->assertStatus(200);
        $response->assertSee('Paracetamol');
        $response->assertSee('Amoxicillin');
    }

    public function test_warga_can_search_medicines()
    {
        $response = $this->actingAs($this->warga)
            ->get(route('warga.medicines.index', ['search' => 'Paracetamol']));

        $response->assertStatus(200);
        $response->assertSee('Paracetamol');
        $response->assertDontSee('Amoxicillin');
    }

    public function test_warga_can_filter_medicines_by_category()
    {
        $response = $this->actingAs($this->warga)
            ->get(route('warga.medicines.index', ['category_id' => $this->category2->id]));

        $response->assertStatus(200);
        $response->assertSee('Amoxicillin');
        $response->assertDontSee('Paracetamol');
    }
}
