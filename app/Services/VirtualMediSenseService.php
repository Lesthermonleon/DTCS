<?php

namespace App\Services;

use App\Models\MediSenseInteraction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * VirtualMediSenseService — Manages role capability definitions for the MediSense UI.
 * AI backend execution, external LLM calls, and predictive algorithms have been removed.
 */
class VirtualMediSenseService
{
    /**
     * Get list of allowed capabilities for a user based on configuration (used for UI rendering).
     */
    public function getAllowedCapabilities(User $user): array
    {
        $allConfigured = config('medisense.capabilities', []);
        $allowed = [];

        foreach ($allConfigured as $roleCaps) {
            foreach ($roleCaps as $key => $cap) {
                if (!isset($allowed[$key])) {
                    $allowed[$key] = array_merge($cap, ['key' => $key]);
                }
            }
        }

        return $allowed;
    }

    /**
     * Verify whether a user role capability exists for UI inspection.
     */
    public function isCapabilityAllowed(User $user, string $capabilityKey): bool
    {
        $allowed = $this->getAllowedCapabilities($user);
        return isset($allowed[$capabilityKey]);
    }

    /**
     * Process request — Returns temporary unavailable status (AI processing disabled).
     */
    public function processRequest(
        User $user,
        ?string $capability = null,
        string $userPrompt = '',
        ?int $patientId = null,
        array $additionalContext = []
    ): array {
        // Record attempt in local interaction log without calling any AI backend
        $this->logInteraction(
            $user->id,
            $user->primaryRole,
            $capability ?? 'open_ended',
            $additionalContext['module'] ?? null,
            $patientId,
            $userPrompt,
            'MediSense AI functionality is temporarily unavailable.',
            0,
            0,
            'disabled',
            null
        );

        return [
            'success'          => false,
            'error'            => 'MediSense AI functionality is temporarily unavailable.',
            'ai_response'      => 'MediSense AI is temporarily unavailable.',
            'capability_label' => 'MediSense AI',
            'sources'          => [],
            'citations'        => [],
            'notice'           => config('medisense.safety_notice'),
        ];
    }

    /**
     * Record interaction attempt in audit log table if accessible.
     */
    protected function logInteraction(
        int $userId,
        string $userRole,
        string $capability,
        ?string $module,
        ?int $patientId,
        string $userPrompt,
        ?string $aiResponse,
        ?int $tokensUsed,
        int $responseTimeMs,
        string $status,
        ?string $errorMessage
    ): void {
        try {
            MediSenseInteraction::create([
                'user_id'          => $userId,
                'user_role'        => $userRole,
                'capability'       => $capability,
                'module'           => $module,
                'patient_id'       => $patientId,
                'user_prompt'      => $userPrompt,
                'ai_response'      => $aiResponse,
                'tokens_used'      => $tokensUsed,
                'response_time_ms' => $responseTimeMs,
                'status'           => $status,
                'error_message'    => $errorMessage,
            ]);
        } catch (Throwable $e) {
            Log::info("MediSense interaction log skipped: " . $e->getMessage());
        }
    }
}
