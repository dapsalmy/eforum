<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidatesInput
{
    /**
     * Common validation rules for eForum
     */
    protected function getCommonRules(): array
    {
        return [
            'email' => ['email', 'max:255', 'regex:/^[\w\.\-]+@[\w\.\-]+\.[a-zA-Z]{2,}$/'],
            'username' => ['alpha_dash', 'min:3', 'max:30'],
            'name' => ['string', 'min:2', 'max:100', 'regex:/^[\pL\s\-\.]+$/u'],
            'bio' => ['string', 'max:500'],
            'title' => ['string', 'min:5', 'max:200'],
            'content' => ['string', 'min:10'],
            'url' => ['url', 'max:255'],
            'phone' => ['regex:/^(\+234|0)[789]\d{9}$/'], // Nigerian phone number
        ];
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Remove any script tags
                $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $value);
                
                // Remove dangerous HTML tags but keep safe ones for forum formatting
                $allowed_tags = '<p><br><strong><em><u><ul><ol><li><blockquote><code><pre><a><img>';
                $value = strip_tags($value, $allowed_tags);
                
                // Trim whitespace
                $value = trim($value);
                
                // Prevent SQL injection by escaping special characters
                $value = addslashes($value);
            }
            
            $sanitized[$key] = $value;
        }
        
        return $sanitized;
    }

    /**
     * Validate and sanitize request data
     */
    protected function validateAndSanitize(Request $request, array $rules, array $messages = []): array
    {
        // Sanitize input first
        $data = $this->sanitizeInput($request->all());
        
        // Validate
        $validator = Validator::make($data, $rules, $messages);
        
        if ($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ];
        }
        
        return [
            'status' => 'success',
            'data' => $validator->validated()
        ];
    }

    /**
     * Validate Nigerian phone number
     */
    protected function validateNigerianPhone(string $phone): bool
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // Check if it matches Nigerian phone pattern
        return preg_match('/^(\+234|0)[789]\d{9}$/', $phone);
    }

    /**
     * Validate file upload
     */
    protected function validateFileUpload(Request $request, string $field, array $allowedTypes = ['jpg', 'jpeg', 'png'], int $maxSize = 5120): array
    {
        $rules = [
            $field => [
                'required',
                'file',
                'mimes:' . implode(',', $allowedTypes),
                'max:' . $maxSize
            ]
        ];
        
        return $this->validateAndSanitize($request, $rules);
    }

    /**
     * XSS Protection - Clean dangerous content
     */
    protected function cleanXSS(string $input): string
    {
        // Remove any JavaScript event handlers
        $input = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $input);
        
        // Remove javascript: protocol
        $input = preg_replace('/javascript\s*:/i', '', $input);
        
        // Remove vbscript: protocol
        $input = preg_replace('/vbscript\s*:/i', '', $input);
        
        // Encode special characters
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }

    /**
     * Validate pagination parameters
     */
    protected function validatePagination(Request $request): array
    {
        $rules = [
            'page' => ['integer', 'min:1'],
            'per_page' => ['integer', 'min:10', 'max:100'],
            'sort' => ['string', 'in:asc,desc'],
            'order_by' => ['string', 'alpha_dash']
        ];
        
        return $this->validateAndSanitize($request, $rules);
    }
}
