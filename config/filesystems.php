<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', get_setting('aws_access_key_id')),
            'secret' => env('AWS_SECRET_ACCESS_KEY', get_setting('aws_secret_access_key')),
            'region' => env('AWS_DEFAULT_REGION', get_setting('aws_default_region', 'us-east-1')),
            'bucket' => env('AWS_BUCKET', get_setting('aws_bucket')),
            'url' => env('AWS_URL', get_setting('cdn_url')),
            'endpoint' => env('AWS_ENDPOINT', get_setting('aws_endpoint')),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        'wasabi' => [
            'driver' => 's3',
            'key' => env('WASABI_ACCESS_KEY_ID', get_setting('wasabi_access_key_id')),
            'secret' => env('WASABI_SECRET_ACCESS_KEY', get_setting('wasabi_secret_access_key')),
            'region' => env('WASABI_DEFAULT_REGION', get_setting('wasabi_default_region', 'us-east-1')),
            'bucket' => env('WASABI_BUCKET', get_setting('wasabi_bucket')),
            'url' => env('WASABI_URL', get_setting('cdn_url')),
            'endpoint' => env('WASABI_ENDPOINT', get_setting('wasabi_endpoint', 'https://s3.wasabisys.com')),
            'use_path_style_endpoint' => true,
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
