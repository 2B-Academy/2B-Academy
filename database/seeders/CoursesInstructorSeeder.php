<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesInstructorSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('courses_instructors', [
            ['id' => 7, 'course_id' => 6, 'instructor_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 8, 'course_id' => 7, 'instructor_id' => 2, 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'course_id' => 8, 'instructor_id' => 1, 'created_at' => null, 'updated_at' => null],
        ], ['id'], ['course_id', 'instructor_id', 'updated_at']);
    }
}
