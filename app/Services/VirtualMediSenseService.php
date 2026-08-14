<?php

namespace App\Services;

use App\Models\MediSenseInteraction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * VirtualMediSenseService — ChatGPT-Style Conversational AI Agent for HIMS.
 *
 * Implements open-ended natural language reasoning using Gemini Native Function Calling,
 * Google Web Search Grounding, multi-turn conversation context, controlled HIMS tools,
 * RBAC authorization, and audit logging.
 */
class VirtualMediSenseService
{
    protected MediSenseContextBuilder $contextBuilder;
    protected MediSenseIntentDetector $intentDetector;
    protected HimsToolProvider $himsToolProvider;
    protected GoogleWebSearchService $webSearchService;

    public function __construct(
        MediSenseContextBuilder $contextBuilder,
        MediSenseIntentDetector $intentDetector,
        HimsToolProvider $himsToolProvider,
        GoogleWebSearchService $webSearchService
    ) {
        $this->contextBuilder   = $contextBuilder;
        $this->intentDetector   = $intentDetector;
        $this->himsToolProvider = $himsToolProvider;
        $this->webSearchService = $webSearchService;
    }

    /**
     * Get list of allowed capabilities for a user based on configuration.
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
     * Verify whether a user is authorized for a specific capability.
     */
    public function isCapabilityAllowed(User $user, string $capabilityKey): bool
    {
        $allowed = $this->getAllowedCapabilities($user);
        return isset($allowed[$capabilityKey]);
    }

    /**
     * Process an open-ended conversational AI request with Gemini Function Calling & Web Grounding.
     */
    public function processRequest(
        User $user,
        ?string $capability = null,
        string $userPrompt = '',
        ?int $patientId = null,
        array $additionalContext = []
    ): array {
        $startTime = microtime(true);
        $roleSlug = $user->primaryRole;

        // 1. Resolve context & allowed capabilities
        $allowedCapabilities = $this->getAllowedCapabilities($user);
        $intentResult = $this->intentDetector->detectIntent($user, $userPrompt, $allowedCapabilities, $patientId);

        // 2. Build clinical & patient context
        $effectivePatientId = $intentResult['requires_patient_context'] ? $patientId : null;
        $context = $this->contextBuilder->buildContext($user, $capability ?? 'open_ended', $effectivePatientId, $additionalContext);

        // 3. Build System Instructions establishing ChatGPT-like general AI role
        $systemPrompt = $this->buildSystemPrompt($user, $context['context_text']);

        $apiKey     = config('medisense.api_key');
        $apiUrl     = config('medisense.api_url');
        $model      = config('medisense.model', 'gemini-2.0-flash');
        $webEnabled = config('medisense.enable_web_search', true);

        // 4. Validate API Key configuration
        if (empty($apiKey)) {
            Log::warning("MediSense API Warning: Gemini API key is not configured in environment.");
            return [
                'success' => false,
                'error'   => 'MediSense AI is currently unable to connect to the AI service. Please check configuration.',
            ];
        }

        // 5. Prepare Gemini API Tools Payload (HIMS Function Tools + Google Search Grounding)
        $tools = [];
        $tools[] = ['functionDeclarations' => $this->himsToolProvider->getToolDeclarations()];
        if ($webEnabled) {
            $tools[] = $this->webSearchService->getGroundingToolDefinition();
        }

        try {
            $endpoint = "{$apiUrl}/models/{$model}:generateContent?key={$apiKey}";

            // Anonymize user prompt before external processing if patient context exists
            $anonymizedPrompt = $this->webSearchService->anonymizeQuery($userPrompt, $context['patient_info']);

            // Build multi-turn conversation content array if previous history is passed
            $contents = [];

            $historyMessages = $additionalContext['conversation_history'] ?? [];
            foreach ($historyMessages as $msg) {
                $contents[] = [
                    'role'  => $msg['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [['text' => $msg['content']]],
                ];
            }

            // Append current user prompt directly
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $anonymizedPrompt]],
            ];

