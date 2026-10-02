<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OTP Verification record — one per pending login verification attempt.
 *
 * SECURITY: The otp_hash column stores a bcrypt hash of the plaintext OTP.
 * The plaintext OTP is NEVER stored in this model, the database, logs, or sessions.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $purpose
 * @property string      $otp_hash
 * @property int         $attempts
 * @property \Carbon\Carbon|null $last_sent_at
 * @property \Carbon\Carbon      $expires_at
 * @property \Carbon\Carbon|null $verified_at
 */
class OtpVerification extends Model
{
    protected $fillable = [
        'user_id',
        'purpose',
        'otp_hash',
        'attempts',
        'last_sent_at',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
        'expires_at'   => 'datetime',
        'verified_at'  => 'datetime',
    ];

    // ─────────────────────────────────────── Relationships ──────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─────────────────────────────────────── State Helpers ──────────────────

    /**
     * Has this OTP record passed its expiry timestamp?
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Has this OTP already been successfully verified?
     * A verified OTP cannot be used again (replay prevention).
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Has this OTP exhausted all allowed attempts?
     * Once exhausted, the user must request a new OTP.
     */
    public function isExhausted(): bool
    {
        return $this->attempts >= config('otp.max_attempts', 5);
    }

    /**
     * Is this OTP still usable? (not expired, not verified, not exhausted)
     */
    public function isUsable(): bool
    {
        return ! $this->isExpired()
            && ! $this->isVerified()
            && ! $this->isExhausted();
    }

    /**
     * Remaining seconds until expiry (0 if already expired).
     */
    public function secondsUntilExpiry(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) now()->diffInSeconds($this->expires_at);
    }
}
