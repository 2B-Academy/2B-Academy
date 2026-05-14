<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserExamSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('user_exams', [
            [
                'id' => 8,
                'user_id' => 1801,
                'course_id' => 7,
                'exam_id' => 10,
                'user_degree' => 100.00,
                'status' => 'success',
                'created_at' => '2026-02-03 12:19:31',
                'updated_at' => '2026-02-03 12:19:31',
            ],
        ], ['id'], ['user_id', 'course_id', 'exam_id', 'user_degree', 'status', 'updated_at']);
    }
}
