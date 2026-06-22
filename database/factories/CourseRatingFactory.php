<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseRating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseRating>
 */
class CourseRatingFactory extends Factory
{
    protected $model = CourseRating::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'course_id' => Course::factory(),
            'rating'    => fake()->numberBetween(1, 5),
            'comment'   => fake()->optional()->sentence(),
        ];
    }
}
