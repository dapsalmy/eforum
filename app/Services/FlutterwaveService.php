<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FlutterwaveService
{
    protected $baseUrl;
    protected $secretKey;
    protected $publicKey;
    protected $encryptionKey;

    public function __construct()
    {
        $this->baseUrl = config('flutterwave.baseUrl');
        $this->secretKey = config('flutterwave.secretKey');
        $this->publicKey = config('flutterwave.publicKey');
        $this->encryptionKey = config('flutterwave.encryptionKey');
    }

    /**
     * Initialize a payment
     */
    public function initializePayment(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payments', [
                'tx_ref' => $data['reference'] ?? $this->generateReference(),
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'NGN',
                'redirect_url' => $data['callback_url'] ?? route('flutterwave.callback'),
                'payment_options' => implode(',', $data['payment_options'] ?? config('flutterwave.paymentOptions')),
                'customer' => [
                    'email' => $data['email'],
                    'phonenumber' => $data['phone'] ?? '',
                    'name' => $data['name'] ?? '',
                ],
                'customizations' => [
                    'title' => config('flutterwave.title'),
                    'logo' => config('flutterwave.logo'),
                    'description' => $data['description'] ?? 'Payment on eForum',
                ],
                'meta' => $data['metadata'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to initialize payment: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transactions/' . $transactionId . '/verify');

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to verify transaction: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave verification error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment plan
     */
    public function createPlan(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment-plans', [
                'amount' => $data['amount'],
                'name' => $data['name'],
                'interval' => $data['interval'], // daily, weekly, monthly, yearly
                'duration' => $data['duration'] ?? 0, // 0 for unlimited
                'currency' => $data['currency'] ?? 'NGN',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create plan: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave plan creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a transfer beneficiary
     */
    public function createBeneficiary(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/beneficiaries', [
                'account_number' => $data['account_number'],
                'account_bank' => $data['bank_code'],
                'beneficiary_name' => $data['name'],
                'currency' => 'NGN',
                'email' => $data['email'] ?? '',
                'meta' => $data['metadata'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to create beneficiary: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave beneficiary creation error: ' . $e->getMessage());
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
            ])->post($this->baseUrl . '/transfers', [
                'account_bank' => $data['bank_code'],
                'account_number' => $data['account_number'],
                'amount' => $data['amount'],
                'narration' => $data['narration'] ?? 'Withdrawal from eForum',
                'currency' => 'NGN',
                'reference' => $data['reference'] ?? $this->generateReference(),
                'callback_url' => route('flutterwave.transfer.callback'),
                'debit_currency' => 'NGN',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to initiate transfer: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave transfer error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Resolve account details
     */
    public function resolveAccount($accountNumber, $bankCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->post($this->baseUrl . '/accounts/resolve', [
                'account_number' => $accountNumber,
                'account_bank' => $bankCode,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to resolve account: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave account resolution error: ' . $e->getMessage());
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
            ])->get($this->baseUrl . '/banks/NG');

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get banks: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave get banks error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate BVN consent
     */
    public function generateBvnConsent($bvn, $firstname, $lastname)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/bvn/consent', [
                'bvn' => $bvn,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'redirect_url' => route('flutterwave.bvn.callback'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to generate BVN consent: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Flutterwave BVN consent error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate a unique reference
     */
    protected function generateReference()
    {
        return 'EFORUM_FW_' . strtoupper(uniqid());
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhook($signature, $payload)
    {
        $secretHash = config('flutterwave.webhookSecretHash');
        if (!$secretHash) {
            return false;
        }
        
        $calculatedSignature = hash_hmac('sha256', $payload, $secretHash);
        return hash_equals($calculatedSignature, $signature);
    }

    /**
     * Encrypt data for 3DSecure
     */
    public function encrypt3DSecureData($data)
    {
        $encryptedData = openssl_encrypt(
            json_encode($data),
            'DES-EDE3',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            '00000000'
        );
        
        return base64_encode($encryptedData);
    }
}
