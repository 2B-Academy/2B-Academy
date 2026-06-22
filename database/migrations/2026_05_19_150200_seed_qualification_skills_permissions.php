<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotently registers the qualification-skills permissions for the
 * admin guard and attaches them to the Super Admin role (role_id = 1),
 * matching the convention used in PermissionTableSeeder.
 *
 * Kept in a migration so production deployments do not need a manual
 * seeder run — the Spatie permission middleware on the new admin
 * controller will pass immediately for existing admins.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $now = Carbon::now();

        $permissions = [
            'qualification-skills-index',
            'qualification-skills-create',
            'qualification-skills-edit',
            'qualification-skills-delete',
        ];

        $rows = array_map(static fn (string $name) => [
            'table_name' => 'qualification-skills',
            'name'       => $name,
            'guard_name' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ], $permissions);

        DB::table('permissions')->upsert(
            $rows,
            ['name', 'guard_name'],
            ['table_name', 'updated_at']
        );

        if (! Schema::hasTable('role_has_permissions') || ! Schema::hasTable('roles')) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->where('guard_name', 'admin')
            ->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        $adminRoleIds = DB::table('roles')
            ->where('guard_name', 'admin')
            ->pluck('id');

        if ($adminRoleIds->isEmpty()) {
            return;
        }

        $payload = [];
        foreach ($adminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                $payload[] = [
                    'permission_id' => $permissionId,
                    'role_id'       => $roleId,
                ];
            }
        }

        DB::table('role_has_permissions')->upsert(
            $payload,
            ['permission_id', 'role_id'],
            []
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissions = [
            'qualification-skills-index',
            'qualification-skills-create',
            'qualification-skills-edit',
            'qualification-skills-delete',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->where('guard_name', 'admin')
            ->pluck('id');

        if (Schema::hasTable('role_has_permissions') && $permissionIds->isNotEmpty()) {
            DB::table('role_has_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        DB::table('permissions')
            ->whereIn('name', $permissions)
            ->where('guard_name', 'admin')
            ->delete();
    }
};
