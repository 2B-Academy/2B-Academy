<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseExam>
 */
class CourseExamFactory extends Factory
{
    protected $model = CourseExam::class;

    public function definition(): array
    {
        $title = fake()->sentence(2);

        return [
            'course_id'  => Course::factory(),
            'section_id' => CourseSection::factory(),
            'title'      => json_encode(['ar' => $title, 'en' => $title]),
            'degree'     => 100,
            'duration'   => 60,
            'is_final'   => false,
        ];
    }

    /** The course's final exam — the one that drives certificate issuance. */
    public function final(): static
    {
        return $this->state(fn () => ['is_final' => true]);
    }
}
