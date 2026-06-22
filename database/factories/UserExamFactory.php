<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\User;
use App\Models\UserExam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserExam>
 *
 * A learner's exam attempt. Defaults to a passed (`success`) attempt so
 * it can act as a certificate source when attached to a final exam.
 */
class UserExamFactory extends Factory
{
    protected $model = UserExam::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'course_id'   => Course::factory(),
            'exam_id'     => CourseExam::factory(),
            'user_degree' => 100,
            'status'      => 'success',
        ];
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => 'failed', 'user_degree' => 0]);
    }
}
