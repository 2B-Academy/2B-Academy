<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'contact',
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '01098541100',
            'message' => '01098541100',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): bool
    {
        return false;
    }
}
