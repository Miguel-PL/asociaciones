<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(6), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->optional()->sentence(20),
            'body' => fake()->paragraphs(4, true),
            'cover_image' => null,
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(0, 90)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now()->addWeek(),
        ]);
    }

    public function inCategory(?Category $category = null): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category?->id,
        ]);
    }
}
