<?php

namespace App\Models;

use App\Traits\HasStatusBadge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Radiology imaging request ordered by a doctor.
 */
class RadiologyRequest extends Model
{
    use HasStatusBadge;
    use HasFactory;

    protected $fillable = [
        'request_no', 'patient_id', 'doctor_id', 'modality', 'body_part',
        'clinical_information', 'priority', 'status',
        'requested_at', 'scheduled_at', 'completed_at',
    ];

    protected $casts = [
        'requested_at'  => 'datetime',
        'scheduled_at'  => 'datetime',
        'completed_at'  => 'datetime',
    ];

    public function patient(): BelongsTo   { return $this->belongsTo(Patient::class); }
    public function doctor(): BelongsTo    { return $this->belongsTo(User::class, 'doctor_id'); }
    public function images(): HasMany      { return $this->hasMany(RadiologyImage::class); }
    public function report(): HasOne       { return $this->hasOne(RadiologyReport::class); }

    
}

