<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_audit_log_entry(): void
    {
        // 1. Create an Admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // 2. Create a dummy activity log
        $logToDelete = ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'login',
            'description' => 'Dummy test log to be deleted',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        // Verify it exists in the database
        $this->assertDatabaseHas('activity_logs', [
            'id' => $logToDelete->id,
        ]);

        // 3. Act as the Admin and send DELETE request
        $response = $this->actingAs($admin)
                         ->delete(route('admin.logs.destroy', $logToDelete->id));

        // 4. Assert redirection and status
        $response->assertStatus(302);
        
        // 5. Assert the original log is deleted from the database
        $this->assertDatabaseMissing('activity_logs', [
            'id' => $logToDelete->id,
        ]);

        // 6. Assert a new log entry tracking this deletion was added
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'delete_log',
            'description' => 'Menghapus log aktivitas: Dummy test log to be deleted',
            'user_id' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_delete_audit_log_entry(): void
    {
        // 1. Create a non-admin user (e.g., petugas_medis)
        $user = User::factory()->create([
            'role' => 'petugas_medis',
        ]);

        // 2. Create a dummy activity log
        $log = ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Test log that should not be deleted',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        // 3. Act as the non-admin and send DELETE request
        $response = $this->actingAs($user)
                         ->delete(route('admin.logs.destroy', $log->id));

        // 4. Assert forbidden status (due to role middleware)
        $response->assertStatus(403);

        // 5. Assert the log is still present in the database
        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
        ]);
    }

    public function test_admin_can_clear_all_audit_logs(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'login',
            'description' => 'Test log 1',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'logout',
            'description' => 'Test log 2',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $this->assertEquals(2, ActivityLog::count());

        $response = $this->actingAs($admin)
                         ->delete(route('admin.logs.clearAll'));

        $response->assertStatus(302);
        
        // After clearing, there should be exactly 1 log, which is the clear_logs audit entry
        $this->assertEquals(1, ActivityLog::count());
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'clear_logs',
            'description' => 'Membersihkan semua log aktivitas sistem',
            'user_id' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_clear_all_audit_logs(): void
    {
        $user = User::factory()->create([
            'role' => 'petugas_medis',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'login',
            'description' => 'Test log 1',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $response = $this->actingAs($user)
                         ->delete(route('admin.logs.clearAll'));

        $response->assertStatus(403);
        $this->assertEquals(1, ActivityLog::count());
    }
}
