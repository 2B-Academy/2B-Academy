<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title'               => json_encode(['ar' => $title, 'en' => $title]),
            'description'         => json_encode(['ar' => fake()->paragraph(), 'en' => fake()->paragraph()]),
            'title_for_certificate' => null,
            'notification_text'   => null,
            'image'               => 'Course/placeholder.jpg',
            'category_id'         => Category::factory(),
            'intro_video'         => null,
            'price'               => 0,
            'currency'            => 'EGP',
            'hours'               => fake()->numberBetween(1, 40),
            'language'            => 'ar',
            'level'               => 'beginner',
            'certificate'         => true,
            'active'              => true,
            'course_type'         => 'online',
            'for_public'          => false,
            'is_evaluate'         => false,
            'outside_materials'   => false,
            'allow_attendances'   => false,
        ];
    }

    public function offline(): static
    {
        return $this->state(fn () => ['course_type' => 'offline']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
