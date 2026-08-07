<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default facility slug
    |--------------------------------------------------------------------------
    |
    | Used when the request host does not match a facility custom_domain /
    | subdomain (e.g. staging on isoverse.ai/storagesoftai).
    |
    */
    'default_facility_slug' => env('DEFAULT_FACILITY_SLUG', '282-storage'),
];
