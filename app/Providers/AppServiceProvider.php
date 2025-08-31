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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        Carbon::setLocale(env('DATE_LANGUAGE'));
        Validator::extend('recaptcha', 'App\\Rules\\ReCaptcha@validate');

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
