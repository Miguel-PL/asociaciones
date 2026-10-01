<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'body' => fake()->paragraphs(3, true),
            'meta_description' => fake()->optional()->sentence(12),
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(0, 60)),
        ];
    }

    /**
     * La página todavía no es visible.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * La página está publicada con fecha futura.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now()->addWeek(),
        ]);
    }
}
