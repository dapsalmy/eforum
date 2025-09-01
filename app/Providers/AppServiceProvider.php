<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Sentry logging channel if DSN present
        if (env('SENTRY_LARAVEL_DSN')) {
            config(['logging.channels.stack.channels' => array_unique(array_merge(
                config('logging.channels.stack.channels', ['single']),
                ['sentry']
            ))]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Carbon::setLocale(env('DATE_LANGUAGE', 'en'));

        // Enforce HTTPS URLs in production
        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        // Production safeguard: never allow debug to be true
        if (App::environment('production') && config('app.debug')) {
            config(['app.debug' => false]);
        }
    }
}
