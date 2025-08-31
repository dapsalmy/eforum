<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\VisaController;
use App\Http\Controllers\Api\ApiKeyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::prefix('v1')->group(function () {
    
    // Public Auth Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Public Job Routes
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/categories', [JobController::class, 'categories']);
    Route::get('/jobs/{slug}', [JobController::class, 'show']);

    // Public Visa Routes
    Route::get('/visa-trackings', [VisaController::class, 'index']);
    Route::get('/visa-trackings/{id}', [VisaController::class, 'show']);
    Route::get('/visa-trackings/statistics', [VisaController::class, 'statistics']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth Routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);

        // Job Routes
        Route::post('/jobs', [JobController::class, 'store']);
        Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);
        Route::post('/jobs/{id}/save', [JobController::class, 'toggleSave']);
        Route::get('/my/jobs', [JobController::class, 'myJobs']);
        Route::get('/my/job-applications', [JobController::class, 'myApplications']);
        Route::get('/my/saved-jobs', [JobController::class, 'savedJobs']);

        // Visa Tracking Routes
        Route::post('/visa-trackings', [VisaController::class, 'store']);
        Route::put('/visa-trackings/{id}', [VisaController::class, 'update']);
        Route::delete('/visa-trackings/{id}', [VisaController::class, 'destroy']);
        Route::get('/my/visa-trackings', [VisaController::class, 'myTrackings']);
        Route::post('/visa-trackings/{id}/timeline', [VisaController::class, 'addTimelineEvent']);
        Route::put('/visa-trackings/{id}/checklist', [VisaController::class, 'updateChecklist']);

        // API Key Management Routes
        Route::get('/api-keys', [ApiKeyController::class, 'index']);
        Route::post('/api-keys', [ApiKeyController::class, 'store']);
        Route::get('/api-keys/{id}', [ApiKeyController::class, 'show']);
        Route::put('/api-keys/{id}', [ApiKeyController::class, 'update']);
        Route::delete('/api-keys/{id}', [ApiKeyController::class, 'destroy']);
        Route::get('/api-keys/{id}/key', [ApiKeyController::class, 'getKey']);
    });
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String()
    ]);
});