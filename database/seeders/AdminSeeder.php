<?php

namespace Database\Seeders;

use App\Models\Admin;
use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('admins', [
            [
                'id' => 1,
                'name' => 'محمد سعيد',
                'email' => 'dev.mohamedsaid@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$uX7nJ210OYEwlLncfC6DMeBw5YZlywjTR9FwwUWVqrTdN7pqNpHRK',
                'remember_token' => null,
                'created_at' => '2025-07-06 18:53:02',
                'updated_at' => '2025-07-08 20:22:23',
            ],
        ], ['id'], ['name', 'email', 'password', 'updated_at']);

        DB::table('model_has_roles')->updateOrInsert(
            ['role_id' => 1, 'model_type' => Admin::class, 'model_id' => 1],
            []
        );

        $superAdmin = Admin::find(1);
        $role = Role::where(['name' => 'superAdmin', 'guard_name' => 'admin'])->first();

        if ($superAdmin && $role && ! $superAdmin->hasRole($role->name)) {
            $superAdmin->assignRole($role);
        }
    }
}
