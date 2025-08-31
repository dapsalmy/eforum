<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('ratelimit.api.general.attempts', 60))
                ->by($request->user()?->id ?: $request->ip());
        });

        // Authentication rate limiting
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(config('ratelimit.auth.login.attempts', 5))
                ->by($request->email.$request->ip())
                ->response(function () {
                    return response()->json(['message' => 'Too many login attempts. Please try again later.'], 429);
                });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(config('ratelimit.auth.register.attempts', 3))
                ->by($request->ip())
                ->response(function () {
                    return response()->json(['message' => 'Registration limit reached. Please try again later.'], 429);
                });
        });

        // Forum actions rate limiting
        RateLimiter::for('post-create', function (Request $request) {
            return Limit::perHour(config('ratelimit.forum.post_create.attempts', 10))
                ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('comment-create', function (Request $request) {
            return Limit::perHour(config('ratelimit.forum.comment_create.attempts', 20))
                ->by($request->user()?->id ?: $request->ip());
        });

        // Messaging rate limiting
        RateLimiter::for('send-message', function (Request $request) {
            return Limit::perHour(config('ratelimit.messaging.send_message.attempts', 30))
                ->by($request->user()?->id ?: $request->ip());
        });

        // Upload rate limiting
        RateLimiter::for('upload', function (Request $request) {
            return Limit::perHour(config('ratelimit.uploads.image.attempts', 10))
                ->by($request->user()?->id ?: $request->ip());
        });

        // Report rate limiting
        RateLimiter::for('report', function (Request $request) {
            return Limit::perHour(config('ratelimit.reports.report_content.attempts', 5))
                ->by($request->user()?->id ?: $request->ip());
        });
    }
}
