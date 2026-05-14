<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFormSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('user_forms', [
            ['id' => 6, 'form_id' => 7, 'user_id' => 1801, 'name' => 'هاني جريده', 'machine_code' => '1000', 'mark' => 100, 'duration' => 20, 'start_at' => '2026-02-10 23:26:44', 'created_at' => '2026-02-10 21:26:44', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 7, 'form_id' => 7, 'user_id' => 1802, 'name' => 'أحمد زيدان', 'machine_code' => '2531', 'mark' => 67,  'duration' => 20, 'start_at' => '2026-02-10 23:26:44', 'created_at' => '2026-02-10 21:26:44', 'updated_at' => '2026-02-10 21:46:33'],
            ['id' => 8, 'form_id' => 7, 'user_id' => 793,  'name' => 'أدمن للتجربة', 'machine_code' => '0000', 'mark' => 22, 'duration' => 1,  'start_at' => '2026-04-27 17:14:42', 'created_at' => '2026-04-27 14:14:42', 'updated_at' => '2026-04-27 14:15:28'],
            ['id' => 9, 'form_id' => 8, 'user_id' => 793,  'name' => 'أدمن للتجربة', 'machine_code' => '0000', 'mark' => 0,  'duration' => 61, 'start_at' => '2026-04-27 17:39:30', 'created_at' => '2026-04-27 14:39:30', 'updated_at' => '2026-04-27 14:39:43'],
        ], ['id'], ['form_id', 'user_id', 'name', 'machine_code', 'mark', 'duration', 'start_at', 'updated_at']);
    }
}
