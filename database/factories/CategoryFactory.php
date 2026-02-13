<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement(['code', 'server', 'database', 'shield', 'layout', 'terminal', 'cloud', 'cpu']),
            'sort_order' => fake()->numberBetween(0, 100),
            'meta_title' => fake()->optional()->sentence(4),
            'meta_description' => fake()->optional()->sentence(10),
        ];
    }
}
