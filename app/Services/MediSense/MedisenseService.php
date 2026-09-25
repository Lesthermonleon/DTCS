<?php

namespace App\Services\MediSense;

use App\AI\EvidenceMDProvider;
use App\AI\MockAIProvider;
use App\Contracts\AIProviderInterface;
use App\Models\ActivityLog;
use App\Models\MedisenseInteraction;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * MedisenseService — Core service orchestrator for Virtual MediSense AI.
 *
 * Flow:
 * 1. Build structured clinical context from existing HIMS data.
 * 2. Resolve configured provider (MockAIProvider or EvidenceMDProvider).
 * 3. Invoke provider-independent analyzeClinicalContext method.
 * 4. Record interaction in medisense_interactions table and ActivityLog for auditing.
 * 5. Return normalized structured response to Doctor.
 */
class MedisenseService
{
    protected ClinicalContextBuilder $contextBuilder;

    public function __construct(ClinicalContextBuilder $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
    }

    /**
     * Resolve the active AI provider based on configuration.
     *
     * @return AIProviderInterface
     */
    public function getProvider(): AIProviderInterface
    {
        $providerName = config('ai.provider', 'mock');

        return match (strtolower($providerName)) {
            'evidencemd' => new EvidenceMDProvider(),
            'mock'       => new MockAIProvider(),
            default      => new MockAIProvider(),
        };
    }

    /**
     * Execute a MediSense clinical decision-support task.
     *
     * @param string $task SYMPTOM_ASSESSMENT | DIAGNOSTIC_ASSISTANCE | TREATMENT_RECOMMENDATION | CLINICAL_SERVICE_ASSISTANCE
     * @param Patient $patient
     * @return array
     */
    public function executeTask(string $task, Patient $patient): array
    {
        // 1. Build Context
        $context = $this->contextBuilder->build($patient);

        // 2. Resolve Provider & Analyze Context
        $provider = $this->getProvider();
        $response = $provider->analyzeClinicalContext($task, $context);

        // 3. Log Interaction in medisense_interactions table
        $userId   = Auth::id();
        $userIp   = Request::ip();
        $summary  = $response['summary'] ?? 'MediSense analysis executed successfully.';

        MedisenseInteraction::create([
            'user_id'        => $userId,
            'patient_id'     => $patient->id,
            'task'           => $task,
            'provider'       => $response['provider'] ?? 'Mock AI Provider',
            'status'         => $response['status'] ?? 'Development/Test Mode',
            'result_summary' => $summary,
            'ip_address'     => $userIp,
            'logged_at'      => now(),
        ]);

        // 4. Log Action in system ActivityLog for auditing
        ActivityLog::create([
            'user_id'       => $userId,
            'action'        => "Executed MediSense AI Task: {$task}",
            'module'        => 'MediSense AI',
            'description'   => "Doctor executed clinical decision-support task '{$task}' for Patient #{$patient->patient_no} ({$patient->full_name}). Provider: " . ($response['provider'] ?? 'Mock AI Provider'),
            'loggable_type' => Patient::class,
            'loggable_id'   => $patient->id,
            'ip_address'    => $userIp,
            'severity'      => ActivityLog::SEVERITY_INFO,
            'result'        => ActivityLog::RESULT_SUCCESS,
            'logged_at'     => now(),
        ]);

        return $response;
    }
}
