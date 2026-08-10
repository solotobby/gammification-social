<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin gate (obfuscated URL login)
    |--------------------------------------------------------------------------
    |
    | Flow: GET /seniore/login → one-time code → GET /registration/{code} → POST login
    |
    */

    'gate_ttl_minutes' => (int) env('ADMIN_GATE_TTL', 15),

    'gate_path' => env('ADMIN_GATE_PATH', 'seniore/login'),

    'registration_path' => env('ADMIN_REGISTRATION_PATH', 'registration'),

    /*
    |--------------------------------------------------------------------------
    | Local development
    |--------------------------------------------------------------------------
    |
    | When APP_ENV=local, skip IP/country whitelist checks and bind gates to a
    | stable generic IP so localhost requests validate consistently.
    |
    */

    'bypass_ip_check_on_local' => env('ADMIN_BYPASS_IP_ON_LOCAL', true),

    'local_client_ip' => env('ADMIN_LOCAL_IP', '127.0.0.1'),

];
