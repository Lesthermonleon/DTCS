<?php

namespace App\Services;

/**
 * GoogleWebSearchService — Search grounding placeholder.
 * External search grounding and query anonymization for AI API providers have been removed.
 */
class GoogleWebSearchService
{
    public function getGroundingToolDefinition(): array
    {
        return [];
    }

    public function anonymizeQuery(string $query, ?array $patientInfo = null): string
    {
        return $query;
    }

    public function extractCitations(array $responseData): array
    {
        return ['citations' => []];
    }
}
