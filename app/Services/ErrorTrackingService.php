<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ErrorTrackingService
{
    /**
     * Report an error to the tracking service
     */
    public static function report(Throwable $exception, array $context = [], array $tags = [])
    {
        if (Auth::check()) {
            $context['user'] = [
                'id' => Auth::id(),
                'email' => Auth::user()->email,
                'username' => Auth::user()->username,
            ];
        }

        if (request()) {
            $context['request'] = [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ];
        }

        $context['environment'] = [
            'app_env' => config('app.env'),
            'app_version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        Log::error($exception->getMessage(), [
            'exception' => $exception,
            'context' => $context,
            'tags' => $tags,
            'trace' => $exception->getTraceAsString(),
        ]);

        if (function_exists('sentry_capture_exception')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($context, $tags) {
                if (isset($context['user'])) {
                    $scope->setUser($context['user']);
                }

                foreach ($tags as $key => $value) {
                    $scope->setTag($key, $value);
                }

                $scope->setContext('application', $context);
            });

            sentry_capture_exception($exception);
        }
    }

    /**
     * Report a custom message
     */
    public static function reportMessage(string $message, string $level = 'error', array $context = [], array $tags = [])
    {
        if (Auth::check()) {
            $context['user'] = [
                'id' => Auth::id(),
                'email' => Auth::user()->email,
                'username' => Auth::user()->username,
            ];
        }

        Log::log($level, $message, [
            'context' => $context,
            'tags' => $tags,
        ]);

        if (function_exists('sentry_capture_message')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($context, $tags) {
                if (isset($context['user'])) {
                    $scope->setUser($context['user']);
                }

                foreach ($tags as $key => $value) {
                    $scope->setTag($key, $value);
                }

                $scope->setContext('application', $context);
            });

            sentry_capture_message($message, $level);
        }
    }

    /**
     * Report payment-related errors
     */
    public static function reportPaymentError(Throwable $exception, array $paymentData = [])
    {
        self::report($exception, [
            'payment_data' => $paymentData,
            'category' => 'payment',
        ], [
            'component' => 'payment',
            'severity' => 'high',
        ]);
    }

    /**
     * Report authentication errors
     */
    public static function reportAuthError(string $message, array $context = [])
    {
        self::reportMessage($message, 'warning', array_merge($context, [
            'category' => 'authentication',
        ]), [
            'component' => 'auth',
            'severity' => 'medium',
        ]);
    }

    /**
     * Report security-related incidents
     */
    public static function reportSecurityIncident(string $message, array $context = [])
    {
        self::reportMessage($message, 'critical', array_merge($context, [
            'category' => 'security',
            'timestamp' => now()->toISOString(),
        ]), [
            'component' => 'security',
            'severity' => 'critical',
        ]);
    }

    /**
     * Report API errors
     */
    public static function reportApiError(Throwable $exception, array $apiContext = [])
    {
        self::report($exception, array_merge($apiContext, [
            'category' => 'api',
        ]), [
            'component' => 'api',
            'severity' => 'medium',
        ]);
    }

    /**
     * Report database errors
     */
    public static function reportDatabaseError(Throwable $exception, array $queryContext = [])
    {
        self::report($exception, array_merge($queryContext, [
            'category' => 'database',
        ]), [
            'component' => 'database',
            'severity' => 'high',
        ]);
    }
}
