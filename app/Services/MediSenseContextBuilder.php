<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;

/**
 * MediSenseContextBuilder — Basic context metadata provider for MediSense UI.
 * AI prompt construction and clinical data extraction for LLMs have been removed.
 */
class MediSenseContextBuilder
{
    /**
     * Build context metadata array for UI rendering.
     */
    public function buildContext(User $user, string $capability, ?int $patientId = null, array $additionalContext = []): array
    {
        $patientSummary = null;
        if ($patientId) {
            $patient = Patient::find($patientId);
            if ($patient) {
                $patientSummary = [
                    'id'         => $patient->id,
                    'patient_no' => $patient->patient_no,
                    'name'       => $patient->full_name,
                ];
            }
        }

        return [
            'context_text' => 'MediSense AI context generation disabled.',
            'patient_info' => $patientSummary,
            'role_slug'    => $user->primaryRole,
        ];
    }
}
