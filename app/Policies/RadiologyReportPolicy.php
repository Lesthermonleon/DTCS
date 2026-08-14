<?php

namespace App\Policies;

use App\Models\RadiologyReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RadiologyReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any radiology reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'radiologist', 'doctor', 'rad-tech']);
    }

    /**
     * Determine whether the user can view a specific radiology report.
     */
    public function view(User $user, RadiologyReport $radiologyReport): bool
    {
        return $user->hasAnyRole(['admin', 'radiologist', 'doctor', 'rad-tech']);
    }

    /**
     * Determine whether the user can create radiology reports (Radiologists & Admins only).
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'radiologist']);
    }

    /**
     * Determine whether the user can edit/update a radiology report (Radiologists & Admins when not Released).
     */
    public function update(User $user, RadiologyReport $radiologyReport): bool
    {
        if ($radiologyReport->status === 'Released') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'radiologist']);
    }

    /**
     * Determine whether the user can delete a draft report (Radiologists & Admins when Draft).
     */
    public function delete(User $user, RadiologyReport $radiologyReport): bool
    {
        if ($radiologyReport->status !== 'Draft') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'radiologist']);
    }

    /**
     * Determine whether the user can approve/finalize a report (Radiologists & Admins when Draft).
     */
    public function approve(User $user, RadiologyReport $radiologyReport): bool
    {
        if ($radiologyReport->status !== 'Draft') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'radiologist']);
    }

    /**
     * Determine whether the user can release a report to referring doctor (Radiologists & Admins when Approved).
     */
    public function release(User $user, RadiologyReport $radiologyReport): bool
    {
        if ($radiologyReport->status !== 'Approved') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'radiologist']);
    }
}
