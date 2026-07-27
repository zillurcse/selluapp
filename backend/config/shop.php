<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform Domain
    |--------------------------------------------------------------------------
    |
    | The base domain used for vendor subdomains (e.g. mystore.selluee.com).
    |
    */
    'platform_domain' => env('SHOP_PLATFORM_DOMAIN', env('CORS_PLATFORM_DOMAIN', 'selluee.test')),

    /*
    |--------------------------------------------------------------------------
    | Platform IP (optional)
    |--------------------------------------------------------------------------
    |
    | Shown in DNS instructions for apex/root domain A records.
    |
    */
    'platform_ip' => env('SHOP_PLATFORM_IP', ''),

    /*
    |--------------------------------------------------------------------------
    | Reserved Subdomains
    |--------------------------------------------------------------------------
    */
    'reserved_subdomains' => [
        'www', 'api', 'admin', 'app', 'mail', 'ftp', 'cdn', 'static',
        'support', 'help', 'blog', 'shop', 'store', 'dev', 'staging',
    ],

];
