<?php

return [
    /**
     * Public Key from Flutterwave Dashboard
     */
    'publicKey' => env('FLUTTERWAVE_PUBLIC_KEY'),

    /**
     * Secret Key from Flutterwave Dashboard
     */
    'secretKey' => env('FLUTTERWAVE_SECRET_KEY'),

    /**
     * Encryption Key from Flutterwave Dashboard
     */
    'encryptionKey' => env('FLUTTERWAVE_ENCRYPTION_KEY'),

    /**
     * Flutterwave Payment URL
     */
    'baseUrl' => env('FLUTTERWAVE_BASE_URL', 'https://api.flutterwave.com/v3'),

    /**
     * Default currency
     */
    'currency' => env('FLUTTERWAVE_CURRENCY', 'NGN'),

    /**
     * Webhook Secret Hash
     */
    'webhookSecretHash' => env('FLUTTERWAVE_WEBHOOK_SECRET_HASH'),

    /**
     * Payment options to enable
     */
    'paymentOptions' => [
        'card',
        'account',
        'banktransfer',
        'ussd',
        'qr',
        'mobilemoneyghana',
        'mobilemoneyuganda',
        'mobilemoneyrwanda',
        'mobilemoneyzambia',
        'barter',
        'nqr',
        'credit'
    ],

    /**
     * Custom logo URL
     */
    'logo' => env('FLUTTERWAVE_LOGO', '/uploads/settings/logo.png'),

    /**
     * Custom title
     */
    'title' => env('FLUTTERWAVE_TITLE', 'eForum Nigeria'),
];
