<?php

namespace Database\Factories;

use App\Models\NigerianState;
use Illuminate\Database\Eloquent\Factories\Factory;

class NigerianStateFactory extends Factory
{
    protected $model = NigerianState::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Lagos', 'Kano', 'Kaduna', 'Oyo', 'Rivers', 'Bayelsa', 
                'Katsina', 'Ogun', 'Imo', 'Borno', 'Osun', 'Delta'
            ]),
            'code' => strtoupper($this->faker->lexify('??')),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
