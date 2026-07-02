<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Child;
use App\Models\ImmunizationVaccine;
use App\Models\ImmunizationRecord;
use App\Models\ImmunizationReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImmunizationReminderTest extends TestCase
{
    use RefreshDatabase;

    protected $officer;
    protected $parent;
    protected $child;
    protected $vaccine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::create([
            'name' => 'Petugas Medis',
            'email' => 'officer@test.com',
            'password' => bcrypt('password'),
            'role' => 'petugas_medis'
        ]);

        $this->parent = User::create([
            'name' => 'Warga Parent',
            'email' => 'parent@test.com',
            'password' => bcrypt('password'),
            'role' => 'warga'
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
    }

    public function test_scheduling_immunization_more_than_5_days_away_creates_reminder_5_days_prior()
    {
        $this->actingAs($this->officer);

        $scheduledDate = now()->addDays(10)->format('Y-m-d');
        $expectedSendDate = now()->addDays(5)->format('Y-m-d') . ' 00:00:00';

        $response = $this->post(route('imunisasi.schedules.store'), [
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'status' => 'scheduled',
            'scheduled_date' => $scheduledDate,
        ]);

        $response->assertRedirect();
        
        $record = ImmunizationRecord::first();
        $this->assertNotNull($record);

        $this->assertDatabaseHas('immunization_reminders', [
            'record_id' => $record->id,
            'parent_id' => $this->parent->id,
            'send_date' => $expectedSendDate,
            'status' => 'pending',
            'channel' => 'dashboard',
        ]);
    }

    public function test_scheduling_immunization_less_than_5_days_away_creates_reminder_on_scheduled_date()
    {
        $this->actingAs($this->officer);

        $scheduledDate = now()->addDays(2)->format('Y-m-d');
        $expectedSendDate = $scheduledDate . ' 00:00:00';

        $response = $this->post(route('imunisasi.schedules.store'), [
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'status' => 'scheduled',
            'scheduled_date' => $scheduledDate,
        ]);

        $response->assertRedirect();
        
        $record = ImmunizationRecord::first();
        $this->assertNotNull($record);

        $this->assertDatabaseHas('immunization_reminders', [
            'record_id' => $record->id,
            'parent_id' => $this->parent->id,
            'send_date' => $expectedSendDate,
            'status' => 'pending',
        ]);
    }

    public function test_updating_schedule_to_completed_deletes_reminder()
    {
        $this->actingAs($this->officer);

        // First, create a scheduled immunization
        $record = ImmunizationRecord::create([
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'status' => 'scheduled',
            'scheduled_date' => now()->addDays(10)->format('Y-m-d'),
        ]);

        $reminder = ImmunizationReminder::create([
            'record_id' => $record->id,
            'parent_id' => $this->parent->id,
            'send_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'pending',
            'channel' => 'dashboard',
        ]);

        // Update to completed
        $response = $this->put(route('imunisasi.schedules.update', $record->id), [
            'status' => 'completed',
            'scheduled_date' => $record->scheduled_date->format('Y-m-d'),
            'administered_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('immunization_reminders', [
            'id' => $reminder->id,
        ]);
    }

    public function test_updating_completed_schedule_back_to_scheduled_creates_reminder()
    {
        $this->actingAs($this->officer);

        // Create completed immunization
        $record = ImmunizationRecord::create([
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'status' => 'completed',
            'scheduled_date' => now()->subDays(2)->format('Y-m-d'),
            'administered_date' => now()->subDays(2)->format('Y-m-d'),
        ]);

        // Update to scheduled (e.g. rescheduled for future)
        $futureScheduledDate = now()->addDays(7)->format('Y-m-d');
        $expectedSendDate = now()->addDays(2)->format('Y-m-d') . ' 00:00:00';

        $response = $this->put(route('imunisasi.schedules.update', $record->id), [
            'status' => 'scheduled',
            'scheduled_date' => $futureScheduledDate,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('immunization_reminders', [
            'record_id' => $record->id,
            'parent_id' => $this->parent->id,
            'send_date' => $expectedSendDate,
            'status' => 'pending',
        ]);
    }

    public function test_deleting_schedule_deletes_reminder()
    {
        $this->actingAs($this->officer);

        $record = ImmunizationRecord::create([
            'child_id' => $this->child->id,
            'vaccine_id' => $this->vaccine->id,
            'status' => 'scheduled',
            'scheduled_date' => now()->addDays(10)->format('Y-m-d'),
        ]);

        $reminder = ImmunizationReminder::create([
            'record_id' => $record->id,
            'parent_id' => $this->parent->id,
            'send_date' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'pending',
            'channel' => 'dashboard',
        ]);

        $response = $this->delete(route('imunisasi.schedules.destroy', $record->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('immunization_reminders', [
            'id' => $reminder->id,
        ]);
    }
}
