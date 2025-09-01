<?php

namespace Tests\Feature;

use App\Services\ErrorTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ErrorTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_error_tracking_service_logs_errors()
    {
        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('log')
            ->with('error', 'Test error message', ['test' => 'context'])
            ->once();

        ErrorTrackingService::reportMessage('Test error message', 'error', ['test' => 'context']);
    }

    public function test_payment_error_tracking()
    {
        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('log')
            ->with('error', 'Payment processing failed', \Mockery::type('array'))
            ->once();

        ErrorTrackingService::reportMessage('Payment processing failed', 'error', [
            'gateway' => 'paystack',
            'amount' => 5000,
            'reference' => 'TEST_REF_123'
        ]);
    }

    public function test_auth_error_tracking()
    {
        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('log')
            ->with('warning', 'Authentication failed', \Mockery::type('array'))
            ->once();

        ErrorTrackingService::reportAuthError('Authentication failed', [
            'email' => 'test@example.com',
            'ip' => '127.0.0.1'
        ]);
    }

    public function test_security_incident_tracking()
    {
        Log::shouldReceive('channel')
            ->with('single')
            ->once()
            ->andReturnSelf();
        
        Log::shouldReceive('log')
            ->with('critical', 'Security incident detected', \Mockery::type('array'))
            ->once();

        ErrorTrackingService::reportSecurityIncident('Security incident detected', [
            'type' => 'suspicious_activity',
            'ip' => '192.168.1.1'
        ]);
    }
}
