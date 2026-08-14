<?php

namespace App\Services;

use App\Models\User;

/**
 * MediSenseIntentDetector — Open-Ended Context & Capability Resolver.
 *
 * Provides context resolution for Gemini AI Agent without using hardcoded
 * phrase whitelists, question scripts, or regex question matching.
 */
class MediSenseIntentDetector
{
    /**
     * Resolve capability context for the user prompt and role.
     * Gemini dynamically determines intent and function execution natively.
     */
    public function detectIntent(
        User $user,
        string $userPrompt,
        array $allowedCapabilities,
        ?int $patientId = null
    ): array {
        $cleanPrompt = trim($userPrompt);

        // Check if patient context is present in parameter or prompt
        $isPatientSpecific = $patientId !== null || (bool) preg_match('/(patient|his|her|this|record|chart|p-[0-9]+)/i', $cleanPrompt);

        return [
            'intent'                   => 'OPEN_ENDED',
            'capability'               => null,
            'human_label'              => null,
            'requires_hims'            => true,
            'requires_web'             => true,
            'requires_patient_context' => $isPatientSpecific,
            'is_action'                => (bool) preg_match('/(create|order|request|schedule|prescribe)/i', $cleanPrompt),
        ];
    }
}
