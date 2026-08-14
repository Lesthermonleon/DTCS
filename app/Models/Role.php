<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role model for RBAC.
 * Each user can have multiple roles via the role_user pivot.
 */
class Role extends Model
{

    protected $fillable = ['name', 'slug', 'description', 'dashboard_route'];

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
     * Get the dashboard route name, falling back to slug-based defaults if not set in DB.
     */
    public function getDashboardRouteAttribute(?string $value): string
    {
        if (!empty($value)) {
            return $value;
        }

        return match ($this->slug) {
            'admin'          => 'admin.dashboard',
            'doctor'         => 'doctor.dashboard',
            'med-tech'       => 'lab.dashboard',
            'rad-tech'       => 'radiology.dashboard',
            'radiologist'    => 'radiology.dashboard',
            'pharmacist'     => 'pharmacy.dashboard',
            'dietitian'      => 'diet.dashboard',
            'or-coordinator' => 'surgery.dashboard',
            default          => 'dashboard',
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
