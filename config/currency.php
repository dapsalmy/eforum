<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used throughout the application.
    |
    */
    'default' => env('DEFAULT_CURRENCY', 'NGN'),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Available currencies and their configurations
    |
    */
    'currencies' => [
        'NGN' => [
            'name' => 'Nigerian Naira',
            'code' => 'NGN',
            'symbol' => '₦',
            'symbol_position' => 'before', // before or after
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
        ],
        'USD' => [
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'symbol_position' => 'before',
            'decimal_places' => 2,
            'thousands_separator' => ',',
            'decimal_separator' => '.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for currency exchange rates
    |
    */
    'exchange_rates' => [
        'provider' => env('EXCHANGE_RATE_PROVIDER', 'cbn'), // cbn, manual, api
        'cache_duration' => 3600, // Cache exchange rates for 1 hour
        'fallback_rate' => [
            'USD_TO_NGN' => 1500.00, // Fallback rate if API fails
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nigerian Payment Settings
    |--------------------------------------------------------------------------
    |
    | Settings specific to Nigerian payment processing
    |
    */
    'minimum_amounts' => [
        'NGN' => 100, // Minimum ₦100
        'USD' => 1,   // Minimum $1
    ],
    
    'maximum_amounts' => [
        'NGN' => 10000000, // Maximum ₦10,000,000
        'USD' => 10000,    // Maximum $10,000
    ],
];
