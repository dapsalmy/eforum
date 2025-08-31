<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Currency
{
    /**
     * Format amount in Nigerian Naira
     */
    public static function formatNaira($amount, $includeSymbol = true)
    {
        $formatted = number_format($amount, 2, '.', ',');
        return $includeSymbol ? '₦' . $formatted : $formatted;
    }

    /**
     * Format amount in any currency
     */
    public static function format($amount, $currencyCode = null)
    {
        $currencyCode = $currencyCode ?: config('currency.default', 'NGN');
        $currency = config("currency.currencies.{$currencyCode}");
        
        if (!$currency) {
            return number_format($amount, 2);
        }
        
        $formatted = number_format(
            $amount,
            $currency['decimal_places'],
            $currency['decimal_separator'],
            $currency['thousands_separator']
        );
        
        if ($currency['symbol_position'] === 'before') {
            return $currency['symbol'] . $formatted;
        } else {
            return $formatted . $currency['symbol'];
        }
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert($amount, $from = 'USD', $to = 'NGN')
    {
        if ($from === $to) {
            return $amount;
        }
        
        $rate = self::getExchangeRate($from, $to);
        return $amount * $rate;
    }

    /**
     * Get exchange rate
     */
    public static function getExchangeRate($from, $to)
    {
        $cacheKey = "exchange_rate_{$from}_{$to}";
        
        return Cache::remember($cacheKey, config('currency.exchange_rates.cache_duration'), function () use ($from, $to) {
            // In production, this would fetch from CBN API or other provider
            // For now, use fallback rates
            $fallbackKey = "{$from}_TO_{$to}";
            return config("currency.exchange_rates.fallback_rate.{$fallbackKey}", 1);
        });
    }

    /**
     * Parse amount from formatted string
     */
    public static function parseAmount($formattedAmount)
    {
        // Remove currency symbols and thousands separators
        $amount = preg_replace('/[₦$,\s]/', '', $formattedAmount);
        return floatval($amount);
    }

    /**
     * Validate Nigerian bank account number
     */
    public static function validateNigerianAccountNumber($accountNumber)
    {
        // Nigerian account numbers are typically 10 digits
        return preg_match('/^\d{10}$/', $accountNumber);
    }

    /**
     * Format Nigerian phone number
     */
    public static function formatNigerianPhone($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
        
        // Add country code if not present
        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '234' . $phoneNumber;
        } elseif (strlen($phoneNumber) === 11 && $phoneNumber[0] === '0') {
            $phoneNumber = '234' . substr($phoneNumber, 1);
        }
        
        // Format as +234 XXX XXX XXXX
        if (strlen($phoneNumber) === 13) {
            return '+' . substr($phoneNumber, 0, 3) . ' ' . 
                   substr($phoneNumber, 3, 3) . ' ' . 
                   substr($phoneNumber, 6, 3) . ' ' . 
                   substr($phoneNumber, 9, 4);
        }
        
        return $phoneNumber;
    }
}
