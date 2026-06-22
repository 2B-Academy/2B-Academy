<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the 16 dashboard "view-*" permissions and the 4 system roles
 * required by the 2026 admin Roles redesign.
 *
 * Strictly additive and idempotent:
 *   - Permissions are inserted only if they don't already exist.
 *   - System role rows are upserted by (name, guard_name) so re-running
 *     the migration on an already-seeded database is a no-op.
 *
 * The view permissions live under the `admin` guard so they slot into
 * Spatie's existing role/permission machinery without any code change.
 * Their `table_name` matches the Figma group ("Main", "Learning
 * Operation", "Manage Competency", "System") so the AdminRoleService
 * can group them for the UI by grouping on `table_name`.
 */
return new class extends Migration
{
    /** @var array<string,array<int,array{key:string,label_en:string,label_ar:string}>> */
    private const VIEW_GROUPS = [
        'Main' => [
            ['key' => 'view-dashboard', 'label_en' => 'Dashboard', 'label_ar' => 'لوحة التحكم'],
            ['key' => 'view-inbox',     'label_en' => 'Inbox',     'label_ar' => 'الوارد'],
        ],
        'Learning Operation' => [
            ['key' => 'view-courses',     'label_en' => 'Courses',     'label_ar' => 'الدورات'],
            ['key' => 'view-assignments', 'label_en' => 'Assignments', 'label_ar' => 'الواجبات'],
            ['key' => 'view-quizzes',     'label_en' => 'Quizzes',     'label_ar' => 'الاختبارات'],
            ['key' => 'view-ratings',     'label_en' => 'Ratings',     'label_ar' => 'التقييمات'],
            ['key' => 'view-resources',   'label_en' => 'Resources',   'label_ar' => 'الموارد'],
        ],
        'Manage Competency' => [
            ['key' => 'view-job-titles',     'label_en' => 'Job Titles',     'label_ar' => 'المسميات الوظيفية'],
            ['key' => 'view-qualifications', 'label_en' => 'Qualifications', 'label_ar' => 'المؤهلات'],
            ['key' => 'view-certificates',   'label_en' => 'Certificates',   'label_ar' => 'الشهادات'],
            ['key' => 'view-categories',     'label_en' => 'Categories',     'label_ar' => 'الفئات'],
            ['key' => 'view-reports',        'label_en' => 'Reports',        'label_ar' => 'التقارير'],
        ],
        'System' => [
            ['key' => 'view-users',           'label_en' => 'Users',           'label_ar' => 'المستخدمون'],
            ['key' => 'view-platform-config', 'label_en' => 'Platform Config', 'label_ar' => 'إعدادات المنصة'],
            ['key' => 'view-audit-log',       'label_en' => 'Audit Log',       'label_ar' => 'سجل التدقيق'],
            ['key' => 'view-roles',           'label_en' => 'Roles',           'label_ar' => 'الأدوار'],
        ],
    ];

    /** Default system-role definitions. */
    private const SYSTEM_ROLES = [
        [
            'name'         => 'superAdmin',     // legacy machine key (already exists)
            'name_en'      => 'Super Admin',
            'name_ar'      => 'مدير عام',
            'description_en' => 'Full access to all platform features and settings.',
            'description_ar' => 'وصول كامل إلى جميع ميزات المنصة وإعداداتها.',
            'color'        => 'teal',
            'permissions'  => '*',              // all view-* permissions
        ],
        [
            'name'         => 'admin',
            'name_en'      => 'Admin',
            'name_ar'      => 'مدير',
            'description_en' => 'Manages courses, users, and compliance. Cannot access Roles or Audit Log.',
            'description_ar' => 'يدير الدورات والمستخدمين والامتثال. لا يمكنه الوصول إلى الأدوار أو سجل التدقيق.',
            'color'        => 'blue',
            'permissions'  => [
                'view-dashboard', 'view-inbox',
                'view-courses', 'view-assignments', 'view-quizzes', 'view-ratings', 'view-resources',
                'view-job-titles', 'view-qualifications', 'view-certificates', 'view-categories', 'view-reports',
                'view-users', 'view-platform-config',
            ],
        ],
        [
            'name'         => 'instructor',
            'name_en'      => 'Instructor',
            'name_ar'      => 'مُدرّب',
            'description_en' => 'Delivers courses, grades assignments, manages cohorts.',
            'description_ar' => 'يقدم الدورات ويقيّم الواجبات ويدير المجموعات.',
            'color'        => 'green',
            'permissions'  => [
                'view-dashboard', 'view-inbox',
                'view-courses', 'view-assignments', 'view-quizzes', 'view-ratings', 'view-resources',
            ],
        ],
        [
            'name'         => 'reports-viewer',
            'name_en'      => 'Reports Viewer',
            'name_ar'      => 'مشاهد التقارير',
            'description_en' => 'Read-only access to reports and compliance data.',
            'description_ar' => 'وصول للقراءة فقط إلى التقارير وبيانات الامتثال.',
            'color'        => 'orange',
            'permissions'  => [
                'view-dashboard',
                'view-qualifications', 'view-certificates', 'view-reports',
            ],
        ],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            return;
        }

        $now = Carbon::now();

        // -- 1) seed the 16 view-* permissions ---------------------------
        $permissionIdByKey = [];

        foreach (self::VIEW_GROUPS as $group => $rows) {
            foreach ($rows as $row) {
                $existing = DB::table('permissions')
                    ->where('name', $row['key'])
                    ->where('guard_name', 'admin')
                    ->first();

                if ($existing) {
                    // Keep the row but ensure table_name matches the group.
                    if (Schema::hasColumn('permissions', 'table_name')) {
                        DB::table('permissions')
                            ->where('id', $existing->id)
                            ->where(function ($q) use ($group) {
                                $q->whereNull('table_name')
                                  ->orWhere('table_name', '!=', $group);
                            })
                            ->update(['table_name' => $group, 'updated_at' => $now]);
                    }
                    $permissionIdByKey[$row['key']] = (int) $existing->id;
                    continue;
                }

                $payload = [
                    'name'       => $row['key'],
                    'guard_name' => 'admin',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (Schema::hasColumn('permissions', 'table_name')) {
                    $payload['table_name'] = $group;
                }

                $permissionIdByKey[$row['key']] = (int) DB::table('permissions')->insertGetId($payload);
            }
        }

        // -- 2) seed / upsert the 4 system roles --------------------------
        $allViewKeys = array_keys($permissionIdByKey);

        foreach (self::SYSTEM_ROLES as $def) {
            $existing = DB::table('roles')
                ->where('name', $def['name'])
                ->where('guard_name', 'admin')
                ->first();

            $roleData = [
                'name_en'        => $def['name_en'],
                'name_ar'        => $def['name_ar'],
                'description_en' => $def['description_en'],
                'description_ar' => $def['description_ar'],
                'color'          => $def['color'],
                'is_system'      => true,
                'updated_at'     => $now,
            ];

            if ($existing) {
                DB::table('roles')->where('id', $existing->id)->update($roleData);
                $roleId = (int) $existing->id;
            } else {
                $roleId = (int) DB::table('roles')->insertGetId(array_merge($roleData, [
                    'name'       => $def['name'],
                    'guard_name' => 'admin',
                    'created_at' => $now,
                ]));
            }

            // Sync view-* permissions (without touching any other
            // existing permissions on the role — we only add/remove the
            // 16 dashboard views).
            $desiredKeys = $def['permissions'] === '*' ? $allViewKeys : (array) $def['permissions'];
            $desiredIds  = array_values(array_intersect_key(
                $permissionIdByKey,
                array_flip($desiredKeys),
            ));

            $currentViewPivot = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->whereIn('permission_id', array_values($permissionIdByKey))
                ->pluck('permission_id')
                ->map(fn ($v) => (int) $v)
                ->all();

            $toAdd    = array_diff($desiredIds,     $currentViewPivot);
            $toRemove = array_diff($currentViewPivot, $desiredIds);

            foreach ($toAdd as $pid) {
                DB::table('role_has_permissions')->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $pid,
                ]);
            }
            if (!empty($toRemove)) {
                DB::table('role_has_permissions')
                    ->where('role_id', $roleId)
                    ->whereIn('permission_id', $toRemove)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        $allKeys = [];
        foreach (self::VIEW_GROUPS as $group => $rows) {
            foreach ($rows as $row) {
                $allKeys[] = $row['key'];
            }
        }

        // Detach pivot first (cascade-on-delete is not guaranteed under
        // SQLite test runs).
        $permIds = DB::table('permissions')
            ->whereIn('name', $allKeys)
            ->where('guard_name', 'admin')
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('id', $permIds)->delete();
    }
};
