<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MedisenseInteraction model — records all MediSense AI clinical decision-support actions.
 *
 * This is an APPEND-ONLY table for audit and traceability.
 */
class MedisenseInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'task',
        'provider',
        'status',
        'result_summary',
        'ip_address',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
