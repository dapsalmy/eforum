<?php

namespace Database\Factories;

use App\Models\Points;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointsFactory extends Factory
{
    protected $model = Points::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->numberBetween(1, 5),
            'score' => $this->faker->numberBetween(-50, 100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
