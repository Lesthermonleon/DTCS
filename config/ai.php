<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MediSense AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Stage 1 defaults to 'mock' provider. Provider-independent architecture
    | allows seamless switching to EvidenceMD or other AI APIs in future stages
    | without modifying MediSense controllers or views.
    |
    */

    'provider' => env('MEDISENSE_PROVIDER', 'mock'),

    'api_key'  => env('MEDISENSE_API_KEY', ''),

    'model'    => env('MEDISENSE_MODEL', 'evidencemd-v1'),

    'base_url' => env('MEDISENSE_BASE_URL', ''),

    'evidencemd' => [
        'api_key'  => env('EVIDENCEMD_API_KEY') ?: env('MEDISENSE_API_KEY', ''),
        'base_url' => env('EVIDENCEMD_BASE_URL') ?: env('MEDISENSE_BASE_URL', 'https://evidencemd.ai/api/v1'),
        'model'    => env('EVIDENCEMD_MODEL') ?: env('MEDISENSE_MODEL', 'evidencemd-pro'),
        'timeout'  => env('EVIDENCEMD_TIMEOUT', 60),
    ],

];
