<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseLecture>
 */
class CourseLectureFactory extends Factory
{
    protected $model = CourseLecture::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'course_id'          => Course::factory(),
            'section_id'         => CourseSection::factory(),
            'title'              => json_encode(['ar' => $title, 'en' => $title]),
            'type'               => 'url',
            'video'              => 'https://example.com/lecture.mp4',
            'duration_minutes'   => fake()->numberBetween(5, 90),
            'require_completion' => false,
        ];
    }
}
