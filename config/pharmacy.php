<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Medication Stock Provider
    |--------------------------------------------------------------------------
    |
    | Defines the active inventory stock provider implementation.
    | Options: 'mock', 'api' (future integration)
    |
    */

    'stock_provider' => env('PHARMACY_STOCK_PROVIDER', 'mock'),

];
