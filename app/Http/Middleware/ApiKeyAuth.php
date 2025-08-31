<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->header('Authorization');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
                'errors' => []
            ], 401);
        }

        // Remove 'Bearer ' prefix if present
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Find the API key
        $key = ApiKey::where('key', $apiKey)->first();

        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'errors' => []
            ], 401);
        }

        // Check if key is active
        if (!$key->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'API key is not active',
                'errors' => []
            ], 401);
        }

        // Check rate limiting
        $rateLimitKey = 'api_key_' . $key->id;
        $maxAttempts = $key->rate_limit ?? 120;

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Try again in ' . $seconds . ' seconds.',
                'errors' => []
            ], 429);
        }

        RateLimiter::hit($rateLimitKey);

        // Update last used timestamp
        $key->updateLastUsed();

        // Add user and API key to request
        $request->merge(['api_user' => $key->user]);
        $request->merge(['api_key' => $key]);

        return $next($request);
    }
}
