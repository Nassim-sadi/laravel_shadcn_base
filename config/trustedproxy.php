<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxies
    |--------------------------------------------------------------------------
    |
    | Set the trusted proxies for your application. This is important when
    | running Laravel behind a reverse proxy (e.g., Cloudflare, Nginx).
    |
    | Options:
    |   - '*' — trust all proxies (not recommended for production)
    |   - ['192.168.1.1', '10.0.0.1'] — trust specific IPs
    |   - Illuminate\Http\Request::HEADER_X_FORWARDED_FOR — trust Cloudflare
    |
    */

    'proxies' => env('TRUSTED_PROXIES', null),

    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    |
    | Determine which headers should be trusted from proxies.
    |
    | Use ALL for Cloudflare:
    |   Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
    |   Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
    |   Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
    |   Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
    |
    */

    'headers' => env('TRUSTED_PROXIES_HEADERS',
        Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
        Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
    ),

];
