<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role model for RBAC.
 * Each user can have multiple roles via the role_user pivot.
 */
class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Get accessible modules for this role.
     */
    public function getAccessibleModulesAttribute(): string
    {
        return match ($this->slug) {
            'admin'          => 'All Modules (LIS, RIS, PMS, SORS, DNMS, User Management, Permission)',
            'doctor'         => 'LIS, RIS, PMS, SORS, DNMS',
            'med-tech'       => 'LIS',
            'rad-tech'       => 'RIS',
            'radiologist'    => 'RIS',
            'pharmacist'     => 'PMS',
            'dietitian'      => 'DNMS',
            'or-coordinator' => 'SORS',
            default          => 'None',
        };
    }

    /**
     * Users that belong to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Permissions assigned to this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
