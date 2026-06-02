<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Flutter Web memanggil API dari origin lain (mis. localhost). Path
    | `api/*` dan `availability-data` harus tercakup agar browser tidak memblokir.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'availability-data'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
