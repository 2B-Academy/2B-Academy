<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\UsersCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsersCourse>
 *
 * An enrolment row. `group_id` is the cohort (course_sections.id) the
 * learner belongs to — required for the sessions / attendance flows.
 */
class UsersCourseFactory extends Factory
{
    protected $model = UsersCourse::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'course_id' => Course::factory(),
            'group_id'  => null,
        ];
    }
}
