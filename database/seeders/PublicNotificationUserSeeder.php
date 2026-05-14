<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicNotificationUserSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('public_notification_users', [
            ['id' => 1, 'public_notification_id' => 2, 'user_code' => '2297', 'created_at' => '2026-01-11 15:47:51', 'updated_at' => '2026-01-11 15:47:51'],
            ['id' => 2, 'public_notification_id' => 2, 'user_code' => '23C2', 'created_at' => '2026-01-11 15:47:51', 'updated_at' => '2026-01-11 15:47:51'],
            ['id' => 3, 'public_notification_id' => 3, 'user_code' => '2531', 'created_at' => '2026-01-11 15:54:25', 'updated_at' => '2026-01-11 15:54:25'],
        ], ['id'], ['public_notification_id', 'user_code', 'updated_at']);
    }
}
