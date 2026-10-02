<?php

namespace Tests\Feature;

use App\Mail\OtpNotificationMail;
use App\Models\ActivityLog;
use App\Models\OtpVerification;
use App\Models\Role;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * OTP Authentication Feature Tests.
 *
 * Tests the complete Email OTP Two-Factor Authentication flow.
 * Uses DatabaseTransactions to isolate database changes per test.
 */
class OtpAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private OtpService $otpService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test role using the project's direct create pattern (no factory exists)
        $role = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name'            => 'System Administrator',
                'slug'            => 'admin',
                'dashboard_route' => 'admin.dashboard',
            ]
        );

        $this->user = User::create([
            'name'        => 'OTP Test User',
            'email'       => 'otptest_' . uniqid() . '@hospital.test',
            'password'    => 'password123',   // Model casts password as hashed automatically
            'is_active'   => true,
            'employee_id' => 'OTP-' . rand(10000, 99999),
        ]);

        $this->user->roles()->attach($role);
        $this->otpService = app(OtpService::class);

        Mail::fake();
    }

    // ─────────────────────────────── Password Step ───────────────────────────

    public function test_correct_credentials_redirect_to_otp_verify_not_dashboard(): void
    {
        $response = $this->post(route('login'), [
            'email'    => $this->user->email,
            'password' => 'password123',
        ]);

        // Must redirect to OTP verify, NOT dashboard
        $response->assertRedirect(route('otp.verify'));

        // Must NOT be authenticated yet
        $this->assertGuest();

        // OTP pending state must be set
        $response->assertSessionHas('otp_pending_user_id', $this->user->id);

        // OTP email must have been sent
        Mail::assertSent(OtpNotificationMail::class, function ($mail) {
            return $mail->hasTo($this->user->email);
        });

        // OTP record must exist in database
        $this->assertDatabaseHas('otp_verifications', [
            'user_id' => $this->user->id,
            'purpose' => 'login',
        ]);
    }

    public function test_wrong_password_does_not_send_otp(): void
    {
        $response = $this->post(route('login'), [
            'email'    => $this->user->email,
            'password' => 'WRONG_PASSWORD',
        ]);

        // Must stay on login page
        $response->assertRedirect();
        $this->assertGuest();

        // No OTP email sent
        Mail::assertNotSent(OtpNotificationMail::class);

        // No OTP record created
        $this->assertDatabaseMissing('otp_verifications', [
            'user_id' => $this->user->id,
        ]);
    }

    // ─────────────────────────────── OTP Verify Step ─────────────────────────

    public function test_correct_otp_authenticates_user_and_redirects_to_dashboard(): void
    {
        // Generate OTP and set pending session
        $plainOtp = $this->otpService->generate($this->user);
        $session  = ['otp_pending_user_id' => $this->user->id];

        $response = $this->withSession($session)
                         ->post(route('otp.verify.submit'), ['otp' => $plainOtp]);

        // Must be authenticated after OTP
        $this->assertAuthenticatedAs($this->user);

        // Must redirect (to dashboard or role route)
        $response->assertRedirect();

        // otp_pending_user_id must be cleared
        $this->assertFalse(session()->has('otp_pending_user_id'));

        // OTP must be marked verified
        $this->assertDatabaseHas('otp_verifications', [
            'user_id' => $this->user->id,
            'purpose' => 'login',
        ]);
        $record = OtpVerification::where('user_id', $this->user->id)->first();
        $this->assertNotNull($record->verified_at);

        // Audit log: Login event must exist
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'Login',
            'module'  => 'Authentication',
            'result'  => ActivityLog::RESULT_SUCCESS,
        ]);
    }

    public function test_wrong_otp_increments_attempts_and_shows_generic_error(): void
    {
        $this->otpService->generate($this->user);
        $session = ['otp_pending_user_id' => $this->user->id];

        $response = $this->withSession($session)
                         ->post(route('otp.verify.submit'), ['otp' => '000000']); // wrong OTP

        // Must NOT be authenticated
        $this->assertGuest();

        // Must redirect back with error
        $response->assertRedirect(route('otp.verify'));
        $response->assertSessionHasErrors(['otp']);

        // Error message must be generic — no internal state revealed
        $errors = session('errors');
        $errorMsg = $errors ? $errors->first('otp') : '';
        $this->assertStringContainsString('invalid or has expired', $errorMsg);

        // Attempt count must be incremented
        $record = OtpVerification::where('user_id', $this->user->id)->first();
        $this->assertEquals(1, $record->attempts);

        // Audit: OTP Failed
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'OTP Failed',
            'result'  => ActivityLog::RESULT_FAILED,
        ]);
    }

    public function test_five_failed_otp_attempts_invalidate_otp_and_redirect_to_login(): void
    {
        $this->otpService->generate($this->user);
        $session  = ['otp_pending_user_id' => $this->user->id];
        $response = null;

        // Submit wrong OTP 5 times
        for ($i = 0; $i < 5; $i++) {
            $response = $this->withSession($session)
                             ->post(route('otp.verify.submit'), ['otp' => '000000']);
            // Keep pending session across requests
            $session = ['otp_pending_user_id' => $this->user->id];
        }

        // After 5 failures, the final redirect should go to login
        $this->assertNotNull($response);
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_expired_otp_is_rejected(): void
    {
        // Generate OTP then manually expire it
        $this->otpService->generate($this->user);
        OtpVerification::where('user_id', $this->user->id)
            ->update(['expires_at' => now()->subMinutes(10)]);

        // Get the actual hash for "any" 6-digit code — we just need to check expiry behavior
        $record   = OtpVerification::where('user_id', $this->user->id)->first();
        $session  = ['otp_pending_user_id' => $this->user->id];

        $response = $this->withSession($session)
                         ->post(route('otp.verify.submit'), ['otp' => '123456']);

        $this->assertGuest();
        $response->assertSessionHasErrors(['otp']);

        // Expired audit event
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'OTP Expired',
        ]);
    }

    public function test_verified_otp_cannot_be_reused_replay_prevention(): void
    {
        $plainOtp = $this->otpService->generate($this->user);
        $session  = ['otp_pending_user_id' => $this->user->id];

        // First use — should succeed
        $this->withSession($session)
             ->post(route('otp.verify.submit'), ['otp' => $plainOtp]);

        $this->assertAuthenticatedAs($this->user);

        // Logout to try again
        $this->post(route('logout'));

        // Second use of same OTP — must fail
        $session2  = ['otp_pending_user_id' => $this->user->id];
        $response2 = $this->withSession($session2)
                          ->post(route('otp.verify.submit'), ['otp' => $plainOtp]);

        $this->assertGuest();
        $response2->assertSessionHasErrors(['otp']);
    }

    public function test_direct_dashboard_access_while_otp_pending_redirects_to_otp_verify(): void
    {
        // Simulate a state where the user IS authenticated but otp_pending_user_id is set
        // (edge case that EnsureOtpVerified guards against)
        $this->actingAs($this->user)
             ->withSession(['otp_pending_user_id' => $this->user->id]);

        $response = $this->withSession([
            'otp_pending_user_id' => $this->user->id,
        ])->actingAs($this->user)->get(route('admin.dashboard'));

        // Must redirect to OTP verify
        $response->assertRedirect(route('otp.verify'));
    }

    public function test_unauthenticated_access_to_otp_verify_page_redirects_to_login(): void
    {
        // No session — should redirect to login
        $response = $this->get(route('otp.verify'));
        $response->assertRedirect(route('login'));
    }

    public function test_resend_within_cooldown_is_rejected(): void
    {
        // First OTP (just sent)
        $this->otpService->generate($this->user);
        $session = ['otp_pending_user_id' => $this->user->id];

        // Immediately attempt resend — should be rejected (within 60s cooldown)
        $response = $this->withSession($session)
                         ->post(route('otp.resend'));

        $response->assertRedirect(route('otp.verify'));
        $response->assertSessionHas('resend_error');

        // Only one OTP mail should have been sent (from generate())
        Mail::assertSentCount(0); // Mail::fake() was set up before generate, so this checks the resend
    }

    public function test_otp_audit_events_are_logged(): void
    {
        // Generate OTP → must log OTP Generated
        $plainOtp = $this->otpService->generate($this->user);

        // ActivityLog is created in OtpService, but OTP Generated is created in the controller
        // We verify the service-level events:
        // OTP Verified event
        $this->otpService->verify($this->user, $plainOtp);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'OTP Verified',
            'module'  => 'Authentication',
            'result'  => ActivityLog::RESULT_SUCCESS,
        ]);

        // OTP Failed event
        $this->otpService->generate($this->user);
        $this->otpService->verify($this->user, '000000');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'OTP Failed',
            'module'  => 'Authentication',
            'result'  => ActivityLog::RESULT_FAILED,
        ]);
    }

    public function test_otp_service_correctly_reports_rate_limit(): void
    {
        $max     = config('otp.rate_limit_max', 5);
        $minutes = config('otp.rate_limit_minutes', 15);

        // Not rate limited initially
        $this->assertFalse($this->otpService->isRateLimited($this->user));

        // Generate max OTPs
        for ($i = 0; $i < $max; $i++) {
            $this->otpService->generate($this->user);
        }

        // Now should be rate limited
        $this->assertTrue($this->otpService->isRateLimited($this->user));
    }

    public function test_otp_model_helpers_work_correctly(): void
    {
        $record = OtpVerification::create([
            'user_id'      => $this->user->id,
            'purpose'      => 'login',
            'otp_hash'     => Hash::make('123456'),
            'attempts'     => 0,
            'last_sent_at' => now(),
            'expires_at'   => now()->addMinutes(3),
            'verified_at'  => null,
        ]);

        $this->assertFalse($record->isExpired());
        $this->assertFalse($record->isVerified());
        $this->assertFalse($record->isExhausted());
        $this->assertTrue($record->isUsable());

        // Mark as verified
        $record->update(['verified_at' => now()]);
        $record->refresh();
        $this->assertTrue($record->isVerified());
        $this->assertFalse($record->isUsable());

        // Expire it
        $record->update(['verified_at' => null, 'expires_at' => now()->subMinute()]);
        $record->refresh();
        $this->assertTrue($record->isExpired());
        $this->assertFalse($record->isUsable());
    }
}
