<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\ValidationRule;

class ReCaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $client = new Client;
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => config('services.recaptcha.secretKey'),
                    'response' => $value
                ]
            ]
        );
        
        $body = json_decode((string)$response->getBody());
        
        if (!$body->success) {
            $fail('The reCAPTCHA verification failed.');
        }
    }
}
