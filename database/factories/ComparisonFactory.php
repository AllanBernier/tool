<?php

namespace Database\Factories;

use App\Enums\GenerationStatus;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comparison>
 */
class ComparisonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tool_a_id' => Tool::factory(),
            'tool_b_id' => Tool::factory(),
            'content' => fake()->paragraphs(3, true),
            'verdict' => fake()->sentence(),
            'generation_status' => GenerationStatus::Pending,
            'meta_title' => fake()->optional()->sentence(4),
            'meta_description' => fake()->optional()->sentence(10),
        ];
    }

    /**
     * Indicate that the comparison is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now(),
            'generation_status' => GenerationStatus::Completed,
        ]);
    }
}
