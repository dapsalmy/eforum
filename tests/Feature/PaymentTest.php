<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Deposits;
use App\Services\PaystackService;
use App\Services\FlutterwaveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

class PaymentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'wallet' => 1000.00,
        ]);
    }

    public function test_user_can_initiate_paystack_payment()
    {
        $this->actingAs($this->user);

        $mockPaystackService = Mockery::mock(PaystackService::class);
        $mockPaystackService->shouldReceive('initializeTransaction')
            ->once()
            ->andReturn([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test123',
                    'reference' => 'TEST_REF_123'
                ]
            ]);

        $this->app->instance(PaystackService::class, $mockPaystackService);

        $response = $this->post('/user/payment/initiate', [
            'amount' => 5000,
            'gateway' => 'paystack',
            'purpose' => 'deposit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'payment_url',
                'reference'
            ]
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 5000,
            'gateway' => 'paystack',
            'purpose' => 'deposit',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_initiate_flutterwave_payment()
    {
        $this->actingAs($this->user);

        $mockFlutterwaveService = Mockery::mock(FlutterwaveService::class);
        $mockFlutterwaveService->shouldReceive('initializePayment')
            ->once()
            ->andReturn([
                'status' => 'success',
                'data' => [
                    'link' => 'https://checkout.flutterwave.com/test123'
                ]
            ]);

        $this->app->instance(FlutterwaveService::class, $mockFlutterwaveService);

        $response = $this->post('/user/payment/initiate', [
            'amount' => 3000,
            'gateway' => 'flutterwave',
            'purpose' => 'deposit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'payment_url',
                'reference'
            ]
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 3000,
            'gateway' => 'flutterwave',
            'purpose' => 'deposit',
            'status' => 'pending',
        ]);
    }

    public function test_payment_validation_fails_with_invalid_amount()
    {
        $this->actingAs($this->user);

        $response = $this->post('/user/payment/initiate', [
            'amount' => 50, // Below minimum of 100
            'gateway' => 'paystack',
            'purpose' => 'deposit',
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['amount']);
    }

    public function test_payment_validation_fails_with_invalid_gateway()
    {
        $this->actingAs($this->user);

        $response = $this->post('/user/payment/initiate', [
            'amount' => 5000,
            'gateway' => 'invalid_gateway',
            'purpose' => 'deposit',
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['gateway']);
    }

    public function test_successful_paystack_callback_processes_payment()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 5000,
            'currency' => 'NGN',
            'gateway' => 'paystack',
            'purpose' => 'deposit',
            'status' => 'pending',
            'reference' => 'TEST_REF_123',
            'metadata' => json_encode([]),
        ]);

        $mockPaystackService = Mockery::mock(PaystackService::class);
        $mockPaystackService->shouldReceive('verifyTransaction')
            ->with('TEST_REF_123')
            ->once()
            ->andReturn([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 500000, // Paystack returns amount in kobo
                    'reference' => 'TEST_REF_123',
                ]
            ]);

        $this->app->instance(PaystackService::class, $mockPaystackService);

        $response = $this->get('/user/payment/callback/paystack?reference=TEST_REF_123');

        $response->assertRedirect('/user/wallet');
        $response->assertSessionHas('success', 'Payment successful!');

        $this->assertDatabaseHas('transactions', [
            'reference' => 'TEST_REF_123',
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('deposits', [
            'user_id' => $this->user->id,
            'amount' => 5000,
            'transaction_id' => 'TEST_REF_123',
            'status' => 1,
        ]);

        $this->assertEquals(6000, $this->user->fresh()->wallet);
    }

    public function test_failed_payment_callback_handles_gracefully()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 5000,
            'currency' => 'NGN',
            'gateway' => 'paystack',
            'purpose' => 'deposit',
            'status' => 'pending',
            'reference' => 'TEST_REF_FAILED',
            'metadata' => json_encode([]),
        ]);

        $mockPaystackService = Mockery::mock(PaystackService::class);
        $mockPaystackService->shouldReceive('verifyTransaction')
            ->with('TEST_REF_FAILED')
            ->once()
            ->andReturn([
                'status' => false,
                'message' => 'Transaction failed'
            ]);

        $this->app->instance(PaystackService::class, $mockPaystackService);

        $response = $this->get('/user/payment/callback/paystack?reference=TEST_REF_FAILED');

        $response->assertRedirect('/user/wallet');
        $response->assertSessionHas('error', 'Payment verification failed');

        $this->assertDatabaseHas('transactions', [
            'reference' => 'TEST_REF_FAILED',
            'status' => 'failed',
        ]);

        $this->assertEquals(1000, $this->user->fresh()->wallet);
    }

    public function test_withdrawal_initiation_with_valid_data()
    {
        $this->actingAs($this->user);

        $mockPaystackService = Mockery::mock(PaystackService::class);
        $mockPaystackService->shouldReceive('resolveAccountNumber')
            ->once()
            ->andReturn(['status' => true]);

        $this->app->instance(PaystackService::class, $mockPaystackService);

        $response = $this->post('/user/withdrawal/initiate', [
            'amount' => 500,
            'bank_code' => '044',
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
            'gateway' => 'paystack',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Withdrawal request submitted successfully'
        ]);

        $this->assertDatabaseHas('withdraws', [
            'user_id' => $this->user->id,
            'amount' => 500,
            'account_number' => '1234567890',
            'status' => 0, // Pending
        ]);

        $this->assertEquals(500, $this->user->fresh()->wallet);
    }

    public function test_withdrawal_fails_with_insufficient_balance()
    {
        $this->actingAs($this->user);

        $response = $this->post('/user/withdrawal/initiate', [
            'amount' => 2000, // More than wallet balance
            'bank_code' => '044',
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
            'gateway' => 'paystack',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Insufficient wallet balance'
        ]);

        $this->assertEquals(1000, $this->user->fresh()->wallet);
    }

    public function test_amount_mismatch_prevents_payment_processing()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'amount' => 5000,
            'currency' => 'NGN',
            'gateway' => 'paystack',
            'purpose' => 'deposit',
            'status' => 'pending',
            'reference' => 'TEST_REF_MISMATCH',
            'metadata' => json_encode([]),
        ]);

        $mockPaystackService = Mockery::mock(PaystackService::class);
        $mockPaystackService->shouldReceive('verifyTransaction')
            ->with('TEST_REF_MISMATCH')
            ->once()
            ->andReturn([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 300000, // Different amount (3000 instead of 5000)
                    'reference' => 'TEST_REF_MISMATCH',
                ]
            ]);

        $this->app->instance(PaystackService::class, $mockPaystackService);

        $response = $this->get('/user/payment/callback/paystack?reference=TEST_REF_MISMATCH');

        $response->assertRedirect('/user/wallet');
        $response->assertSessionHas('error', 'Payment processing failed. Please contact support.');

        $this->assertDatabaseHas('transactions', [
            'reference' => 'TEST_REF_MISMATCH',
            'status' => 'failed',
        ]);

        $this->assertEquals(1000, $this->user->fresh()->wallet);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
