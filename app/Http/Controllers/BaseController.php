<?php

namespace App\Http\Controllers;

use App\Traits\ValidatesInput;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ValidatesInput;

    /**
     * Success response method
     */
    protected function successResponse($data, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error response method
     */
    protected function errorResponse(string $message, array $errors = [], int $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    /**
     * Log security event
     */
    protected function logSecurityEvent(string $event, array $data = []): void
    {
        Log::channel('security')->warning("Security Event: {$event}", array_merge([
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ], $data));
    }

    /**
     * Check if user is verified
     */
    protected function isUserVerified(): bool
    {
        return auth()->check() && auth()->user()->verified == 1;
    }

    /**
     * Check if feature is available for user's subscription
     */
    protected function hasFeatureAccess(string $feature): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $subscription = auth()->user()->subscription();
        
        if (!$subscription) {
            return false;
        }

        return $subscription->$feature == 1;
    }

    /**
     * Validate CSRF token for AJAX requests
     */
    protected function validateCSRF(): bool
    {
        $token = request()->header('X-CSRF-TOKEN');
        
        if (!$token) {
            $token = request()->input('_token');
        }

        return $token && hash_equals(session()->token(), $token);
    }
}
