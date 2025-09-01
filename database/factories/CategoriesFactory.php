<?php

namespace Database\Factories;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriesFactory extends Factory
{
    protected $model = Categories::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'icon' => 'fas fa-folder',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
