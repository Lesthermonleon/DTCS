<?php

namespace App\Services;

use App\Models\User;

/**
 * MediSenseIntentDetector — Intent resolver placeholder.
 * AI intent parsing and natural language keyword analysis have been removed.
 */
class MediSenseIntentDetector
{
    /**
     * Resolve intent payload placeholder for UI requests.
     */
    public function detectIntent(User $user, string $userPrompt, array $allowedCapabilities, ?int $patientId = null): array
    {
        return [
            'intent'                   => 'UNAVAILABLE',
            'matched_capability'       => null,
            'requires_patient_context' => false,
            'confidence'               => 0.0,
        ];
    }
}
