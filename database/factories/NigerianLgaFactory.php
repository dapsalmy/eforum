<?php

namespace Database\Factories;

use App\Models\NigerianLga;
use App\Models\NigerianState;
use Illuminate\Database\Eloquent\Factories\Factory;

class NigerianLgaFactory extends Factory
{
    protected $model = NigerianLga::class;

    public function definition(): array
    {
        return [
            'state_id' => NigerianState::factory(),
            'name' => $this->faker->randomElement([
                'Ikeja', 'Victoria Island', 'Surulere', 'Alimosho', 'Kosofe',
                'Mushin', 'Oshodi-Isolo', 'Eti-Osa', 'Agege', 'Ifako-Ijaiye'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
