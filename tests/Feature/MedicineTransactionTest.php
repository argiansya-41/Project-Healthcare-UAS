<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineUnit;
use App\Models\MedicineTransaction;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MedicineTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $apoteker;
    protected $admin;
    protected $warga;
    protected $medicine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apoteker = User::create([
            'name' => 'Apoteker Uji',
            'email' => 'apoteker@test.com',
            'password' => bcrypt('password'),
            'role' => 'apoteker'
        ]);

        $this->admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->warga = User::create([
            'name' => 'Warga Uji',
            'email' => 'warga@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
        ]);

        $category = MedicineCategory::create([
            'name' => 'Kategori Uji',
            'description' => 'Kategori Deskripsi'
        ]);

        $unit = MedicineUnit::create([
            'name' => 'Tablet',
            'abbreviation' => 'Tab'
        ]);

        $this->medicine = Medicine::create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'code' => 'OBT-999',
            'name' => 'Obat Test',
            'description' => 'Deskripsi Obat',
            'stock' => 100,
            'min_stock' => 10,
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'expiration_date' => '2027-12-31'
        ]);
    }

    public function test_apoteker_can_delete_out_transaction_and_revert_stock()
    {
        $transaction = MedicineTransaction::create([
            'medicine_id' => $this->medicine->id,
            'type' => 'out',
            'quantity' => 20,
            'notes' => 'Test Keluar',
            'transaction_date' => '2026-06-18',
            'user_id' => $this->apoteker->id,
        ]);

        // Stock was 100. Let's assume the transaction had already reduced it or we test deletion logic directly:
        // Deleting OUT transaction should INCREMENT medicine stock by 20.
        $this->medicine->update(['stock' => 80]);

        $response = $this->actingAs($this->apoteker)
            ->delete(route('apotek.transactions.destroy', $transaction->id));

        $response->assertRedirect(route('apotek.transactions.index'));
        $response->assertSessionHas('success');
        
        $this->medicine->refresh();
        $this->assertEquals(100, $this->medicine->stock);
        $this->assertDatabaseMissing('medicine_transactions', ['id' => $transaction->id]);
    }

    public function test_apoteker_can_delete_in_transaction_and_revert_stock()
    {
        $transaction = MedicineTransaction::create([
            'medicine_id' => $this->medicine->id,
            'type' => 'in',
            'quantity' => 30,
            'notes' => 'Test Masuk',
            'transaction_date' => '2026-06-18',
            'user_id' => $this->apoteker->id,
        ]);

        // Stock was 100. Deleting IN transaction of 30 should DECREMENT stock by 30 (from 130 to 100).
        $this->medicine->update(['stock' => 130]);

        $response = $this->actingAs($this->apoteker)
            ->delete(route('apotek.transactions.destroy', $transaction->id));

        $response->assertRedirect(route('apotek.transactions.index'));
        $response->assertSessionHas('success');
        
        $this->medicine->refresh();
        $this->assertEquals(100, $this->medicine->stock);
        $this->assertDatabaseMissing('medicine_transactions', ['id' => $transaction->id]);
    }

    public function test_deleting_in_transaction_fails_if_insufficient_stock()
    {
        $transaction = MedicineTransaction::create([
            'medicine_id' => $this->medicine->id,
            'type' => 'in',
            'quantity' => 50,
            'notes' => 'Test Masuk Besar',
            'transaction_date' => '2026-06-18',
            'user_id' => $this->apoteker->id,
        ]);

        // Stock is currently 30 (which is less than transaction quantity 50).
        // Deleting this transaction would make stock -20, so it should be rejected.
        $this->medicine->update(['stock' => 30]);

        $response = $this->actingAs($this->apoteker)
            ->delete(route('apotek.transactions.destroy', $transaction->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->medicine->refresh();
        $this->assertEquals(30, $this->medicine->stock);
        $this->assertDatabaseHas('medicine_transactions', ['id' => $transaction->id]);
    }

    public function test_apoteker_can_clear_all_transactions()
    {
        MedicineTransaction::create([
            'medicine_id' => $this->medicine->id,
            'type' => 'in',
            'quantity' => 10,
            'notes' => 'notes 1',
            'transaction_date' => '2026-06-18',
            'user_id' => $this->apoteker->id,
        ]);

        MedicineTransaction::create([
            'medicine_id' => $this->medicine->id,
            'type' => 'out',
            'quantity' => 5,
            'notes' => 'notes 2',
            'transaction_date' => '2026-06-18',
            'user_id' => $this->apoteker->id,
        ]);

        $this->assertEquals(2, MedicineTransaction::count());

        $response = $this->actingAs($this->apoteker)
            ->delete(route('apotek.transactions.clearAll'));

        $response->assertRedirect(route('apotek.transactions.index'));
        $response->assertSessionHas('success');
        $this->assertEquals(0, MedicineTransaction::count());
    }

    public function test_warga_cannot_delete_or_clear_transactions()
    {
        $transaction = MedicineTransaction::create([
            'medicine_id' => $this->medicine->id,
            'type' => 'in',
            'quantity' => 10,
            'notes' => 'notes 1',
            'transaction_date' => '2026-06-18',
            'user_id' => $this->apoteker->id,
        ]);

        // Try deleting individual
        $response1 = $this->actingAs($this->warga)
            ->delete(route('apotek.transactions.destroy', $transaction->id));
        $response1->assertStatus(403);

        // Try clearing all
        $response2 = $this->actingAs($this->warga)
            ->delete(route('apotek.transactions.clearAll'));
        $response2->assertStatus(403);

        $this->assertEquals(1, MedicineTransaction::count());
    }
}
