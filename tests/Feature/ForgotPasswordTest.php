<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
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
            'name'        => 'Forgot Pass Test User',
            'email'       => 'forgotpass_' . uniqid() . '@dtcs.hims',
            'password'    => 'OldPassword123!',
            'is_active'   => true,
            'employee_id' => 'FP-' . rand(10000, 99999),
        ]);

        $this->user->roles()->attach($role);
    }

    public function test_password_reset_notification_email_text_states_5_minutes_expiration(): void
    {
        Notification::fake();

        $this->post(route('password.email'), [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo(
            [$this->user],
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) {
                $mailData = $notification->toMail($this->user);
                $renderedHtml = $mailData->render();
                return str_contains($renderedHtml, '5 minutes');
            }
        );
    }

    public function test_password_reset_token_expires_after_5_minutes(): void
    {
        $token = Password::createToken($this->user);

        // 1. Within 4 minutes -> token valid
        Carbon::setTestNow(now()->addMinutes(4));
        $this->assertTrue(Password::getRepository()->exists($this->user, $token));

        // 2. After 6 minutes -> token expired
        Carbon::setTestNow(now()->addMinutes(2)); // total 6 minutes from creation
        $this->assertFalse(Password::getRepository()->exists($this->user, $token));

        // Reset Carbon test time
        Carbon::setTestNow();
    }

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
    }

    public function test_reset_password_link_can_be_requested_for_existing_user(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => $this->user->email,
        ]);

        $response->assertSessionHas('status', 'If an account exists for this email address, a password reset link has been sent.');

        Notification::assertSentTo(
            [$this->user],
            ResetPasswordNotification::class
        );

        // Verify password reset token exists in database for registered user
        $this->assertTrue(
            DB::table('password_reset_tokens')->where('email', $this->user->email)->exists(),
            'Password reset token should be generated for existing account.'
        );

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'Password Reset Requested',
            'result'  => ActivityLog::RESULT_SUCCESS,
        ]);
    }

    public function test_forgot_password_does_not_reveal_non_existent_user(): void
    {
        Notification::fake();

        $nonExistentEmail = 'nonexistent_' . uniqid() . '@dtcs.hims';

        $response = $this->post(route('password.email'), [
            'email' => $nonExistentEmail,
        ]);

        // Response must be identical to existing user response
        $response->assertSessionHas('status', 'If an account exists for this email address, a password reset link has been sent.');
        $response->assertSessionHasNoErrors();

        // No email sent
        Notification::assertNothingSent();

        // No password reset token created for non-existent account
        $this->assertFalse(
            DB::table('password_reset_tokens')->where('email', $nonExistentEmail)->exists(),
            'Password reset token must NOT be generated for non-existent account.'
        );
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $token = Password::createToken($this->user);

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $this->user->email]));

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
    }

    public function test_reset_password_screen_cannot_be_rendered_with_expired_token(): void
    {
        $token = Password::createToken($this->user);

        // Advance time by 6 minutes
        Carbon::setTestNow(now()->addMinutes(6));

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $this->user->email]));

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status', 'This password reset link has expired or is invalid. Please request a new password reset link.');

        Carbon::setTestNow();
    }

    public function test_password_cannot_be_reset_with_expired_token(): void
    {
        $token = Password::createToken($this->user);

        // Advance time by 6 minutes
        Carbon::setTestNow(now()->addMinutes(6));

        $response = $this->post(route('password.store'), [
            'token'                 => $token,
            'email'                 => $this->user->email,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status', 'This password reset link has expired or is invalid. Please request a new password reset link.');

        // Password should remain unchanged
        $this->user->refresh();
        $this->assertTrue(Hash::check('OldPassword123!', $this->user->password));

        Carbon::setTestNow();
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $token = Password::createToken($this->user);

        $response = $this->post(route('password.store'), [
            'token'                 => $token,
            'email'                 => $this->user->email,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Verify password updated
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewSecurePass123!', $this->user->password));

        // Verify active session was cleared
        $this->assertNull($this->user->active_session_id);

        // Verify audit log recorded
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action'  => 'Password Reset Completed',
            'result'  => ActivityLog::RESULT_SUCCESS,
        ]);
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $response = $this->post(route('password.store'), [
            'token'                 => 'invalid-token-123456',
            'email'                 => $this->user->email,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status', 'This password reset link has expired or is invalid. Please request a new password reset link.');

        // Password should remain unchanged
        $this->user->refresh();
        $this->assertTrue(Hash::check('OldPassword123!', $this->user->password));
    }

    public function test_after_password_reset_login_requires_new_password_and_otp(): void
    {
        // 1. Reset password
        $token = Password::createToken($this->user);
        $this->post(route('password.store'), [
            'token'                 => $token,
            'email'                 => $this->user->email,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        // 2. Sign in with old password -> fails
        $failResponse = $this->post(route('login'), [
            'email'    => $this->user->email,
            'password' => 'OldPassword123!',
        ]);
        $failResponse->assertSessionHasErrors();

        // 3. Sign in with new password -> redirects to OTP verify page
        $successResponse = $this->post(route('login'), [
            'email'    => $this->user->email,
            'password' => 'NewSecurePass123!',
        ]);
        $successResponse->assertRedirect(route('otp.verify'));
    }
}
