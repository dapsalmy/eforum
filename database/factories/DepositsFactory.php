<?php

namespace Database\Factories;

use App\Models\Deposits;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositsFactory extends Factory
{
    protected $model = Deposits::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(100, 50000),
            'gateway' => $this->faker->randomElement(['paystack', 'flutterwave', 'paypal']),
            'transaction_id' => 'TXN_' . $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'status' => $this->faker->numberBetween(0, 1),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
