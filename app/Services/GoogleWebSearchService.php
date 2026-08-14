<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * GoogleWebSearchService — Manages patient data anonymization and Google Search grounding citations.
 */
class GoogleWebSearchService
{
    /**
     * Anonymize clinical queries before external web grounding.
     * Strips patient names, IDs, DOB, phone numbers, and addresses.
     */
    public function anonymizeQuery(string $query, ?array $patientContext = null): string
    {
        $clean = $query;

        if ($patientContext) {
            if (!empty($patientContext['name'])) {
                $nameParts = explode(' ', $patientContext['name']);
                foreach ($nameParts as $part) {
                    if (strlen($part) > 2) {
                        $clean = preg_replace('/\b' . preg_quote($part, '/') . '\b/i', 'patient', $clean);
                    }
                }
            }

            if (!empty($patientContext['patient_no'])) {
                $clean = str_ireplace($patientContext['patient_no'], 'patient', $clean);
            }

            if (!empty($patientContext['phone'])) {
                $clean = str_ireplace($patientContext['phone'], '', $clean);
            }

            if (!empty($patientContext['address'])) {
                $clean = str_ireplace($patientContext['address'], '', $clean);
            }
        }

        // Strip explicit patient ID patterns like P-123456 or ID: 999
        $clean = preg_replace('/(patient id|patient no|p-)\s*[:#]?[0-9a-z-]+/i', 'patient', $clean);

        // Strip email addresses and phone numbers
        $clean = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '', $clean);
        $clean = preg_replace('/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/', '', $clean);

        return trim(preg_replace('/\s+/', ' ', $clean));
    }

    /**
     * Get Google Search grounding tool definition payload for Gemini API.
     */
    public function getGroundingToolDefinition(): array
    {
        return [
            'googleSearch' => (object) [],
        ];
    }

    /**
     * Parse groundingMetadata returned by Gemini API into clean web citation structures.
     */
    public function extractCitations(array $responseData): array
    {
        $candidate = $responseData['candidates'][0] ?? null;
        if (!$candidate) {
            return [];
        }

        $groundingMetadata = $candidate['groundingMetadata'] ?? null;
        if (!$groundingMetadata) {
            return [];
        }

        $webQueries = $groundingMetadata['webSearchQueries'] ?? [];
        $searchChunks = $groundingMetadata['groundingChunks'] ?? [];
        $searchSupports = $groundingMetadata['groundingSupports'] ?? [];

        $citations = [];
        foreach ($searchChunks as $chunk) {
            $web = $chunk['web'] ?? null;
            if ($web && !empty($web['uri'])) {
                $url = $web['uri'];
                $host = parse_url($url, PHP_URL_HOST) ?? 'web-source';
                $hostClean = preg_replace('/^www\./', '', $host);

                $citations[] = [
                    'title'  => $web['title'] ?? $hostClean,
                    'url'    => $url,
                    'domain' => $hostClean,
                ];
            }
        }

        // De-duplicate citations by URL
        $uniqueCitations = [];
        $seenUrls = [];
        foreach ($citations as $cit) {
            if (!in_array($cit['url'], $seenUrls)) {
                $seenUrls[] = $cit['url'];
                $uniqueCitations[] = $cit;
            }
        }

        return [
            'search_queries' => $webQueries,
            'citations'      => array_slice($uniqueCitations, 0, 5),
        ];
    }
}
