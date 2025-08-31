<?php

namespace App\Http\Controllers;

use App\Services\PaystackService;
use App\Services\FlutterwaveService;
use App\Models\Deposits;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Helpers\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NigerianPaymentController extends Controller
{
    protected $paystackService;
    protected $flutterwaveService;

    public function __construct(PaystackService $paystackService, FlutterwaveService $flutterwaveService)
    {
        $this->middleware('auth');
        $this->paystackService = $paystackService;
        $this->flutterwaveService = $flutterwaveService;
    }

    /**
     * Initiate payment with selected gateway
     */
    public function initiatePayment(Request $request)
    {
        $validatedData = $this->validateWithCustomRules($request->all(), [
            'amount' => 'required|numeric|min:100',
            'gateway' => 'required|in:paystack,flutterwave',
            'purpose' => 'required|in:deposit,subscription,buy_points',
            'plan_id' => 'required_if:purpose,subscription',
            'points_package_id' => 'required_if:purpose,buy_points',
        ]);

        if ($validatedData->fails()) {
            return $this->errorResponse('Validation failed', $validatedData->errors()->toArray());
        }

        $user = Auth::user();
        $amount = $request->amount;
        $gateway = $request->gateway;
        
        // Create pending transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'NGN',
            'gateway' => $gateway,
            'purpose' => $request->purpose,
            'status' => 'pending',
            'reference' => $this->generateReference($gateway),
            'metadata' => json_encode($request->only(['plan_id', 'points_package_id'])),
        ]);

        try {
            if ($gateway === 'paystack') {
                $response = $this->paystackService->initializeTransaction([
                    'email' => $user->email,
                    'amount' => $amount,
                    'reference' => $transaction->reference,
                    'callback_url' => route('payment.callback', ['gateway' => 'paystack']),
                    'metadata' => [
                        'user_id' => $user->id,
                        'transaction_id' => $transaction->id,
                        'purpose' => $request->purpose,
                    ],
                ]);

                if ($response['status']) {
                    return $this->successResponse([
                        'payment_url' => $response['data']['authorization_url'],
                        'reference' => $transaction->reference,
                    ], 'Payment initialized successfully');
                }
            } else {
                $response = $this->flutterwaveService->initializePayment([
                    'email' => $user->email,
                    'amount' => $amount,
                    'name' => $user->name,
                    'phone' => $user->formatted_phone,
                    'reference' => $transaction->reference,
                    'callback_url' => route('payment.callback', ['gateway' => 'flutterwave']),
                    'metadata' => [
                        'user_id' => $user->id,
                        'transaction_id' => $transaction->id,
                        'purpose' => $request->purpose,
                    ],
                ]);

                if ($response['status'] === 'success') {
                    return $this->successResponse([
                        'payment_url' => $response['data']['link'],
                        'reference' => $transaction->reference,
                    ], 'Payment initialized successfully');
                }
            }

            throw new \Exception('Failed to initialize payment');
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            Log::error('Payment initialization failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to initialize payment. Please try again.');
        }
    }

    /**
     * Handle payment callback
     */
    public function handleCallback(Request $request, $gateway)
    {
        try {
            $reference = $request->reference ?? $request->tx_ref;
            
            if (!$reference) {
                $this->logSecurityEvent('payment_callback_no_reference', [
                    'gateway' => $gateway,
                    'request_data' => $request->except(['password'])
                ]);
                return redirect()->route('user.wallet')->with('error', 'Invalid payment reference');
            }

            $transaction = Transaction::where('reference', $reference)->first();
            
            if (!$transaction) {
                $this->logSecurityEvent('payment_callback_invalid_reference', [
                    'reference' => $reference,
                    'gateway' => $gateway
                ]);
                return redirect()->route('user.wallet')->with('error', 'Transaction not found');
            }

            if ($transaction->status !== 'pending') {
                Log::info('Payment callback for already processed transaction', [
                    'reference' => $reference,
                    'status' => $transaction->status
                ]);
                return redirect()->route('user.wallet')->with('info', 'Transaction already processed');
            }

            // Lock transaction for processing to prevent race conditions
            $transaction = Transaction::where('id', $transaction->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return redirect()->route('user.wallet')->with('info', 'Transaction is being processed');
            }

            try {
                if ($gateway === 'paystack') {
                    $response = $this->paystackService->verifyTransaction($reference);
                    
                    if ($response['status'] && $response['data']['status'] === 'success') {
                        // Verify amount matches
                        if ($response['data']['amount'] / 100 != $transaction->amount) {
                            throw new \Exception('Amount mismatch');
                        }
                        
                        $this->processSuccessfulPayment($transaction, $response['data']);
                        return redirect()->route('user.wallet')->with('success', 'Payment successful!');
                    }
                } else {
                    $transactionId = $request->transaction_id;
                    if (!$transactionId) {
                        throw new \Exception('Transaction ID missing for Flutterwave');
                    }
                    
                    $response = $this->flutterwaveService->verifyTransaction($transactionId);
                    
                    if ($response['status'] === 'success' && $response['data']['status'] === 'successful') {
                        // Verify amount matches
                        if ($response['data']['amount'] != $transaction->amount) {
                            throw new \Exception('Amount mismatch');
                        }
                        
                        $this->processSuccessfulPayment($transaction, $response['data']);
                        return redirect()->route('user.wallet')->with('success', 'Payment successful!');
                    }
                }

                // Payment verification failed
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($response ?? [])
                ]);
                
                Log::warning('Payment verification failed', [
                    'reference' => $reference,
                    'gateway' => $gateway,
                    'response' => $response ?? null
                ]);
                
                return redirect()->route('user.wallet')->with('error', 'Payment verification failed');
                
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                Log::error('Payment gateway API error', [
                    'gateway' => $gateway,
                    'reference' => $reference,
                    'error' => $e->getMessage(),
                    'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
                ]);
                
                // Don't mark as failed immediately for network errors
                return redirect()->route('user.wallet')
                    ->with('warning', 'Payment verification pending. Please check back in a few minutes.');
                    
            } catch (\Exception $e) {
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode(['error' => $e->getMessage()])
                ]);
                
                Log::error('Payment processing error', [
                    'gateway' => $gateway,
                    'reference' => $reference,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('user.wallet')->with('error', 'Payment processing failed. Please contact support.');
            }
            
        } catch (\Exception $e) {
            Log::critical('Payment callback critical error', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('user.wallet')
                ->with('error', 'An error occurred processing your payment. Please contact support.');
        }
    }

    /**
     * Process successful payment
     */
    protected function processSuccessfulPayment($transaction, $paymentData)
    {
        try {
            DB::transaction(function () use ($transaction, $paymentData) {
                // Update transaction status
                $transaction->update([
                    'status' => 'success',
                    'gateway_response' => json_encode($paymentData),
                    'processed_at' => now()
                ]);

                $user = $transaction->user;
                
                if (!$user) {
                    throw new \Exception('User not found for transaction');
                }
                
                $metadata = json_decode($transaction->metadata, true) ?? [];

                switch ($transaction->purpose) {
                    case 'deposit':
                        // Add to wallet
                        $user->increment('wallet', $transaction->amount);
                        
                        // Create deposit record
                        Deposits::create([
                            'user_id' => $user->id,
                            'amount' => $transaction->amount,
                            'payment_method' => $transaction->gateway,
                            'status' => 1,
                            'transaction_id' => $transaction->reference,
                        ]);
                        
                        // Log successful deposit
                        Log::info('Deposit processed successfully', [
                            'user_id' => $user->id,
                            'amount' => $transaction->amount,
                            'reference' => $transaction->reference
                        ]);
                        
                        // Send email notification
                        try {
                            \Mail::to($user->email)->send(new \App\Mail\PaymentConfirmation($user, $transaction));
                        } catch (\Exception $e) {
                            Log::error('Failed to send payment confirmation email', [
                                'user_id' => $user->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                        break;

                    case 'subscription':
                        // Handle subscription activation
                        if (!isset($metadata['plan_id'])) {
                            throw new \Exception('Plan ID missing for subscription payment');
                        }
                        
                        $plan = \App\Models\Plan::find($metadata['plan_id']);
                        if (!$plan) {
                            throw new \Exception('Subscription plan not found');
                        }
                        
                        // Update user subscription
                        $user->update([
                            'plan_id' => $plan->id,
                            'subscription_ends_at' => now()->addDays($plan->duration_days),
                        ]);
                        
                        Log::info('Subscription activated', [
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'reference' => $transaction->reference
                        ]);
                        break;

                    case 'buy_points':
                        // Handle points purchase
                        if (!isset($metadata['points_package_id'])) {
                            throw new \Exception('Points package ID missing');
                        }
                        
                        $package = \App\Models\PointsPackage::find($metadata['points_package_id']);
                        if (!$package) {
                            throw new \Exception('Points package not found');
                        }
                        
                        // Add points to user
                        $user->increment('points', $package->points);
                        
                        // Record points transaction
                        \App\Models\Points::create([
                            'user_id' => $user->id,
                            'type' => 'purchased',
                            'score' => $package->points,
                            'reason' => 'Points purchased',
                            'related_id' => $transaction->id
                        ]);
                        
                        Log::info('Points purchased successfully', [
                            'user_id' => $user->id,
                            'points' => $package->points,
                            'reference' => $transaction->reference
                        ]);
                        break;
                        
                    default:
                        Log::warning('Unknown payment purpose', [
                            'purpose' => $transaction->purpose,
                            'transaction_id' => $transaction->id
                        ]);
                }
            });
            
        } catch (\Exception $e) {
            // Log the error but don't throw - payment was successful at gateway
            Log::critical('Failed to process successful payment', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Update transaction with error
            $transaction->update([
                'status' => 'processing_error',
                'notes' => 'Payment received but processing failed: ' . $e->getMessage()
            ]);
            
            // Notify admins
            try {
                \Mail::to(get_setting('admin_email', 'admin@eforum.ng'))
                    ->send(new \App\Mail\PaymentProcessingError($transaction, $e->getMessage()));
            } catch (\Exception $mailError) {
                Log::error('Failed to send admin notification', ['error' => $mailError->getMessage()]);
            }
        }
    }

    /**
     * Initialize withdrawal
     */
    public function initiateWithdrawal(Request $request)
    {
        $validatedData = $this->validateWithCustomRules($request->all(), [
            'amount' => 'required|numeric|min:1000',
            'bank_code' => 'required',
            'account_number' => 'required|digits:10',
            'account_name' => 'required|string',
            'gateway' => 'required|in:paystack,flutterwave',
        ]);

        if ($validatedData->fails()) {
            return $this->errorResponse('Validation failed', $validatedData->errors()->toArray());
        }

        $user = Auth::user();
        $amount = $request->amount;

        // Check wallet balance
        if ($user->wallet < $amount) {
            return $this->errorResponse('Insufficient wallet balance');
        }

        // Verify account details
        try {
            if ($request->gateway === 'paystack') {
                $verification = $this->paystackService->resolveAccountNumber(
                    $request->account_number,
                    $request->bank_code
                );
                
                if (!$verification['status']) {
                    return $this->errorResponse('Account verification failed');
                }
            } else {
                $verification = $this->flutterwaveService->resolveAccount(
                    $request->account_number,
                    $request->bank_code
                );
                
                if ($verification['status'] !== 'success') {
                    return $this->errorResponse('Account verification failed');
                }
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to verify account details');
        }

        // Create withdrawal request
        DB::transaction(function () use ($user, $amount, $request) {
            // Deduct from wallet
            $user->decrement('wallet', $amount);
            
            // Create withdrawal record
            Withdraw::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'bank_name' => $request->bank_name,
                'bank_code' => $request->bank_code,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'gateway' => $request->gateway,
                'status' => 0, // Pending
                'reference' => $this->generateReference('withdraw'),
            ]);
        });

        return $this->successResponse(null, 'Withdrawal request submitted successfully');
    }

    /**
     * Get Nigerian banks list
     */
    public function getBanks(Request $request)
    {
        $gateway = $request->gateway ?? 'paystack';
        
        try {
            if ($gateway === 'paystack') {
                $response = $this->paystackService->getBanks();
                $banks = $response['data'] ?? [];
            } else {
                $response = $this->flutterwaveService->getBanks();
                $banks = $response['data'] ?? [];
            }

            // Format banks for frontend
            $formattedBanks = collect($banks)->map(function ($bank) {
                return [
                    'id' => $bank['id'] ?? null,
                    'name' => $bank['name'],
                    'code' => $bank['code'],
                ];
            })->sortBy('name')->values();

            return $this->successResponse($formattedBanks, 'Banks retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to get banks: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve banks list');
        }
    }

    /**
     * Generate unique reference
     */
    protected function generateReference($type)
    {
        return strtoupper($type) . '_' . time() . '_' . uniqid();
    }
}
