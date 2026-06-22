<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSession>
 */
class CourseSessionFactory extends Factory
{
    protected $model = CourseSession::class;

    public function definition(): array
    {
        return [
            'course_id'    => Course::factory(),
            'section_id'   => CourseSection::factory(),
            'title'        => fake()->sentence(3),
            'time_from'    => null,
            'time_to'      => null,
            'location'     => 'Room ' . fake()->numberBetween(1, 9),
            'session_date' => now()->toDateString(),
        ];
    }

    /**
     * A session that is open for the mobile S-06 passcode flow right now:
     * today's date, no time-of-day restriction, with a live passcode.
     */
    public function openForAttendance(string $passcode = '12345'): static
    {
        return $this->state(fn () => [
            'session_date'              => now()->toDateString(),
            'time_from'                 => null,
            'time_to'                   => null,
            'passcode'                  => $passcode,
            'passcode_issued_at'        => now(),
            'passcode_expires_at'       => now()->addMinutes(30),
            'attendance_window_minutes' => 30,
        ]);
    }
}
