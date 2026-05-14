<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersCourseSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('users_courses', [
            ['id' => 46, 'user_id' => 793,  'course_id' => 6, 'group_id' => 20, 'created_at' => null, 'updated_at' => null],
            ['id' => 47, 'user_id' => 793,  'course_id' => 7, 'group_id' => 23, 'created_at' => null, 'updated_at' => null],
            ['id' => 48, 'user_id' => 1802, 'course_id' => 6, 'group_id' => 21, 'created_at' => null, 'updated_at' => null],
            ['id' => 49, 'user_id' => 793,  'course_id' => 8, 'group_id' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 50, 'user_id' => 1802, 'course_id' => 8, 'group_id' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 51, 'user_id' => 1761, 'course_id' => 8, 'group_id' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 52, 'user_id' => 1762, 'course_id' => 8, 'group_id' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 53, 'user_id' => 3,    'course_id' => 8, 'group_id' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 54, 'user_id' => 4,    'course_id' => 8, 'group_id' => 26, 'created_at' => null, 'updated_at' => null],
            ['id' => 55, 'user_id' => 19,   'course_id' => 8, 'group_id' => 27, 'created_at' => null, 'updated_at' => null],
            ['id' => 56, 'user_id' => 21,   'course_id' => 8, 'group_id' => 27, 'created_at' => null, 'updated_at' => null],
        ], ['id'], ['user_id', 'course_id', 'group_id', 'updated_at']);
    }
}
