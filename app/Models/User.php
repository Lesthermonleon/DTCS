<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
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
    use Notifiable, SoftDeletes;

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

    // Clinical & System relationships
    public function labRequests(): HasMany           { return $this->hasMany(LabRequest::class, 'doctor_id'); }
    public function radiologyRequests(): HasMany     { return $this->hasMany(RadiologyRequest::class, 'doctor_id'); }
    public function prescriptions(): HasMany         { return $this->hasMany(Prescription::class, 'doctor_id'); }
    public function surgeryRequests(): HasMany       { return $this->hasMany(SurgeryRequest::class, 'doctor_id'); }
    public function dietRequests(): HasMany          { return $this->hasMany(DietRequest::class, 'doctor_id'); }

    // Aliases for report withCount (same FK, distinct names for clarity)
    public function labRequestsAsDoctor(): HasMany         { return $this->hasMany(LabRequest::class, 'doctor_id'); }
    public function radiologyRequestsAsDoctor(): HasMany   { return $this->hasMany(RadiologyRequest::class, 'doctor_id'); }
    public function prescriptionsAsDoctor(): HasMany       { return $this->hasMany(Prescription::class, 'doctor_id'); }
    public function surgeryRequestsAsDoctor(): HasMany     { return $this->hasMany(SurgeryRequest::class, 'doctor_id'); }

    public function activityLogs(): HasMany          { return $this->hasMany(ActivityLog::class); }
    public function notifications(): HasMany         { return $this->hasMany(Notification::class); }
    public function conversations(): BelongsToMany   { return $this->belongsToMany(Conversation::class, 'conversation_participants')->withPivot('last_read_at')->withTimestamps(); }

    // ──────────────────────────── RBAC Helpers ────────────────────────────

    /**
     * Check if the user has a specific role by slug.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('roles.slug', $roleSlug)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('roles.slug', $roleSlugs)->exists();
    }

    /**
     * Check if the user has a specific permission by slug via their assigned roles.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionSlug) {
            $query->where('permissions.slug', $permissionSlug);
        })->exists();
    }

    /**
     * Check if the user is a System Administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a Doctor.
     */
    public function isDoctor(): bool
    {
        return $this->hasRole('doctor');
    }

    /**
     * Check if the user can access the full Patient Information module (Admin or Doctor).
     */
    public function canAccessPatients(): bool
    {
        return $this->hasAnyRole(['admin', 'doctor']);
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
