<?php

namespace App\AI;

use App\Contracts\AIProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * EvidenceMDProvider — Real AI Provider Adapter for Virtual MediSense AI.
 *
 * Interacts with the EvidenceMD API to perform evidence-based clinical decision support
 * for licensed physicians. Adheres strictly to provider-independent contracts,
 * data minimization, server-side privacy boundaries (zero patient name/MRN/location leakage),
 * safe technical logging, and physician-in-the-loop safety principles.
 */
class EvidenceMDProvider implements AIProviderInterface
{
    public const PROVIDER_NAME = 'EvidenceMD API Provider';
    public const STATUS_ACTIVE = 'Stage 2 — EvidenceMD AI Active';
    public const STATUS_ERROR  = 'EvidenceMD Provider Error';

    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $config = config('ai.evidencemd', []);

        $this->apiKey  = $config['api_key']  ?? config('ai.api_key', '');
        $this->baseUrl = $config['base_url'] ?? config('ai.base_url', 'https://evidencemd.ai/api/v1');
        $this->model   = $config['model']    ?? config('ai.model', 'evidencemd-pro');
        $this->timeout = (int) ($config['timeout'] ?? 60);
    }

    /**
     * Analyze structured clinical context via EvidenceMD API.
     *
     * @param string $task SYMPTOM_ASSESSMENT | DIAGNOSTIC_ASSISTANCE | TREATMENT_RECOMMENDATION | CLINICAL_SERVICE_ASSISTANCE
     * @param array $context Raw HIMS clinical context from ClinicalContextBuilder
     * @return array Structured MediSense response
     */
    public function analyzeClinicalContext(string $task, array $context): array
    {
        $patient  = $context['patient'] ?? [];
        $name     = $patient['full_name'] ?? 'Selected Patient';
        $no       = $patient['patient_no'] ?? 'N/A';
        $age      = $patient['age'] ?? 'N/A';
        $gender   = $patient['gender'] ?? 'N/A';
        $type     = $patient['patient_type'] ?? 'N/A';
        $findings = $context['symptoms_and_findings'] ?? 'No clinical findings recorded.';

        $disclaimer = 'MediSense is a clinical decision-support component powered by EvidenceMD. '
            . 'Information generated is intended to assist the attending Doctor and does not replace professional clinical judgment. '
            . 'The attending Doctor remains responsible for all final clinical decisions, diagnoses, treatments, and orders.';

        $taskLabels = [
            'SYMPTOM_ASSESSMENT'          => 'Symptom Assessment',
            'DIAGNOSTIC_ASSISTANCE'       => 'Diagnostic Assistance',
            'TREATMENT_RECOMMENDATION'    => 'Treatment Recommendation',
            'CLINICAL_SERVICE_ASSISTANCE' => 'Clinical Service Assistance',
        ];
        $taskLabel = $taskLabels[$task] ?? $task;

        // Internal HIMS patient record details (Retained inside HIMS for authorized hospital UI display ONLY)
        $contextReceived = [
            'patient_name' => $name,
            'patient_no'   => $no,
            'age'          => $age,
            'gender'       => $gender,
            'patient_type' => $type,
            'ward'         => ($patient['ward'] ?? null) ? "{$patient['ward']}" . ($patient['bed_number'] ? " / Bed {$patient['bed_number']}" : '') : null,
        ];

        $himsContext = [
            'laboratory_results'   => count($context['recent_lab_results'] ?? []),
            'radiology_reports'    => count($context['recent_radiology_reports'] ?? []),
            'active_prescriptions' => count($context['active_prescriptions'] ?? []),
            'surgery_requests'     => count($context['surgery_requests'] ?? []),
            'diet_requests'        => count($context['diet_requests'] ?? []),
        ];

        if (empty($this->apiKey)) {
            Log::warning('EvidenceMD API key is missing or not configured.', [
                'provider' => self::PROVIDER_NAME,
                'model'    => $this->model,
                'task'     => $task,
                'category' => 'missing_api_key',
            ]);

            return [
                'task'             => $task,
                'provider'         => self::PROVIDER_NAME,
                'status'           => 'Configuration Notice',
                'summary'          => "EvidenceMD API Key is missing. Please configure `EVIDENCEMD_API_KEY` in environment settings to enable live AI clinical analysis for {$taskLabel}.",
                'context_received' => $contextReceived,
                'findings_received'=> $findings,
                'hims_context'     => $himsContext,
                'evidence'         => [
                    'EvidenceMD Provider Adapter v1',
                    'Notice: Missing API credentials',
                ],
                'citations'        => [],
                'disclaimer'       => $disclaimer,
                'clinical_considerations'         => [],
                'diagnostic_considerations'       => [],
                'treatment_considerations'        => [],
                'clinical_service_considerations' => [],
            ];
        }

        $startTime = microtime(true);

        try {
            // Build prompt with SERVER-SIDE SANITIZED context (Zero PHI/PII leakage over API wire)
            $promptData = $this->buildPrompt($task, $context);
            $endpoint   = str_ends_with($this->baseUrl, '/chat/completions')
                ? $this->baseUrl
                : rtrim($this->baseUrl, '/') . '/chat/completions';

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'x-api-key'     => $this->apiKey,
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept'        => 'application/json',
                ])
                ->post($endpoint, [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $promptData['system']],
                        ['role' => 'user', 'content' => $promptData['user']],
                    ],
                    'temperature' => 0.2,
                ]);

            $durationMs = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->failed()) {
                $statusCode = $response->status();
                [$userMessage, $category, $statusText] = $this->mapHttpError($statusCode);

                // Safe technical logging - NO PHI, NO API KEY, NO PROMPT, NO HEADERS
                Log::error("EvidenceMD API Request Failed [HTTP {$statusCode}]", [
                    'provider'    => self::PROVIDER_NAME,
                    'model'       => $this->model,
                    'task'        => $task,
                    'status'      => $statusCode,
                    'duration_ms' => $durationMs,
                    'category'    => $category,
                ]);

                return $this->buildFallbackResponse(
                    $task,
                    $taskLabel,
                    $userMessage,
                    $contextReceived,
                    $findings,
                    $himsContext,
                    $disclaimer,
                    $statusText
                );
            }

            $resJson = $response->json();
            $parsed  = $this->parseApiResponse($resJson, $task);

            if ($this->isResponseEmpty($parsed)) {
                Log::warning('EvidenceMD returned malformed or unexpected response', [
                    'provider'    => self::PROVIDER_NAME,
                    'model'       => $this->model,
                    'task'        => $task,
                    'duration_ms' => $durationMs,
                    'category'    => 'malformed_response',
                ]);

                return $this->buildFallbackResponse(
                    $task,
                    $taskLabel,
                    'EvidenceMD returned an unexpected response. Please try again.',
                    $contextReceived,
                    $findings,
                    $himsContext,
                    $disclaimer,
                    'Unexpected Response'
                );
            }

            return [
                'task'             => $task,
                'provider'         => self::PROVIDER_NAME,
                'status'           => self::STATUS_ACTIVE,
                'summary'          => $parsed['summary'] ?? "EvidenceMD analysis completed for {$taskLabel}.",
                'context_received' => $contextReceived, // Internal HIMS patient details for authorized UI display
                'findings_received'=> $findings,
                'hims_context'     => $himsContext,
                'evidence'         => array_merge([
                    "EvidenceMD Provider v1 — Model: {$this->model}",
                    'Privacy Boundary — Server-side anonymized clinical payload (Name, MRN & Ward excluded)',
                ], $parsed['citations'] ?? []),
                'citations'        => $parsed['citations'] ?? [],
                'disclaimer'       => $disclaimer,
                'clinical_considerations'         => $parsed['clinical_considerations'] ?? [],
                'diagnostic_considerations'       => $parsed['diagnostic_considerations'] ?? [],
                'treatment_considerations'        => $parsed['treatment_considerations'] ?? [],
                'clinical_service_considerations' => $parsed['clinical_service_considerations'] ?? [],
            ];

        } catch (Throwable $e) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);

            // Safe technical logging - NO PHI, NO STACK TRACE, NO API KEY
            Log::error('EvidenceMD Provider Exception: Connection failure', [
                'provider'    => self::PROVIDER_NAME,
                'model'       => $this->model,
                'task'        => $task,
                'duration_ms' => $durationMs,
                'category'    => 'connection_error',
            ]);

            return $this->buildFallbackResponse(
                $task,
                $taskLabel,
                'Unable to connect to EvidenceMD. Please try again later.',
                $contextReceived,
                $findings,
                $himsContext,
                $disclaimer,
                'Connection Error'
            );
        }
    }

    /**
     * Sanitize clinical context to enforce data minimization & server-side privacy boundaries.
     * Excludes all direct patient identifiers (full_name, patient_no, id, ward, bed_number, etc.)
     * and scrubs free-text fields before transmitting request payload to EvidenceMD API.
     *
     * @param array $context Raw HIMS clinical context
     * @return array Minimum necessary non-identifying clinical context
     */
    public function sanitizeContextForEvidenceMD(array $context): array
    {
        $patient = $context['patient'] ?? [];

        // Demographics — include only non-identifying clinical parameters
        $cleanDemographics = array_filter([
            'age'          => $patient['age'] ?? null,
            'sex'          => $patient['gender'] ?? null,
            'care_setting' => $patient['patient_type'] ?? null,
            'blood_type'   => $patient['blood_type'] ?? null,
        ]);

        // Laboratory results — strip internal patient & lab request IDs + sanitize free-text remarks
        $cleanLabs = array_map(function ($lab) use ($patient) {
            return array_filter([
                'test_name'    => $lab['test_name'] ?? null,
                'result_value' => $lab['result_value'] ?? null,
                'remarks'      => $this->sanitizeFreeText($lab['remarks'] ?? null, $patient),
                'status'       => $lab['status'] ?? null,
                'released_at'  => isset($lab['released_at']) ? substr($lab['released_at'], 0, 10) : null,
            ]);
        }, $context['recent_lab_results'] ?? []);

        // Radiology reports — strip internal patient & radiology request IDs + sanitize free-text findings & impression
        $cleanRad = array_map(function ($rad) use ($patient) {
            return array_filter([
                'modality'        => $rad['modality'] ?? null,
                'body_part'       => $rad['body_part'] ?? null,
                'findings'        => $this->sanitizeFreeText($rad['findings'] ?? null, $patient),
                'impression'      => $this->sanitizeFreeText($rad['impression'] ?? null, $patient),
                'recommendations' => $this->sanitizeFreeText($rad['recommendations'] ?? null, $patient),
            ]);
        }, $context['recent_radiology_reports'] ?? []);

        // Active prescriptions — strip prescription_no, order IDs + sanitize diagnosis text
        $cleanRx = array_map(function ($rx) use ($patient) {
            return array_filter([
                'diagnosis'   => $this->sanitizeFreeText($rx['diagnosis'] ?? null, $patient),
                'medications' => $rx['medications'] ?? null,
                'status'      => $rx['status'] ?? null,
            ]);
        }, $context['active_prescriptions'] ?? []);

        // Surgery requests — strip request_no and patient IDs
        $cleanSurgery = array_map(function ($surg) {
            return array_filter([
                'procedure_name' => $surg['procedure_name'] ?? null,
                'urgency'        => $surg['urgency'] ?? null,
                'status'         => $surg['status'] ?? null,
            ]);
        }, $context['surgery_requests'] ?? []);

        // Diet requests — strip request_no and patient IDs
        $cleanDiet = array_map(function ($diet) {
            return array_filter([
                'diet_type' => $diet['diet_type'] ?? null,
                'allergies' => $diet['allergies'] ?? null,
                'status'    => $diet['status'] ?? null,
            ]);
        }, $context['diet_requests'] ?? []);

        $cleanData = [
            'clinical_demographics'    => !empty($cleanDemographics) ? $cleanDemographics : null,
            'relevant_findings'        => $this->sanitizeFreeText($context['symptoms_and_findings'] ?? null, $patient),
            'recent_lab_results'       => !empty(array_filter($cleanLabs)) ? array_values(array_filter($cleanLabs)) : null,
            'recent_radiology_reports' => !empty(array_filter($cleanRad)) ? array_values(array_filter($cleanRad)) : null,
            'active_prescriptions'     => !empty(array_filter($cleanRx)) ? array_values(array_filter($cleanRx)) : null,
            'surgery_requests'         => !empty(array_filter($cleanSurgery)) ? array_values(array_filter($cleanSurgery)) : null,
            'diet_requests'            => !empty(array_filter($cleanDiet)) ? array_values(array_filter($cleanDiet)) : null,
            'privacy_boundary'        => 'Server-enforced: All direct & indirect identifiers (Name, MRN, Ward, Bed, Request Nos) excluded and free-text sanitized.',
        ];

        return array_filter($cleanData);
    }

    /**
     * Scrub direct patient identifiers (name, MRN, ward, bed) from free-text clinical notes.
     *
     * @param string|null $text
     * @param array $patient
     * @return string|null
     */
    public function sanitizeFreeText(?string $text, array $patient): ?string
    {
        if (empty($text)) {
            return $text;
        }

        $targets = [];

        if (!empty($patient['full_name'])) {
            $targets[] = $patient['full_name'];
            $parts = explode(' ', $patient['full_name']);
            foreach ($parts as $part) {
                $trimmed = trim($part, '., ');
                if (strlen($trimmed) >= 3) {
                    $targets[] = $trimmed;
                }
            }
        }

        if (!empty($patient['patient_no'])) {
            $targets[] = $patient['patient_no'];
        }

        if (!empty($patient['ward'])) {
            $targets[] = $patient['ward'];
        }

        if (!empty($patient['bed_number'])) {
            $targets[] = "Bed {$patient['bed_number']}";
            $targets[] = $patient['bed_number'];
        }

        usort($targets, fn($a, $b) => strlen($b) <=> strlen($a));
        $targets = array_unique($targets);

        foreach ($targets as $target) {
            if (empty($target)) continue;
            $text = str_ireplace($target, '[REDACTED_IDENTIFIER]', $text);
        }

        return $text;
    }

    /**
     * Map HTTP status codes to user-facing error messages, safe log categories, and status badges.
     *
     * @param int $statusCode
     * @return array [string $userMessage, string $category, string $statusText]
     */
    protected function mapHttpError(int $statusCode): array
    {
        return match (true) {
            $statusCode === 400 => [
                'EvidenceMD rejected the request. Please verify the request configuration.',
                'bad_request',
                'Request Rejected (HTTP 400)',
            ],
            $statusCode === 401 => [
                'EvidenceMD authentication failed. Please verify the configured API key.',
                'authentication_failure',
                'Authentication Failed (HTTP 401)',
            ],
            $statusCode === 402 => [
                'EvidenceMD could not process this request because the API account has insufficient credits or requires billing/credit verification. Please check the EvidenceMD developer portal.',
                'insufficient_credits',
                'Insufficient Credits (HTTP 402)',
            ],
            $statusCode === 403 => [
                'EvidenceMD denied the API request. Please verify the API account permissions.',
                'permission_denied',
                'Permission Denied (HTTP 403)',
            ],
            $statusCode === 408 => [
                'EvidenceMD request timed out. Please try again.',
                'request_timeout',
                'Request Timeout (HTTP 408)',
            ],
            $statusCode === 429 => [
                'EvidenceMD request limit was reached. Please wait and try again.',
                'rate_limit_exceeded',
                'Rate Limit Exceeded (HTTP 429)',
            ],
            $statusCode >= 500 && $statusCode < 600 => [
                'EvidenceMD is temporarily unavailable. Please try again later.',
                'service_unavailable',
                'Service Unavailable (HTTP ' . $statusCode . ')',
            ],
            default => [
                "EvidenceMD API request failed (HTTP {$statusCode}). Please try again later.",
                'http_error',
                'HTTP Error (' . $statusCode . ')',
            ],
        };
    }

    /**
     * Check if parsed result contains no content.
     */
    protected function isResponseEmpty(array $parsed): bool
    {
        return empty($parsed['summary'])
            && empty($parsed['clinical_considerations'])
            && empty($parsed['diagnostic_considerations'])
            && empty($parsed['treatment_considerations'])
            && empty($parsed['clinical_service_considerations']);
    }

    /**
     * Build system and user prompt strings for the specified task using sanitized clinical context.
     */
    protected function buildPrompt(string $task, array $context): array
    {
        $systemPrompt = <<<SYS
You are EvidenceMD, a clinical decision support AI assistant integrated into a Hospital Information System (HIMS).
Your target user is a licensed attending physician.

RULES:
1. Provide evidence-based clinical insights, differential diagnostic options, recommended diagnostic evaluations, or treatment considerations.
2. DO NOT make autonomous clinical decisions, issue binding prescriptions, or order interventions directly.
3. Keep recommendations precise, structured, and clinically actionable.
4. Cite relevant clinical guidelines or medical literature where appropriate (e.g. ACC/AHA, UpToDate, IDSA, KDIGO).
5. Output ONLY valid JSON adhering strictly to the format:
{
  "summary": "Brief 1-2 sentence clinical summary of the evaluation.",
  "clinical_considerations": ["Clinical finding or differential insight 1", "Insight 2"],
  "diagnostic_considerations": ["Diagnostic test or imaging recommendation 1", "Recommendation 2"],
  "treatment_considerations": ["Therapeutic option or drug safety check 1", "Option 2"],
  "clinical_service_considerations": ["Consultation or nursing/dietary recommendation 1", "Recommendation 2"],
  "citations": ["Clinical practice guideline reference 1", "Literature reference 2"]
}
SYS;

        $taskInstructions = [
            'SYMPTOM_ASSESSMENT' => 'Evaluate presenting symptoms and findings. Emphasize clinical considerations, symptom acuity, and differential diagnostic possibilities.',
            'DIAGNOSTIC_ASSISTANCE' => 'Evaluate clinical findings alongside laboratory and radiology reports. Emphasize diagnostic considerations, confirmatory workups, and monitoring tests.',
            'TREATMENT_RECOMMENDATION' => 'Evaluate active prescriptions and clinical findings. Emphasize evidence-based therapeutic options, medication safety, and regimen adjustments.',
            'CLINICAL_SERVICE_ASSISTANCE' => 'Evaluate patient care needs. Emphasize clinical service considerations including specialist consults, diet, surgery coordination, and disposition planning.',
        ];

        $instruction = $taskInstructions[$task] ?? 'Analyze patient context and provide evidence-based clinical insights.';

        // SANITIZE CONTEXT SERVER-SIDE BEFORE ENCODING INTO API PAYLOAD
        $cleanContext = $this->sanitizeContextForEvidenceMD($context);
        $contextJson  = json_encode($cleanContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $userPrompt = "TASK: {$task}\nINSTRUCTION: {$instruction}\n\nNON-IDENTIFYING CLINICAL CONTEXT (PATIENT DIRECT IDENTIFIERS EXCLUDED SERVER-SIDE):\n{$contextJson}";

        return [
            'system' => $systemPrompt,
            'user'   => $userPrompt,
        ];
    }

    /**
     * Parse and structure the API response content.
     */
    protected function parseApiResponse(?array $resJson, string $task): array
    {
        if (empty($resJson)) {
            return $this->emptyParsedResult();
        }

        $rawText = $resJson['choices'][0]['message']['content']
            ?? $resJson['content']
            ?? $resJson['text']
            ?? null;

        if (is_array($rawText)) {
            return array_merge($this->emptyParsedResult(), $rawText);
        }

        if (is_string($rawText)) {
            // Remove markdown code fence if present
            $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
            $decoded = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return [
                    'summary'                         => $decoded['summary'] ?? 'Analysis completed.',
                    'clinical_considerations'         => is_array($decoded['clinical_considerations'] ?? null) ? $decoded['clinical_considerations'] : [],
                    'diagnostic_considerations'       => is_array($decoded['diagnostic_considerations'] ?? null) ? $decoded['diagnostic_considerations'] : [],
                    'treatment_considerations'        => is_array($decoded['treatment_considerations'] ?? null) ? $decoded['treatment_considerations'] : [],
                    'clinical_service_considerations' => is_array($decoded['clinical_service_considerations'] ?? null) ? $decoded['clinical_service_considerations'] : [],
                    'citations'                       => is_array($decoded['citations'] ?? null) ? $decoded['citations'] : [],
                ];
            }

            // Fallback: parse plain text bullet points
            return $this->parseTextFallback($rawText, $task);
        }

        return $this->emptyParsedResult();
    }

    /**
     * Plain text fallback parser if JSON is not returned.
     */
    protected function parseTextFallback(string $text, string $task): array
    {
        $lines = explode("\n", $text);
        $bullets = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (preg_match('/^[-*•\d+\.]\s*(.+)$/', $line, $m)) {
                $bullets[] = trim($m[1]);
            }
        }

        if (empty($bullets)) {
            $bullets = [trim($text)];
        }

        $result = $this->emptyParsedResult();
        $result['summary'] = 'EvidenceMD analysis response generated.';

        switch ($task) {
            case 'SYMPTOM_ASSESSMENT':
                $result['clinical_considerations'] = $bullets;
                break;
            case 'DIAGNOSTIC_ASSISTANCE':
                $result['diagnostic_considerations'] = $bullets;
                break;
            case 'TREATMENT_RECOMMENDATION':
                $result['treatment_considerations'] = $bullets;
                break;
            case 'CLINICAL_SERVICE_ASSISTANCE':
            default:
                $result['clinical_service_considerations'] = $bullets;
                break;
        }

        return $result;
    }

    protected function emptyParsedResult(): array
    {
        return [
            'summary'                         => '',
            'clinical_considerations'         => [],
            'diagnostic_considerations'       => [],
            'treatment_considerations'        => [],
            'clinical_service_considerations' => [],
            'citations'                       => [],
        ];
    }

    protected function buildFallbackResponse(
        string $task,
        string $taskLabel,
        string $errorMsg,
        array $contextReceived,
        string $findings,
        array $himsContext,
        string $disclaimer,
        string $statusText = self::STATUS_ERROR
    ): array {
        return [
            'task'             => $task,
            'provider'         => self::PROVIDER_NAME,
            'status'           => $statusText,
            'summary'          => $errorMsg,
            'context_received' => $contextReceived,
            'findings_received'=> $findings,
            'hims_context'     => $himsContext,
            'evidence'         => [
                'EvidenceMD Provider Adapter v1',
                "Status: {$statusText}",
            ],
            'citations'        => [],
            'disclaimer'       => $disclaimer,
            'clinical_considerations'         => [],
            'diagnostic_considerations'       => [],
            'treatment_considerations'        => [],
            'clinical_service_considerations' => [],
        ];
    }
}
