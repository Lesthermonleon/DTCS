<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Surgery request submitted by a doctor for a patient.
 */
class SurgeryRequest extends Model
{
    use HasStatusBadge;
    use HasFactory;

    protected $fillable = [
        'request_no', 'patient_id', 'doctor_id', 'procedure_name',
        'diagnosis', 'urgency', 'status', 'notes',
        'anesthesia_type', 'estimated_duration', 'requested_at',
    ];

    protected $casts = ['requested_at' => 'datetime'];

    public function patient(): BelongsTo   { return $this->belongsTo(Patient::class); }
    public function doctor(): BelongsTo    { return $this->belongsTo(User::class, 'doctor_id'); }
    public function schedule(): HasOne     { return $this->hasOne(SurgerySchedule::class); }

    
}

