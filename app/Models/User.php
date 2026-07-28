<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User model extended with RBAC helpers and hospital-specific fields.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'department',
        'phone',
        'avatar',
        'is_active',
        'login_token',
        'failed_attempts',
        'locked_at',
        'lockout_until',
    ];

    #[Hidden(['password', 'remember_token'])]
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'locked_at'         => 'datetime',
            'lockout_until'     => 'datetime',
        ];
    }

    // ──────────────────────────── Relationships ────────────────────────────

    /**
     * Roles assigned to this user (many-to-many).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // Clinical relationships (as doctor / staff)
    public function labRequests(): HasMany           { return $this->hasMany(LabRequest::class, 'doctor_id'); }
    public function radiologyRequests(): HasMany     { return $this->hasMany(RadiologyRequest::class, 'doctor_id'); }
    public function prescriptions(): HasMany         { return $this->hasMany(Prescription::class, 'doctor_id'); }
    public function surgeryRequests(): HasMany       { return $this->hasMany(SurgeryRequest::class, 'doctor_id'); }
    public function dietRequests(): HasMany          { return $this->hasMany(DietRequest::class, 'doctor_id'); }
    public function activityLogs(): HasMany          { return $this->hasMany(ActivityLog::class); }

    // ──────────────────────────── RBAC Helpers ────────────────────────────

    /**
     * Check if the user has a specific role by slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    /**
     * Get the first role slug for the user (convenience).
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        return $this->roles()->first()?->slug;
    }

    /**
     * Get the first role name for display.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles()->first()?->name ?? 'No Role';
    }

    // ──────────────────────── Login Security Helpers ────────────────────────

    /**
     * Check if the account is permanently locked (requires admin unlock).
     */
    public function isLockedOut(): bool
    {
        return $this->locked_at !== null;
    }

    /**
     * Check if the account is in a timed cooldown period.
     */
    public function isCoolingDown(): bool
    {
        return $this->lockout_until !== null && $this->lockout_until->isFuture();
    }

    /**
     * Get remaining cooldown seconds.
     */
    public function getCooldownSeconds(): int
    {
        if (! $this->isCoolingDown()) {
            return 0;
        }

        return (int) now()->diffInSeconds($this->lockout_until, false);
    }

    /**
     * Reset all login attempt tracking (called on successful login or admin unlock).
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_at'       => null,
            'lockout_until'   => null,
        ]);
    }
}
