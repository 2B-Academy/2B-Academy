<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'course_id'        => Course::factory(),
            'user_machine_code' => null,
            'section_id'       => null,
            'session_id'       => null,
            'attendance_hours' => 1,
            'is_manual'        => false,
        ];
    }
}
