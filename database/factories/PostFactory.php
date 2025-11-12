<?php

namespace Database\Factories;

use App\Enums\PostStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(),
            'body' => fake()->paragraphs(4, true),
            'status' => fake()->randomElement(PostStatusEnum::cases()),
            'published_at' => fake()->boolean(80) ? fake()->dateTimeThisMonth() : null,
        ];
    }
}
