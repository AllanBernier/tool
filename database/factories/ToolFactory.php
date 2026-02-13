<?php

namespace Database\Factories;

use App\Enums\GenerationStatus;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tool>
 */
class ToolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'url' => fake()->url(),
            'description' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'pricing' => [
                ['plan' => 'Free', 'price' => 0],
                ['plan' => 'Pro', 'price' => fake()->numberBetween(5, 50)],
            ],
            'pros' => fake()->words(3),
            'cons' => fake()->words(2),
            'features' => fake()->words(5),
            'faq' => [
                ['question' => fake()->sentence(), 'answer' => fake()->paragraph()],
            ],
            'platforms' => fake()->randomElements(['windows', 'macos', 'linux', 'web'], fake()->numberBetween(1, 4)),
            'category_id' => Category::factory(),
            'generation_status' => GenerationStatus::Pending,
            'meta_title' => fake()->optional()->sentence(4),
            'meta_description' => fake()->optional()->sentence(10),
        ];
    }

    /**
     * Indicate that the tool is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now(),
            'generation_status' => GenerationStatus::Completed,
        ]);
    }

    /**
     * Indicate that the tool is sponsored.
     */
    public function sponsored(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sponsored' => true,
            'sponsored_until' => now()->addMonth(),
        ]);
    }
}
