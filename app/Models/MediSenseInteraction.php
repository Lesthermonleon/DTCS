<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit log model for Virtual MediSense AI interactions.
 */
class MediSenseInteraction extends Model
{
    protected $table = 'medisense_interactions';

    protected $fillable = [
        'user_id',
        'user_role',
        'capability',
        'module',
        'patient_id',
        'user_prompt',
        'ai_response',
        'tokens_used',
        'response_time_ms',
        'status',
        'error_message',
    ];

    /**
     * The user who initiated the AI interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The patient context associated with the interaction, if any.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
