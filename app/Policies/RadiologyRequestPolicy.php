<?php

namespace App\Policies;

use App\Models\RadiologyRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RadiologyRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any radiology requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'rad-tech', 'radiologist']);
    }

    /**
     * Determine whether the user can view the specific radiology request.
     */
    public function view(User $user, RadiologyRequest $radiologyRequest): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'rad-tech', 'radiologist']);
    }

    /**
     * Determine whether the user can create radiology requests (Doctors & Admins).
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /**
     * Determine whether the user can update/edit the radiology request (Doctors & Admins when Pending).
     */
    public function update(User $user, RadiologyRequest $radiologyRequest): bool
    {
        if ($radiologyRequest->status !== 'Pending') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /**
     * Determine whether the user can delete/cancel the radiology request (Doctors & Admins when Pending).
     */
    public function delete(User $user, RadiologyRequest $radiologyRequest): bool
    {
        if ($radiologyRequest->status !== 'Pending') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /**
     * Determine whether the user can schedule/process the imaging procedure (Rad Techs & Admins when Pending).
     */
    public function schedule(User $user, RadiologyRequest $radiologyRequest): bool
    {
        if ($radiologyRequest->status !== 'Pending') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'rad-tech']);
    }

    /**
     * Determine whether the user can start the imaging procedure (Rad Techs & Admins when Scheduled).
     */
    public function start(User $user, RadiologyRequest $radiologyRequest): bool
    {
        if ($radiologyRequest->status !== 'Scheduled') {
            return false;
        }

        return $user->hasAnyRole(['admin', 'rad-tech']);
    }

    /**
     * Determine whether the user can upload scan images/records (Rad Techs & Admins when Scheduled or In Progress).
     */
    public function uploadImage(User $user, RadiologyRequest $radiologyRequest): bool
    {
        if (! in_array($radiologyRequest->status, ['Scheduled', 'In Progress'])) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'rad-tech']);
    }

    /**
     * Determine whether the user can mark the imaging procedure as completed (Rad Techs & Admins when Scheduled or In Progress).
     */
    public function complete(User $user, RadiologyRequest $radiologyRequest): bool
    {
        if (! in_array($radiologyRequest->status, ['Scheduled', 'In Progress'])) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'rad-tech']);
    }
}
