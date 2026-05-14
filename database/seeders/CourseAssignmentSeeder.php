<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * `course_assignments.title` is NOT in the translation migrations and stays
 * a plain varchar, so we just persist the source value verbatim.
 */
class CourseAssignmentSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('course_assignments', [
            ['id' => 6, 'course_id' => 6, 'title' => 'فايل اساين 1', 'file' => 'CourseAssignment/z8aJm0bWpvgYUmEBcBMd926c7d6bf00d850de492cf7d62bdf3de.pdf', 'created_at' => '2026-05-09 08:04:26', 'updated_at' => '2026-05-09 08:04:26'],
            ['id' => 7, 'course_id' => 6, 'title' => 'فايل اساين 2', 'file' => 'CourseAssignment/G5xRaF68H1ysvAh89RMp258f325c17e9367df866c4ecafe75393.pdf', 'created_at' => '2026-05-09 08:04:26', 'updated_at' => '2026-05-09 08:04:26'],
        ], ['id'], ['course_id', 'title', 'file', 'updated_at']);
    }
}
