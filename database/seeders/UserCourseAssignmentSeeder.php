<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCourseAssignmentSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('user_course_assignments', [
            [
                'id' => 5,
                'user_id' => 793,
                'course_assignment_id' => 6,
                'user_file' => 'UserCourseAssignment/liulDzephdLqOgyH9eLh7f13ee866d81802bd04c02b9f11db998.pdf',
                'feedback' => 'هذا الملف ليس كامل من فضلك تواصل مع الانستراكتور احمد زيدان',
                'score' => '60',
                'created_at' => '2026-05-09 08:07:01',
                'updated_at' => '2026-05-09 08:34:13',
            ],
            [
                'id' => 6,
                'user_id' => 793,
                'course_assignment_id' => 7,
                'user_file' => 'UserCourseAssignment/qC1HRA8MeQJtST3zjDQr97954ef169c2d38e03778bf83d43240c.pdf',
                'feedback' => 'ملف ممتاز',
                'score' => '95',
                'created_at' => '2026-05-09 08:07:23',
                'updated_at' => '2026-05-09 09:09:59',
            ],
        ], ['id'], ['user_id', 'course_assignment_id', 'user_file', 'feedback', 'score', 'updated_at']);
    }
}
