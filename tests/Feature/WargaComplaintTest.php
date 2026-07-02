<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Child;
use App\Models\ImmunizationRecord;
use App\Models\ImmunizationVaccine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WargaComplaintTest extends TestCase
{
    use RefreshDatabase;

    protected $warga;
    protected $child;
    protected $vaccine;
    protected $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warga = User::create([
            'name' => 'Warga Uji',
            'email' => 'warga@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
        ]);

        $this->child = Child::create([
            'parent_id' => $this->warga->id,
            'name' => 'Anak Uji',
            'date_of_birth' => now()->subMonths(10)->toDateString(),
            'gender' => 'L',
            'nik' => '1234567890123456'
        ]);

        $this->vaccine = ImmunizationVaccine::create([
            'name' => 'BCG',
            'code' => 'BCG',
            'target_age_months' => 1
        ]);

        $this->record = ImmunizationRecord::create([
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'scheduled_date' => now()->subDays(10)->toDateString(),
            'administered_date' => now()->subDays(10)->toDateString(),
            'status' => 'completed',
            'vaccine_complaint' => 'Demam pasca suntik',
            'doctor_response' => 'Berikan paracetamol syrup'
        ]);
    }

    public function test_guest_cannot_access_warga_complaints()
    {
        $response = $this->get(route('warga.complaints.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_warga_can_access_complaints_list()
    {
        $response = $this->actingAs($this->warga)
            ->get(route('warga.complaints.index'));

        $response->assertStatus(200);
        $response->assertSee('Anak Uji');
        $response->assertSee('BCG');
        $response->assertSee('Demam pasca suntik');
        $response->assertSee('Berikan paracetamol syrup');
    }
}
