<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'uploads/*', 'storage/*'],

    'allowed_methods' => ['*'],

    /*
     | Static allowed origins (loaded from .env via CORS_ALLOWED_ORIGINS).
     | Dynamic vendor domains are handled by DynamicCorsOrigin middleware.
     | Example .env: CORS_ALLOWED_ORIGINS=https://admin.selluee.com,http://localhost:3000
     */
    'allowed_origins' => array_filter(
        array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:3001')))
    ),

    /*
     | Regex patterns — matches any subdomain of the platform domain.
     | Example .env: CORS_PLATFORM_DOMAIN=selluee.com
     | This allows *.selluee.com automatically without a DB lookup.
     */
    'allowed_origins_patterns' => env('CORS_PLATFORM_DOMAIN')
        ? ['^https?:\/\/([a-z0-9-]+\.)?' . preg_quote(env('CORS_PLATFORM_DOMAIN'), '/') . '$']
        : [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
