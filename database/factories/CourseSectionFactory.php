<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSection>
 *
 * A cohort. Defaults to a future, joinable cohort (open deadline, seats
 * available) so it shows up in the Academy availability feed (S-02).
 */
class CourseSectionFactory extends Factory
{
    protected $model = CourseSection::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'course_id'           => Course::factory(),
            'name'                => json_encode(['ar' => $name, 'en' => $name]),
            'start_date'          => now()->addDays(20)->toDateString(),
            'end_date'            => now()->addDays(40)->toDateString(),
            'capacity'            => 30,
            'status'              => 'scheduled',
            'enrolment_closes_at' => now()->addDays(15)->toDateString(),
        ];
    }

    /** Cohort whose enrolment deadline has already passed. */
    public function closed(): static
    {
        return $this->state(fn () => [
            'start_date'          => now()->subDays(1)->toDateString(),
            'enrolment_closes_at' => now()->subDays(2)->toDateString(),
        ]);
    }

    /** Cohort with no free seats. */
    public function full(): static
    {
        return $this->state(fn () => ['capacity' => 0]);
    }

    /** Cohort currently running (start in past, end in future). */
    public function running(): static
    {
        return $this->state(fn () => [
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date'   => now()->addDays(15)->toDateString(),
        ]);
    }
}
