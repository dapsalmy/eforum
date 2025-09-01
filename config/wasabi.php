<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Wasabi Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Wasabi cloud storage service
    |
    */

    'driver' => 's3',
    'key' => env('WASABI_ACCESS_KEY_ID'),
    'secret' => env('WASABI_SECRET_ACCESS_KEY'),
    'region' => env('WASABI_DEFAULT_REGION', 'us-east-1'),
    'bucket' => env('WASABI_BUCKET'),
    'endpoint' => env('WASABI_ENDPOINT', 'https://s3.wasabisys.com'),
    'use_path_style_endpoint' => true,
    'throw' => false,
];
