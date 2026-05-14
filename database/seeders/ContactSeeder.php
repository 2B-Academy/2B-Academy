<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('contacts', [
            [
                'id' => 2,
                'name' => 'محمد سعيد',
                'email' => 'dev@dev.com',
                'mobile' => '01015454545',
                'message' => 'عندي مشكلة في الاداره',
                'is_seen' => 1,
                'created_at' => '2025-09-10 11:14:58',
                'updated_at' => '2025-09-10 11:15:41',
            ],
        ], ['id'], ['name', 'email', 'mobile', 'message', 'is_seen', 'updated_at']);
    }
}
