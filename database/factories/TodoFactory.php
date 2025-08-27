<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(),
            'completed' => fake()->boolean(30),
            'priority' => fake()->numberBetween(1, 5),
            'due_date' => fake()->optional(0.6)->dateTimeBetween('today', '+30 days'),
        ];
    }
}
