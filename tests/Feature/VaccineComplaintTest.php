<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Child;
use App\Models\ImmunizationVaccine;
use App\Models\ImmunizationRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VaccineComplaintTest extends TestCase
{
    use RefreshDatabase;

    protected $parent;
    protected $otherParent;
    protected $doctor;
    protected $child;
    protected $vaccine;
    protected $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parent = User::create([
            'name' => 'Warga Parent',
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
        ]);

        $this->otherParent = User::create([
            'name' => 'Other Parent',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
        ]);

        $this->doctor = User::create([
            'name' => 'Dr. Andi',
            'email' => 'doctor@test.com',
            'password' => bcrypt('password'),
            'role' => 'petugas_medis'
        ]);

        $this->child = Child::create([
            'parent_id' => $this->parent->id,
            'name' => 'Baby Warga',
            'gender' => 'L',
            'date_of_birth' => now()->subMonths(6)
        ]);

        $this->vaccine = ImmunizationVaccine::create([
            'name' => 'Polio',
            'code' => 'POLIO1',
            'target_age_months' => 2
        ]);

        $this->record = ImmunizationRecord::create([
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'status' => 'completed',
            'scheduled_date' => now()->subDays(5),
            'administered_date' => now()->subDays(5),
            'batch_number' => 'B123'
        ]);
    }

    public function test_parent_can_report_vaccine_complaint()
    {
        $this->actingAs($this->parent);

        $response = $this->post(route('warga.complaint.store', $this->record->id), [
            'vaccine_complaint' => 'Anak demam tinggi setelah vaksin'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('immunization_records', [
            'id' => $this->record->id,
            'vaccine_complaint' => 'Anak demam tinggi setelah vaksin',
            'doctor_response' => null
        ]);
    }

    public function test_parent_cannot_report_complaint_for_unowned_child()
    {
        $this->actingAs($this->otherParent);

        $response = $this->post(route('warga.complaint.store', $this->record->id), [
            'vaccine_complaint' => 'Bukan anak saya'
        ]);

        $response->assertStatus(403);
    }

    public function test_doctor_can_respond_to_vaccine_complaint()
    {
        // Set complaint first
        $this->record->update([
            'vaccine_complaint' => 'Anak demam'
        ]);

        $this->actingAs($this->doctor);

        $response = $this->post(route('dokter.complaints.respond', $this->record->id), [
            'doctor_response' => 'Berikan obat penurun panas dan kompres hangat'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('immunization_records', [
            'id' => $this->record->id,
            'doctor_response' => 'Berikan obat penurun panas dan kompres hangat'
        ]);
    }
}
