<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SessionLockTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name'            => 'System Administrator',
                'slug'            => 'admin',
                'dashboard_route' => 'admin.dashboard',
            ]
        );

        $this->user = User::create([
            'name'        => 'Session Lock Test User',
            'email'       => 'locktest_' . uniqid() . '@dtcs.hims',
            'password'    => 'HospitalPass123!',
            'is_active'   => true,
            'employee_id' => 'LOCK-' . rand(10000, 99999),
        ]);

        $this->user->roles()->attach($role);
    }

    public function test_authenticated_user_can_lock_session(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('session.lock'));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(session('hims_session_locked'));
        $this->assertEquals(0, session('hims_unlock_attempts'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'Session Locked',
            'result'  => ActivityLog::RESULT_SUCCESS,
        ]);
    }

    public function test_locked_session_blocks_access_to_protected_routes(): void
    {
        $this->actingAs($this->user);
        session(['hims_session_locked' => true]);

        // Standard Web GET request should redirect to locked page
        $webResponse = $this->get(route('dashboard'));
        $webResponse->assertRedirect(route('session.locked'));

        // AJAX / JSON request should return 423 Locked
        $jsonResponse = $this->getJson(route('notifications.unread-count'));
        $jsonResponse->assertStatus(423)
            ->assertJson([
                'locked' => true,
            ]);
    }

    public function test_locked_session_can_be_unlocked_with_correct_password(): void
    {
        $this->actingAs($this->user);
        session([
            'hims_session_locked'  => true,
            'hims_unlock_attempts' => 1,
        ]);

        $response = $this->postJson(route('session.unlock'), [
            'password' => 'HospitalPass123!',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertFalse(session()->has('hims_session_locked'));
        $this->assertFalse(session()->has('hims_unlock_attempts'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'Session Unlocked',
            'result'  => ActivityLog::RESULT_SUCCESS,
        ]);
    }

    public function test_unlock_attempt_with_wrong_password_increments_attempts(): void
    {
        $this->actingAs($this->user);
        session(['hims_session_locked' => true]);

        // Attempt 1: Wrong password
        $response1 = $this->postJson(route('session.unlock'), [
            'password' => 'WrongPass123!',
        ]);

        $response1->assertStatus(422)
            ->assertJson([
                'success'            => false,
                'attempts_remaining' => 2,
            ]);

        $this->assertEquals(1, session('hims_unlock_attempts'));

        // Attempt 2: Wrong password
        $response2 = $this->postJson(route('session.unlock'), [
            'password' => 'WrongPass123!',
        ]);

        $response2->assertStatus(422)
            ->assertJson([
                'success'            => false,
                'attempts_remaining' => 1,
            ]);

        $this->assertEquals(2, session('hims_unlock_attempts'));
    }

    public function test_three_wrong_password_attempts_forces_full_logout(): void
    {
        $this->actingAs($this->user);
        session([
            'hims_session_locked'  => true,
            'hims_unlock_attempts' => 2,
        ]);

        // Attempt 3: Wrong password
        $response = $this->postJson(route('session.unlock'), [
            'password' => 'WrongPass123!',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success'               => false,
                'max_attempts_exceeded' => true,
            ]);

        $this->assertFalse(Auth::check());

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'Session Logout After Failed Unlock',
            'result'  => ActivityLog::RESULT_BLOCKED,
        ]);
    }

    public function test_user_can_sign_out_directly_from_lock_screen(): void
    {
        $this->actingAs($this->user);
        session(['hims_session_locked' => true]);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::check());
    }

    public function test_single_session_replacement_invalidates_locked_session(): void
    {
        // Establish initial session for user on Device A
        $this->actingAs($this->user);
        $sessionId = session()->getId();
        $this->user->update(['active_session_id' => $sessionId]);

        session(['hims_session_locked' => true]);

        // Device B logs in with same user credentials -> active_session_id updates to Device B's session ID
        $this->user->update(['active_session_id' => 'device_b_session_id_xyz']);

        // Device A attempts any request while locked
        $response = $this->get(route('dashboard'));

        // EnsureSingleSessionActive middleware intercepts and redirects to login with session_replaced
        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::check());
    }
}
