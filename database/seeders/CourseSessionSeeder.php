<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSessionSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $location = 'السراج مول الدور السابع';

        $rows = [
            ['id' => 5, 'course_id' => 6, 'section_id' => 20, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-01-01', 'created_at' => '2026-02-02 20:02:24'],
            ['id' => 6, 'course_id' => 6, 'section_id' => 20, 'title' => 'session 2', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-02-12', 'created_at' => '2026-02-02 20:02:24'],
            ['id' => 7, 'course_id' => 6, 'section_id' => 20, 'title' => 'session 3', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-02-15', 'created_at' => '2026-02-02 20:02:24'],
            ['id' => 8, 'course_id' => 6, 'section_id' => 21, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-02-22', 'created_at' => '2026-02-02 20:03:34'],
            ['id' => 9, 'course_id' => 6, 'section_id' => 21, 'title' => 'session 2', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-02-24', 'created_at' => '2026-02-02 20:03:34'],
            ['id' => 10, 'course_id' => 6, 'section_id' => 22, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-03-01', 'created_at' => '2026-02-02 20:04:06'],
            ['id' => 11, 'course_id' => 6, 'section_id' => 22, 'title' => 'session 2', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-03-04', 'created_at' => '2026-02-02 20:04:06'],
            ['id' => 12, 'course_id' => 7, 'section_id' => 23, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '18:00:00', 'session_date' => '2026-02-10', 'created_at' => '2026-02-03 12:12:43'],
            ['id' => 13, 'course_id' => 7, 'section_id' => 24, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '19:00:00', 'session_date' => '2026-02-15', 'created_at' => '2026-02-03 12:13:02'],
            ['id' => 14, 'course_id' => 8, 'section_id' => 26, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '17:00:00', 'session_date' => '2026-02-10', 'created_at' => '2026-02-09 11:11:37'],
            ['id' => 15, 'course_id' => 8, 'section_id' => 26, 'title' => 'session 2', 'time_from' => '14:00:00', 'time_to' => '17:00:00', 'session_date' => '2026-02-20', 'created_at' => '2026-02-09 11:11:37'],
            ['id' => 16, 'course_id' => 8, 'section_id' => 27, 'title' => 'session 1', 'time_from' => '14:00:00', 'time_to' => '17:00:00', 'session_date' => '2026-02-25', 'created_at' => '2026-02-09 11:12:44'],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'course_id' => $row['course_id'],
            'section_id' => $row['section_id'],
            'title' => $row['title'],
            'time_from' => $row['time_from'],
            'time_to' => $row['time_to'],
            'location' => $location,
            'session_date' => $row['session_date'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['created_at'],
        ], $rows);

        $this->schemaAwareUpsert('course_sessions', $payload, ['id'], ['course_id', 'section_id', 'title', 'time_from', 'time_to', 'location', 'session_date', 'updated_at']);
    }
}
