<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate with progressive lockout.
     *
     * Attempt 1 → warning message
     * Attempt 2 → 30-second cooldown
     * Attempt 3 → 5-minute cooldown
     * Attempt 4 → permanent account lock (admin must unlock)
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        // Look up the user (including soft-deleted check is handled by is_active)
        $user = User::where('email', $this->input('email'))->first();

        // ── Check if permanently locked ──────────────────────────────────
        if ($user && $user->isLockedOut()) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been locked due to multiple failed login attempts. Please contact your System Administrator to unlock your account.',
            ]);
        }

        // ── Check if in cooldown period ──────────────────────────────────
        if ($user && $user->isCoolingDown()) {
            $seconds = $user->getCooldownSeconds();

            session()->flash('lockout_seconds', $seconds);

            throw ValidationException::withMessages([
                'email' => "Too many failed attempts. Please wait {$seconds} seconds before trying again.",
            ]);
        }

        // ── Attempt authentication ───────────────────────────────────────
        if (! Auth::attempt(
            array_merge($this->only('email', 'password'), ['is_active' => true]),
            $this->boolean('remember')
        )) {
            // Auth failed — escalate lockout for known users
            if ($user) {
                $this->handleFailedAttempt($user);
            } else {
                // Unknown user — generic message, no escalation
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }

        // Success — attempts are reset in the controller after token generation
    }

    /**
     * Handle a failed login attempt with progressive penalties.
     *
     * @throws ValidationException
     */
    protected function handleFailedAttempt(User $user): void
    {
        $attempts = $user->failed_attempts + 1;
        $updateData = ['failed_attempts' => $attempts];

        switch ($attempts) {
            case 1:
                $user->update($updateData);
                throw ValidationException::withMessages([
                    'email' => 'Invalid credentials. Be careful, your account will be locked after multiple failed attempts.',
                ]);

            case 2:
                $updateData['lockout_until'] = now()->addSeconds(30);
                $user->update($updateData);
                session()->flash('lockout_seconds', 30);
                throw ValidationException::withMessages([
                    'email' => 'Invalid credentials. Your account is temporarily locked for 30 seconds.',
                ]);

            case 3:
                $updateData['lockout_until'] = now()->addMinutes(5);
                $user->update($updateData);
                session()->flash('lockout_seconds', 300);
                throw ValidationException::withMessages([
                    'email' => 'Invalid credentials. Your account is temporarily locked for 5 minutes.',
                ]);

            default: // 4th attempt and beyond
                $updateData['locked_at']  = now();
                $updateData['is_active']  = false;
                $user->update($updateData);
                throw ValidationException::withMessages([
                    'email' => 'Your account has been locked due to multiple failed login attempts. Please contact your System Administrator to unlock your account.',
                ]);
        }
    }
}
