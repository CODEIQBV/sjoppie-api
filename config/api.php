<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the API.
    |
    */

    'api_key' => env('API_KEY', 'your-default-api-key-here'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The current version of the API.
    |
    */
    'version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure the rate limiting for the API.
    |
    */
    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
];
