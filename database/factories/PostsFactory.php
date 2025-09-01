<?php

namespace Database\Factories;

use App\Models\Posts;
use App\Models\User;
use App\Models\Categories;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostsFactory extends Factory
{
    protected $model = Posts::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Categories::factory(),
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'body' => $this->faker->paragraphs(3, true),
            'tags' => implode(',', $this->faker->words(3)),
            'views' => $this->faker->numberBetween(0, 1000),
            'likes' => $this->faker->numberBetween(0, 100),
            'solved' => $this->faker->boolean(20),
            'closed' => $this->faker->boolean(5),
            'pinned' => $this->faker->boolean(2),
            'status' => 1,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
