<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | This file defines the rate limiting rules for eForum to prevent abuse
    | and ensure fair usage of the platform.
    |
    */

    // Authentication endpoints
    'auth' => [
        'login' => [
            'attempts' => 5,
            'decay_minutes' => 1,
        ],
        'register' => [
            'attempts' => 3,
            'decay_minutes' => 60,
        ],
        'password_reset' => [
            'attempts' => 3,
            'decay_minutes' => 60,
        ],
    ],

    // Forum actions
    'forum' => [
        'post_create' => [
            'attempts' => 10,
            'decay_minutes' => 60,
        ],
        'comment_create' => [
            'attempts' => 20,
            'decay_minutes' => 60,
        ],
        'reply_create' => [
            'attempts' => 30,
            'decay_minutes' => 60,
        ],
    ],

    // API endpoints
    'api' => [
        'general' => [
            'attempts' => 60,
            'decay_minutes' => 1,
        ],
        'search' => [
            'attempts' => 30,
            'decay_minutes' => 1,
        ],
    ],

    // Messaging
    'messaging' => [
        'send_message' => [
            'attempts' => 30,
            'decay_minutes' => 60,
        ],
        'create_chat' => [
            'attempts' => 5,
            'decay_minutes' => 60,
        ],
    ],

    // File uploads
    'uploads' => [
        'image' => [
            'attempts' => 10,
            'decay_minutes' => 60,
        ],
        'attachment' => [
            'attempts' => 5,
            'decay_minutes' => 60,
        ],
    ],

    // Reports and flags
    'reports' => [
        'report_content' => [
            'attempts' => 5,
            'decay_minutes' => 60,
        ],
        'report_user' => [
            'attempts' => 3,
            'decay_minutes' => 60,
        ],
    ],
];
