<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Feature tests for the mandatory admin comment on user archive (soft-delete) and restore.
 *
 * Covers:
 *  - Archive with valid comment           → succeeds, audit record created
 *  - Archive without comment              → 422 / redirect back with error
 *  - Archive with whitespace-only comment → 422 / redirect back with error
 *  - Restore with valid comment           → succeeds, audit record created
 *  - Restore without comment              → 422 / redirect back with error
 *  - Restore with whitespace-only comment → 422 / redirect back with error
 *  - Unauthorised roles cannot bypass
 */
class UserDeleteRestoreCommentTest extends TestCase
{
    use DatabaseTransactions;

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesExist();
    }

    private function ensureRolesExist(): void
    {
        $roles = [
            ['name' => 'System Administrator', 'slug' => 'admin',         'dashboard_route' => 'admin.dashboard'],
            ['name' => 'Doctor',               'slug' => 'doctor',        'dashboard_route' => 'doctor.dashboard'],
            ['name' => 'Medical Technologist', 'slug' => 'med-tech',      'dashboard_route' => 'lab.dashboard'],
            ['name' => 'Pharmacist',           'slug' => 'pharmacist',    'dashboard_route' => 'pharmacy.dashboard'],
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['slug' => $r['slug']], $r);
        }
    }

    private function createUserWithRole(string $slug): User
    {
        $user = User::create([
            'name'        => 'Test ' . ucfirst($slug),
            'email'       => $slug . '_' . uniqid() . '@test.hospital',
            'password'    => bcrypt('password'),
            'employee_id' => 'EMP-' . rand(10000, 99999),
            'department'  => 'Test',
            'is_active'   => true,
        ]);
        $role = Role::where('slug', $slug)->firstOrFail();
        $user->roles()->attach($role->id);
        return $user;
    }

    private function destroyRoute(User $target): string
    {
        return route('admin.users.destroy', $target);
    }

    private function restoreRoute(User $target): string
    {
        return route('admin.users.restore', $target->id);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Archive (destroy) — happy path
    // ──────────────────────────────────────────────────────────────────────────

    public function test_admin_can_archive_user_with_valid_comment(): void
    {
        $admin  = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('doctor');

        $this->actingAs($admin)
             ->delete($this->destroyRoute($target), [
                 'comment' => 'Account no longer required — employee left.',
             ])
             ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_archive_creates_audit_record_with_comment(): void
    {
        $admin   = $this->createUserWithRole('admin');
        $target  = $this->createUserWithRole('doctor');
        $comment = 'Archived for audit test — employee resigned.';

        $this->actingAs($admin)
             ->delete($this->destroyRoute($target), ['comment' => $comment]);

        $log = ActivityLog::where('action', 'User Archived')
                          ->where('user_id', $admin->id)
                          ->orderByDesc('id')
                          ->first();

        $this->assertNotNull($log, 'Audit record was not created.');
        $this->assertStringContainsString($target->email, $log->description);
        $this->assertStringContainsString($comment, $log->description);
        $this->assertSame(ActivityLog::SEVERITY_WARNING, $log->severity);
        $this->assertSame(ActivityLog::RESULT_SUCCESS, $log->result);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Archive — validation failures
    // ──────────────────────────────────────────────────────────────────────────

    public function test_archive_without_comment_is_rejected(): void
    {
        $admin  = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('doctor');

        $this->actingAs($admin)
             ->delete($this->destroyRoute($target), [])
             ->assertSessionHasErrors('comment');

        $this->assertNotSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_archive_with_whitespace_only_comment_is_rejected(): void
    {
        $admin  = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('doctor');

        $this->actingAs($admin)
             ->delete($this->destroyRoute($target), ['comment' => '   '])
             ->assertSessionHasErrors('comment');

        $this->assertNotSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_archive_own_account(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)
             ->delete($this->destroyRoute($admin), ['comment' => 'Self-delete attempt.'])
             ->assertStatus(403);

        $this->assertNotSoftDeleted('users', ['id' => $admin->id]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Restore — happy path
    // ──────────────────────────────────────────────────────────────────────────

    public function test_admin_can_restore_user_with_valid_comment(): void
    {
        $admin  = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('doctor');
        $target->delete(); // soft-delete first

        $this->actingAs($admin)
             ->post($this->restoreRoute($target), [
                 'comment' => 'User returned to duty — access reinstated.',
             ])
             ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id'         => $target->id,
            'deleted_at' => null,
        ]);
    }

    public function test_restore_creates_audit_record_with_comment(): void
    {
        $admin   = $this->createUserWithRole('admin');
        $target  = $this->createUserWithRole('doctor');
        $target->delete();
        $comment = 'Restored for audit test — returned to active assignment.';

        $this->actingAs($admin)
             ->post($this->restoreRoute($target), ['comment' => $comment]);

        $log = ActivityLog::where('action', 'User Restored')
                          ->where('user_id', $admin->id)
                          ->orderByDesc('id')
                          ->first();

        $this->assertNotNull($log, 'Audit record for User Restored was not created.');
        $this->assertStringContainsString($target->email, $log->description);
        $this->assertStringContainsString($comment, $log->description);
        $this->assertSame(ActivityLog::SEVERITY_WARNING, $log->severity);
        $this->assertSame(ActivityLog::RESULT_SUCCESS, $log->result);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Restore — validation failures
    // ──────────────────────────────────────────────────────────────────────────

    public function test_restore_without_comment_is_rejected(): void
    {
        $admin  = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('doctor');
        $target->delete();

        $this->actingAs($admin)
             ->post($this->restoreRoute($target), [])
             ->assertSessionHasErrors('comment');

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_restore_with_whitespace_only_comment_is_rejected(): void
    {
        $admin  = $this->createUserWithRole('admin');
        $target = $this->createUserWithRole('doctor');
        $target->delete();

        $this->actingAs($admin)
             ->post($this->restoreRoute($target), ['comment' => '     '])
             ->assertSessionHasErrors('comment');

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Authorisation — non-admin roles are blocked
    // ──────────────────────────────────────────────────────────────────────────

    public function test_non_admin_cannot_archive_user(): void
    {
        $doctor = $this->createUserWithRole('doctor');
        $target = $this->createUserWithRole('med-tech');

        $this->actingAs($doctor)
             ->delete($this->destroyRoute($target), [
                 'comment' => 'Attempting unauthorised archive.',
             ])
             ->assertStatus(403);

        $this->assertNotSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_non_admin_cannot_restore_user(): void
    {
        $doctor = $this->createUserWithRole('doctor');
        $target = $this->createUserWithRole('med-tech');
        $target->delete();

        $this->actingAs($doctor)
             ->post($this->restoreRoute($target), [
                 'comment' => 'Attempting unauthorised restore.',
             ])
             ->assertStatus(403);

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_unauthenticated_request_is_redirected(): void
    {
        $target = $this->createUserWithRole('doctor');

        $this->delete($this->destroyRoute($target), ['comment' => 'Unauthenticated.'])
             ->assertRedirect('/login');
    }
}
