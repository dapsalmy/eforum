<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),
    'release' => env('SENTRY_RELEASE'),
    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),
    'sample_rate' => (float) (env('SENTRY_SAMPLE_RATE', 1.0)),
    'traces_sample_rate' => (float) (env('SENTRY_TRACES_SAMPLE_RATE', 0.0)),
    'send_default_pii' => env('SENTRY_SEND_DEFAULT_PII', false),
    'breadcrumbs' => [
        'logs' => true,
        'cache' => true,
        'livewire' => true,
        'sql_queries' => true,
        'sql_bindings' => true,
        'sql_transactions' => true,
        'command_info' => true,
    ],
    'tracing' => [
        'queue_job_transactions' => env('SENTRY_TRACE_QUEUE_ENABLED', false),
        'queue_jobs' => true,
        'sql_queries' => true,
        'requests' => true,
        'default_integrations' => true,
    ],
];
