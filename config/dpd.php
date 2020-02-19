<?php

declare(strict_types=1);

return [
    /*
     * API Details used for the internal client.
     */
    'api' => [
        'url'        => env('DPD_API_URL', 'https://api.dpd.co.uk/'),
        'timeout'    => env('DPD_API_TIMEOUT', 5.0),
        'should_log' => env('DPD_API_SHOULD_LOG', true),
    ],

    /*
     * Authentication details for the DPD integration.
     */
    'auth' => [
        'account_id' => env('DPD_AUTH_ACCOUNT_ID'),
        'username'   => env('DPD_AUTH_USERNAME'),
        'password'   => env('DPD_AUTH_PASSWORD'),
    ],

    /*
     * Settings for keeping the tokens alive.
     * Please note this is just to store the cache, if the token becomes invalid after
     * any timeframe it will automatically be regenerated and added back with a fresh TTL.
     *
     * Set to null to disable.
     */
    'token_cache_ttl' => 3600 * 24, // Attempt to keep it for a day if possible.
];
