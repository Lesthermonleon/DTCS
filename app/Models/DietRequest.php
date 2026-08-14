<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Therapeutic diet request submitted by a doctor for a patient.
 */
class DietRequest extends Model
{
    use HasStatusBadge;

    protected $fillable = [
        'request_no', 'patient_id', 'doctor_id', 'diet_type',
        'allergies', 'food_restrictions', 'clinical_notes', 'status', 'requested_at',
    ];

    protected $casts = ['requested_at' => 'datetime'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function doctor(): BelongsTo  { return $this->belongsTo(User::class, 'doctor_id'); }
    public function dietPlan(): HasOne   { return $this->hasOne(DietPlan::class); }

    
}

