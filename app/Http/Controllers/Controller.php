<?php

namespace App\Http\Controllers;

use App\Traits\ValidatesInput;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
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
        Log::warning("Security Event: {$event}", array_merge([
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ], $data));
    }
}
