<?php

namespace App\Services;

use App\Models\User;

/**
 * HimsToolProvider — Function tool declarations placeholder.
 * Automated AI tool declarations and LLM function invocation have been removed.
 */
class HimsToolProvider
{
    public function getToolDeclarations(): array
    {
        return [];
    }

    public function executeTool(string $functionName, array $arguments, User $user): array
    {
        return [
            'success' => false,
            'message' => 'AI tool execution is currently disabled.',
        ];
    }
}
