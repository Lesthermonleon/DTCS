<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Patient model — the central entity for all clinical requests.
 */
class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_no',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'blood_type',
        'address',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'patient_type',
        'ward',
        'bed_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the patient's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    // ──────────────────────────── Relationships ────────────────────────────

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class);
    }

    public function radiologyRequests(): HasMany
    {
        return $this->hasMany(RadiologyRequest::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function surgeryRequests(): HasMany
    {
        return $this->hasMany(SurgeryRequest::class);
    }

    public function dietRequests(): HasMany
    {
        return $this->hasMany(DietRequest::class);
    }
}
