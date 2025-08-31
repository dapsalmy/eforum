<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaystackService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;

    public function __construct()
    {
        $this->baseUrl = config('paystack.paymentUrl');
        $this->secretKey = config('paystack.secretKey');
        $this->publicKey = config('paystack.publicKey');
    }

    /**
     * Initialize a transaction
     */
    public function initializeTransaction(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $data['email'],
                'amount' => $data['amount'] * 100, // Convert to kobo
                'currency' => $data['currency'] ?? 'NGN',
                'reference' => $data['reference'] ?? $this->generateReference(),
                'callback_url' => $data['callback_url'] ?? route('paystack.callback'),
                'metadata' => $data['metadata'] ?? [],
                'channels' => $data['channels'] ?? config('paystack.channels'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to initialize transaction: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to verify transaction: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack verification error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a subscription plan
     */
    public function createPlan(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/plan', [
                'name' => $data['name'],
                'amount' => $data['amount'] * 100, // Convert to kobo
                'interval' => $data['interval'], // daily, weekly, monthly, annually
                'currency' => $data['currency'] ?? 'NGN',
                'description' => $data['description'] ?? '',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create plan: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack plan creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a customer
     */
    public function createCustomer(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/customer', [
                'email' => $data['email'],
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'phone' => $data['phone'] ?? '',
                'metadata' => $data['metadata'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create customer: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack customer creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Resolve account number
     */
    public function resolveAccountNumber($accountNumber, $bankCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank/resolve', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to resolve account: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack account resolution error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a transfer recipient
     */
    public function createTransferRecipient(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transferrecipient', [
                'type' => 'nuban', // Nigerian bank account
                'name' => $data['name'],
                'account_number' => $data['account_number'],
                'bank_code' => $data['bank_code'],
                'currency' => 'NGN',
                'description' => $data['description'] ?? '',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create transfer recipient: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack transfer recipient error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Initiate a transfer
     */
    public function initiateTransfer(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfer', [
                'source' => 'balance',
                'amount' => $data['amount'] * 100, // Convert to kobo
                'recipient' => $data['recipient_code'],
                'reason' => $data['reason'] ?? 'Withdrawal from eForum',
                'reference' => $data['reference'] ?? $this->generateReference(),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to initiate transfer: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack transfer error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get list of banks
     */
    public function getBanks()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank', [
                'country' => 'nigeria',
                'perPage' => 100,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get banks: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack get banks error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate a unique reference
     */
    protected function generateReference()
    {
        return 'EFORUM_' . strtoupper(uniqid());
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhook($requestBody, $signature)
    {
        $calculated = hash_hmac('sha512', $requestBody, $this->secretKey);
        return hash_equals($calculated, $signature);
    }
}
