<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Promotes "Controllers" (portal-administrator management) to a
 * first-class dashboard view governed by the 2026 Roles redesign.
 *
 *   1. Seeds the `view-controllers` permission under the `admin` guard
 *      in the "System" group (idempotent — only inserts if missing).
 *   2. Grants it to the legacy `superAdmin` role and the new `admin`
 *      system role so existing privileged accounts retain access on
 *      the very next /me refresh.
 *
 * Strictly additive — no existing permission, role, or pivot row is
 * touched. Custom roles get `view-controllers` only when an admin
 * explicitly checks it in the Roles UI.
 */
return new class extends Migration
{
    private const KEY   = 'view-controllers';
    private const GROUP = 'System';

    public function up(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            return;
        }

        $now = Carbon::now();

        // -- 1) Insert the permission if missing -----------------------
        $existing = DB::table('permissions')
            ->where('name', self::KEY)
            ->where('guard_name', 'admin')
            ->first();

        if ($existing) {
            if (Schema::hasColumn('permissions', 'table_name')) {
                DB::table('permissions')
                    ->where('id', $existing->id)
                    ->where(function ($q) {
                        $q->whereNull('table_name')
                          ->orWhere('table_name', '!=', self::GROUP);
                    })
                    ->update(['table_name' => self::GROUP, 'updated_at' => $now]);
            }
            $permissionId = (int) $existing->id;
        } else {
            $payload = [
                'name'       => self::KEY,
                'guard_name' => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (Schema::hasColumn('permissions', 'table_name')) {
                $payload['table_name'] = self::GROUP;
            }
            $permissionId = (int) DB::table('permissions')->insertGetId($payload);
        }

        // -- 2) Grant to the privileged system roles -------------------
        $autoGrantRoles = ['superAdmin', 'admin'];

        $roleRows = DB::table('roles')
            ->where('guard_name', 'admin')
            ->whereIn('name', $autoGrantRoles)
            ->pluck('id');

        foreach ($roleRows as $roleId) {
            $alreadyAttached = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$alreadyAttached) {
                DB::table('role_has_permissions')->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $permId = DB::table('permissions')
            ->where('name', self::KEY)
            ->where('guard_name', 'admin')
            ->value('id');

        if (!$permId) return;

        DB::table('role_has_permissions')->where('permission_id', $permId)->delete();
        DB::table('model_has_permissions')->where('permission_id', $permId)->delete();
        DB::table('permissions')->where('id', $permId)->delete();
    }
};
