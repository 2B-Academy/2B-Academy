<?php

namespace Database\Factories;

use App\Models\QualificationSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QualificationSkill>
 */
class QualificationSkillFactory extends Factory
{
    protected $model = QualificationSkill::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => json_encode(['ar' => $name, 'en' => $name]),
        ];
    }
}
