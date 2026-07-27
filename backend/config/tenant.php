<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Vendor ID (development only)
    |--------------------------------------------------------------------------
    |
    | Fallback tenant when X-Vendor-Id / X-Tenant-Domain headers are missing.
    | Leave empty in production — tenant resolution should fail explicitly.
    |
    */
    'default_vendor_id' => env('DEFAULT_VENDOR_ID'),

];
