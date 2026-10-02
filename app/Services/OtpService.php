<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

/**
 * OtpService — Central business logic for Email OTP Two-Factor Authentication.
 *
 * SECURITY INVARIANTS:
 * - Plaintext OTPs are generated using PHP's cryptographically secure random_int().
 * - Plaintext OTPs are NEVER stored — only their bcrypt hash is persisted.
 * - This service returns the plaintext OTP to the caller (controller) for email delivery only.
 * - Verification uses Hash::check() against the stored hash.
 */
class OtpService
{
    /**
     * Generate a new 6-digit OTP for the given user and purpose.
     *
     * Deletes any previous pending (non-verified) OTP for the same user+purpose
     * before creating a fresh record. This prevents the previous OTP from being
     * used after a resend.
     *
     * @param  User   $user
     * @param  string $purpose  e.g. 'login'
     * @return string           The plaintext OTP — caller must send via email and discard.
     */
    public function generate(User $user, string $purpose = 'login'): string
    {
        // Invalidate any existing pending OTP for this user+purpose
        $this->invalidatePrevious($user, $purpose);

        // Generate a cryptographically secure 6-digit OTP (leading zeros allowed)
        $plainOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $expiresMinutes = config('otp.expires_minutes', 3);

        OtpVerification::create([
            'user_id'      => $user->id,
            'purpose'      => $purpose,
            'otp_hash'     => Hash::make($plainOtp),
            'attempts'     => 0,
            'last_sent_at' => now(),
            'expires_at'   => now()->addMinutes($expiresMinutes),
            'verified_at'  => null,
        ]);

        // IMPORTANT: Return plaintext OTP to caller for email delivery ONLY.
        // It must never be stored, logged, or placed in any session or URL.
        return $plainOtp;
    }

    /**
     * Verify a plaintext OTP submitted by the user.
     *
     * Returns one of: 'verified', 'invalid', 'expired', 'exhausted', 'not_found'
     *
     * On success, the record is marked as verified (preventing replay).
     * On failure, the attempt counter is incremented.
     *
     * @param  User   $user
     * @param  string $plainOtp  The OTP the user submitted
     * @param  string $purpose
     * @return string  Result code
     */
    public function verify(User $user, string $plainOtp, string $purpose = 'login'): string
    {
        $record = OtpVerification::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->orderByDesc('created_at')
            ->first();

        if (! $record) {
            return 'not_found';
        }

        if ($record->isExpired()) {
            $this->logOtpEvent($user, 'OTP Expired', ActivityLog::SEVERITY_WARNING, ActivityLog::RESULT_FAILED,
                "OTP for account [{$user->email}] expired before verification.");
            return 'expired';
        }

        if ($record->isExhausted()) {
            return 'exhausted';
        }

        // Check the hash — timing-safe via Hash::check() (bcrypt)
        if (! Hash::check($plainOtp, $record->otp_hash)) {
            // Increment attempt counter
            $record->increment('attempts');

            $this->logOtpEvent($user, 'OTP Failed', ActivityLog::SEVERITY_WARNING, ActivityLog::RESULT_FAILED,
                "Failed OTP attempt (#{$record->attempts}) for account [{$user->email}].");

            // Re-check exhaustion after incrementing
            if ($record->fresh()->isExhausted()) {
                return 'exhausted';
            }

            return 'invalid';
        }

        // ── Success: mark as verified (prevents replay) ──
        $record->update(['verified_at' => now()]);

        $this->logOtpEvent($user, 'OTP Verified', ActivityLog::SEVERITY_INFO, ActivityLog::RESULT_SUCCESS,
            "OTP successfully verified for account [{$user->email}].");

        return 'verified';
    }

    /**
     * Check whether the user can request a resend (60-second cooldown).
     *
     * @param  User   $user
     * @param  string $purpose
     * @return bool  true if resend is allowed
     */
    public function canResend(User $user, string $purpose = 'login'): bool
    {
        $cooldownSeconds = config('otp.resend_cooldown_sec', 60);

        $latest = OtpVerification::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->orderByDesc('created_at')
            ->value('last_sent_at');

        if (! $latest) {
            return true;
        }

        return now()->diffInSeconds(\Carbon\Carbon::parse($latest), false) >= $cooldownSeconds;
    }

    /**
     * Remaining cooldown seconds before the user can resend.
     * Returns 0 if they can resend now.
     */
    public function resendCooldownRemaining(User $user, string $purpose = 'login'): int
    {
        $cooldownSeconds = config('otp.resend_cooldown_sec', 60);

        $latest = OtpVerification::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->orderByDesc('created_at')
            ->value('last_sent_at');

        if (! $latest) {
            return 0;
        }

        $elapsed = (int) now()->diffInSeconds(\Carbon\Carbon::parse($latest), false);
        $remaining = $cooldownSeconds - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Check whether the user has exceeded the OTP send rate limit.
     * Limit: max 5 OTPs in any 15-minute window.
     *
     * @param  User   $user
     * @param  string $purpose
     * @return bool  true if rate-limited (should block)
     */
    public function isRateLimited(User $user, string $purpose = 'login'): bool
    {
        $max     = config('otp.rate_limit_max', 5);
        $minutes = config('otp.rate_limit_minutes', 15);

        $count = OtpVerification::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();

        return $count >= $max;
    }

    /**
     * Delete all pending (non-verified) OTP records for a user+purpose.
     * Called before generating a new OTP so that old codes cannot be replayed.
     */
    public function invalidatePrevious(User $user, string $purpose = 'login'): void
    {
        OtpVerification::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->update(['expires_at' => now()]);
    }

    // ─────────────────────────────── Private Helpers ─────────────────────────

    /**
     * Log an OTP-related audit event using the existing ActivityLog system.
     * NEVER logs the plaintext OTP or its hash.
     */
    private function logOtpEvent(
        User   $user,
        string $action,
        string $severity,
        string $result,
        string $description
    ): void {
        ActivityLog::create([
            'user_id'     => $user->id,
            'action'      => $action,
            'module'      => ActivityLog::MODULE_AUTH,
            'severity'    => $severity,
            'result'      => $result,
            'description' => $description,
            'ip_address'  => Request::ip(),
            'logged_at'   => now(),
        ]);
    }
}
