<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lab request created by a doctor for a patient.
 */
class LabRequest extends Model
{
    use HasStatusBadge;
    use HasFactory;

    protected $fillable = [
        'request_no',
        'patient_id',
        'doctor_id',
        'priority',
        'status',
        'clinical_notes',
        'specimen_type',
        'requested_at',
        'received_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'received_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LabRequestItem::class);
    }

    
}

