<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceLogSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('attendance_logs', [
            ['id' => 1, 'user_id' => 1, 'attendance_id' => 35, 'employee_code' => '1610', 'log' => ' تم حذف سيشن للموظف 1761 والدورة التدريبية 8', 'created_at' => '2026-02-22 14:07:40', 'updated_at' => '2026-02-22 14:07:40'],
            ['id' => 2, 'user_id' => 1, 'attendance_id' => 33, 'employee_code' => '1610', 'log' => ' تم حذف سيشن للموظف 1761 والدورة التدريبية 8', 'created_at' => '2026-02-22 14:08:05', 'updated_at' => '2026-02-22 14:08:05'],
        ], ['id'], ['user_id', 'attendance_id', 'employee_code', 'log', 'updated_at']);
    }
}
