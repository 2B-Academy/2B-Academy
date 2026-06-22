<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourseEvaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCourseEvaluation>
 *
 * Row existence (for an evaluate-type course) is what makes an
 * evaluation-based certificate appear in S-07.
 */
class UserCourseEvaluationFactory extends Factory
{
    protected $model = UserCourseEvaluation::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'course_id'     => Course::factory(),
            'instructor_id' => 1,
            'evaluation_id' => 1,
        ];
    }
}