            $payload = [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemPrompt],
                    ],
                ],
                'contents'         => $contents,
                'generationConfig' => [
                    'temperature'     => 0.3,
                    'maxOutputTokens' => 2048,
                ],
                'tools'            => $tools,
            ];

            $response = Http::timeout(config('medisense.timeout', 30))->post($endpoint, $payload);
            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $responseData = $response->json();
                $candidate = $responseData['candidates'][0] ?? [];
                $content = $candidate['content'] ?? [];
                $parts = $content['parts'] ?? [];

                // Check if Gemini requested a function call
                $functionCall = null;
                foreach ($parts as $part) {
                    if (isset($part['functionCall'])) {
                        $functionCall = $part['functionCall'];
                        break;
                    }
                }

                $sources = ['✨ AI Analysis'];

                if ($functionCall) {
                    $fnName = $functionCall['name'];
                    $fnArgs = $functionCall['args'] ?? [];

                    // Execute requested HIMS tool safely
                    $toolResult = $this->himsToolProvider->executeTool($fnName, $fnArgs, $user);

                    // If tool requires confirmation for sensitive action, prompt user
                    if (isset($toolResult['requires_confirm']) && $toolResult['requires_confirm']) {
                        return [
                            'success'          => true,
                            'requires_confirm' => true,
                            'action_details'   => $toolResult['action_details'],
                            'ai_response'      => $toolResult['message'],
                            'context_info'     => $context['patient_info'],
                            'intent'           => 'ACTION_CONFIRMATION_REQUIRED',
                            'sources'          => ['🏥 HIMS System'],
                            'notice'           => config('medisense.safety_notice'),
                        ];
                    }

                    $sources[] = '🏥 HIMS Data';

                    // Multi-turn function response back to Gemini
                    $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $functionCall]]];
                    $contents[] = [
                        'role'  => 'user',
                        'parts' => [
                            [
                                'functionResponse' => [
                                    'name'     => $fnName,
                                    'response' => $toolResult,
                                ],
                            ],
                        ],
                    ];

                    $followUpPayload = [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemPrompt]],
                        ],
                        'contents'         => $contents,
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 2048],
                    ];

                    $followUpResp = Http::timeout(config('medisense.timeout', 30))->post($endpoint, $followUpPayload);
                    if ($followUpResp->successful()) {
                        $responseData = $followUpResp->json();
                        $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? "Executed HIMS action `{$fnName}`.";
                    } else {
                        $aiText = "Executed HIMS function `{$fnName}`:\n```json\n" . json_encode($toolResult, JSON_PRETTY_PRINT) . "\n```";
                    }
                } else {
                    $aiText = $parts[0]['text'] ?? 'I understand your request. How else can I assist you?';
                }

                // Extract citations from Google Search Grounding metadata
                $citationsData = $this->webSearchService->extractCitations($responseData);
                if (!empty($citationsData['citations'])) {
                    $sources[] = '🌐 Web Sources';
                }

                $tokensUsed = $responseData['usageMetadata']['totalTokenCount'] ?? null;

                $this->logInteraction(
                    $user->id, $roleSlug, $capability ?? 'open_ended', $additionalContext['module'] ?? null,
                    $effectivePatientId, $userPrompt, $aiText, $tokensUsed, $responseTimeMs, 'success', null
                );

                return [
                    'success'          => true,
                    'ai_response'      => $aiText,
                    'context_info'     => $context['patient_info'],
                    'intent'           => $intentResult['intent'],
                    'capability'       => $capability,
                    'capability_label' => 'MediSense AI Agent',
                    'sources'          => array_values(array_unique($sources)),
                    'citations'        => $citationsData['citations'] ?? [],
                    'notice'           => config('medisense.safety_notice'),
                ];
            } else {
                $errorBody = $response->body();
                Log::error("MediSense API Error: " . $errorBody);
                $this->logInteraction(
                    $user->id, $roleSlug, 'open_ended', $additionalContext['module'] ?? null,
                    $effectivePatientId, $userPrompt, null, 0, $responseTimeMs, 'error', $errorBody
                );

                return [
                    'success' => false,
                    'error'   => 'MediSense AI is currently unable to connect to the AI service. Please try again.',
                ];
            }
        } catch (Throwable $e) {
            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            Log::error("MediSense Exception: " . $e->getMessage());

            $this->logInteraction(
                $user->id, $roleSlug, 'open_ended', $additionalContext['module'] ?? null,
                $effectivePatientId, $userPrompt, null, 0, $responseTimeMs, 'error', $e->getMessage()
            );

            return [
                'success' => false,
                'error'   => 'Unable to connect to MediSense AI service: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build system prompt establishing ChatGPT-like General Conversational AI role.
     */
    protected function buildSystemPrompt(User $user, string $contextText): string
    {
        $roleName     = $user->roleName;
        $safetyNotice = config('medisense.safety_notice');

        return <<<PROMPT
You are MediSense AI: A General Conversational AI Assistant and Clinical Decision Support Agent integrated into a Hospital Information Management System (HIMS).

IDENTITY & BEHAVIOR:
- You behave like a general-purpose AI assistant (such as ChatGPT) with specialized clinical capabilities.
- You understand open-ended, natural language requests, paraphrases, context, follow-up questions, and general medical inquiries.
- You have access to 3 knowledge sources:
  1. 🏥 HIMS Database (via controlled function tools)
  2. 🌐 Real-Time Web Search (Google Search Grounding)
  3. ✨ Gemini AI Knowledge & Reasoning

CAPABILITIES:
- Answer general questions (medical definitions, explanations, general knowledge).
- Inspect HIMS patient records, lab results, radiology reports, prescriptions, and departmental workloads using provided function tools.
- Perform internet research when current external guidelines, research, or monographs are helpful.
- Prepare and submit authorized HIMS actions when requested (with explicit user confirmation for write operations).

SAFETY & PRIVACY RULES:
1. Provide decision support for user role: {$roleName}.
2. Patient data in HIMS remains strictly private; never send patient names or direct identifiers in external web searches.
3. Express clinical observations using decision-support language ("may suggest", "consider evaluating", "consistent with").
4. All write actions (creating lab requests, ordering procedures) require explicit confirmation.

SYSTEM AUTHORIZATION CONTEXT:
{$contextText}

{$safetyNotice}
PROMPT;
    }

    /**
     * Record interaction in audit log.
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
            Log::error("Failed to write MediSense log: " . $e->getMessage());
        }
    }
}
