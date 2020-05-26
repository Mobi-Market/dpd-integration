<?php

declare(strict_types=1);

return [
    /*
     * API Details used for the internal client.
     */
    'api' => [
        'url'        => env('DPDLOCAL_API_URL', 'https://api.dpdlocal.co.uk/'),
        'timeout'    => (float) env('DPDLOCAL_API_TIMEOUT', 30.0),
        'should_log' => env('DPDLOCAL_API_SHOULD_LOG', true),
    ],

    /*
     * Authentication details for the DPD integration.
     */
    'auth' => [
        'account_id' => env('DPDLOCAL_AUTH_ACCOUNT_ID'),
        'username'   => env('DPDLOCAL_AUTH_USERNAME'),
        'password'   => env('DPDLOCAL_AUTH_PASSWORD'),
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
