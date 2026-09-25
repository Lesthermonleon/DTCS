<?php

namespace App\Contracts;

/**
 * AIProviderInterface — Provider abstraction for Virtual MediSense AI.
 *
 * Ensures all clinical decision-support requests pass through a clean,
 * provider-independent contract. Stage 1 utilizes MockAIProvider.
 * Stage 2 will replace MockAIProvider with EvidenceMDProvider or another API
 * without modifying controllers, services, or views.
 */
interface AIProviderInterface
{
    /**
     * Analyze structured clinical context and return normalized decision support recommendations.
     *
     * @param string $task Task identifier: SYMPTOM_ASSESSMENT, DIAGNOSTIC_ASSISTANCE, TREATMENT_RECOMMENDATION, CLINICAL_SERVICE_ASSISTANCE
     * @param array $context Structured patient context gathered from HIMS modules
     * @return array Normalized structured AI response
     */
    public function analyzeClinicalContext(string $task, array $context): array;
}
