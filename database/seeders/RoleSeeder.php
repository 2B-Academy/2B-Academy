<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('roles', [
            [
                'id' => 1,
                'name' => 'superAdmin',
                'guard_name' => 'admin',
                'created_at' => '2025-07-06 18:53:02',
                'updated_at' => '2025-07-06 18:53:02',
            ],
        ], ['id'], ['name', 'guard_name', 'updated_at']);
    }
}
