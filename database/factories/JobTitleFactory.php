<?php

namespace Database\Factories;

use App\Models\JobTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobTitle>
 */
class JobTitleFactory extends Factory
{
    protected $model = JobTitle::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
        ];
    }
}
