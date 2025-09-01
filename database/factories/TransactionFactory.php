<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(100, 50000),
            'currency' => 'NGN',
            'gateway' => $this->faker->randomElement(['paystack', 'flutterwave', 'paypal']),
            'purpose' => $this->faker->randomElement(['deposit', 'withdrawal', 'subscription']),
            'status' => $this->faker->randomElement(['pending', 'success', 'failed']),
            'reference' => 'TEST_' . $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'gateway_response' => json_encode(['status' => 'success']),
            'metadata' => json_encode([]),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
