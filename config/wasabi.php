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
    'key' => env('WASABI_ACCESS_KEY_ID', get_setting('wasabi_access_key_id')),
    'secret' => env('WASABI_SECRET_ACCESS_KEY', get_setting('wasabi_secret_access_key')),
    'region' => env('WASABI_DEFAULT_REGION', get_setting('wasabi_default_region', 'us-east-1')),
    'bucket' => env('WASABI_BUCKET', get_setting('wasabi_bucket')),
    'endpoint' => env('WASABI_ENDPOINT', get_setting('wasabi_endpoint', 'https://s3.wasabisys.com')),
    'use_path_style_endpoint' => true,
    'throw' => false,
];
