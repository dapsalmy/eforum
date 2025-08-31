<?php

namespace App\Http\Middleware;

use App\Services\TwoFactorAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireTwoFactorAuthentication
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Check if 2FA is required and not verified
        if ($this->twoFactorService->requiresVerification($user)) {
            // Allow access to 2FA routes
            if ($request->routeIs('two-factor.*', 'logout')) {
                return $next($request);
            }

            return redirect()->route('two-factor.verify');
        }

        // Check if verification has expired
        if ($user->two_factor_enabled && $this->twoFactorService->verificationExpired()) {
            session()->forget(['2fa_verified', '2fa_verified_at']);
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }
}
