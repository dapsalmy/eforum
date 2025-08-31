<?php

return [
    /**
     * Public Key from Paystack Dashboard
     */
    'publicKey' => env('PAYSTACK_PUBLIC_KEY'),

    /**
     * Secret Key from Paystack Dashboard
     */
    'secretKey' => env('PAYSTACK_SECRET_KEY'),

    /**
     * Paystack Payment URL
     */
    'paymentUrl' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),

    /**
     * Optional email address of the merchant
     */
    'merchantEmail' => env('PAYSTACK_MERCHANT_EMAIL', 'admin@eforum.ng'),

    /**
     * Default currency
     */
    'currency' => env('PAYSTACK_CURRENCY', 'NGN'),

    /**
     * Webhook URL
     */
    'webhookUrl' => env('PAYSTACK_WEBHOOK_URL', '/api/paystack/webhook'),

    /**
     * Transaction charge parameters
     */
    'charge' => [
        'percentage' => env('PAYSTACK_CHARGE_PERCENTAGE', 1.5),
        'cap' => env('PAYSTACK_CHARGE_CAP', 2000), // Maximum charge in kobo
        'additional_charge' => env('PAYSTACK_ADDITIONAL_CHARGE', 10000), // Additional charge in kobo if above threshold
        'threshold' => env('PAYSTACK_THRESHOLD', 250000), // Threshold in kobo
    ],

    /**
     * Channels to enable
     */
    'channels' => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
];
