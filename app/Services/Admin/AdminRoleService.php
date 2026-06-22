<?php

namespace App\Services\Admin;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Service backing the 2026 admin Roles redesign.
 *
 * The legacy public role endpoints (App\Http\Controllers\apis\RoleController
 * @ /api/v1/roles) remain untouched and continue to serve their original
 * consumers. This service exposes a focused, additive surface for the new
 * admin overview:
 *
 *   - bilingual identity (name_en / name_ar / description_en / description_ar)
 *   - 5-swatch color palette
 *   - a curated 16-section "dashboard view" catalog (Spatie permissions
 *     under the `admin` guard, grouped by Figma section)
 *   - live `user_count` per role (counted from Spatie's model_has_roles
 *     pivot — works for admins and any other model using HasRoles).
 *
 * No legacy MVC code is touched.
 */
class AdminRoleService
{
    /** @var array<int,string> Allowed badge colors. */
    public const COLORS = ['teal', 'green', 'orange', 'red', 'blue'];

    /** @var array<int,string> Groups in their Figma display order. */
    public const SECTION_GROUPS = ['Main', 'Learning Operation', 'Manage Competency', 'System'];

    /** @var array<string,string> view-key → English label (mirrors the seed migration). */
    private const SECTION_LABELS_EN = [
        'view-dashboard'       => 'Dashboard',
        'view-inbox'           => 'Inbox',
        'view-courses'         => 'Courses',
        'view-assignments'     => 'Assignments',
        'view-quizzes'         => 'Quizzes',
        'view-ratings'         => 'Ratings',
        'view-resources'       => 'Resources',
        'view-job-titles'      => 'Job Titles',
        'view-qualifications'  => 'Qualifications',
        'view-certificates'    => 'Certificates',
        'view-categories'      => 'Categories',
        'view-reports'         => 'Reports',
        'view-users'           => 'Users',
        'view-platform-config' => 'Platform Config',
        'view-audit-log'       => 'Audit Log',
        'view-roles'           => 'Roles',
        'view-controllers'     => 'Controllers',
    ];

    /** @var array<string,string> view-key → Arabic label. */
    private const SECTION_LABELS_AR = [
        'view-dashboard'       => 'لوحة التحكم',
        'view-inbox'           => 'الوارد',
        'view-courses'         => 'الدورات',
        'view-assignments'     => 'الواجبات',
        'view-quizzes'         => 'الاختبارات',
        'view-ratings'         => 'التقييمات',
        'view-resources'       => 'الموارد',
        'view-job-titles'      => 'المسميات الوظيفية',
        'view-qualifications'  => 'المؤهلات',
        'view-certificates'    => 'الشهادات',
        'view-categories'      => 'الفئات',
        'view-reports'         => 'التقارير',
        'view-users'           => 'المستخدمون',
        'view-platform-config' => 'إعدادات المنصة',
        'view-audit-log'       => 'سجل التدقيق',
        'view-roles'           => 'الأدوار',
        'view-controllers'     => 'المشرفون',
    ];

    /* ------------------------------------------------------------------ *
     |  CATALOG                                                           |
     * ------------------------------------------------------------------ */

    /**
     * Return every view-* permission grouped exactly as the Figma form
     * lays them out: 4 groups × N items.
     *
     * @return array{
     *   total:int,
     *   groups:array<int,array{
     *     key:string,
     *     label:string,
     *     items:array<int,array{key:string,label:string}>
     *   }>
     * }
     */
    public function sectionCatalog(): array
    {
        $locale = app()->getLocale();
        $labels = $locale === 'ar' ? self::SECTION_LABELS_AR : self::SECTION_LABELS_EN;

        $perms = DB::table('permissions')
            ->where('guard_name', 'admin')
            ->whereIn('name', array_keys(self::SECTION_LABELS_EN))
            ->orderBy('table_name')->orderBy('name')
            ->get(['name', 'table_name']);

        $byGroup = [];
        foreach ($perms as $p) {
            $group = $p->table_name ?: 'System';
            $byGroup[$group][] = [
                'key'   => (string) $p->name,
                'label' => $labels[$p->name] ?? $this->humanise((string) $p->name),
            ];
        }

        $groups = [];
        $total  = 0;
        foreach (self::SECTION_GROUPS as $group) {
            $items   = $byGroup[$group] ?? [];
            $total  += count($items);
            $groups[] = [
                'key'   => $this->groupKey($group),
                'label' => $this->translateGroupLabel($group, $locale),
                'items' => $items,
            ];
        }

        return ['total' => $total, 'groups' => $groups];
    }

    /* ------------------------------------------------------------------ *
     |  LIST                                                              |
     * ------------------------------------------------------------------ */

    /**
     * Return every admin-guard role with its bilingual identity, color,
     * `is_system` flag, `user_count`, and the list of selected view
     * sections.
     *
     * The endpoint is intentionally NOT paginated — the Figma renders a
     * card-grid that the admin scans at a glance, and the total number of
     * roles is bounded.
     *
     * @param  string|null  $search  Free-text on name / description.
     * @return array{
     *   total_views:int,
     *   roles:array<int,array<string,mixed>>
     * }
     */
    public function list(?string $search = null): array
    {
        $locale  = app()->getLocale();
        $catalog = $this->sectionCatalog();

        $query = DB::table('roles')->where('guard_name', 'admin');

        if ($search) {
            $needle = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
            $query->where(function ($w) use ($needle) {
                $w->where('name',           'LIKE', $needle)
                  ->orWhere('name_en',        'LIKE', $needle)
                  ->orWhere('name_ar',        'LIKE', $needle)
                  ->orWhere('description_en', 'LIKE', $needle)
                  ->orWhere('description_ar', 'LIKE', $needle);
            });
        }

        $rows = $query
            ->orderByDesc('is_system')
            ->orderBy('name_en')
            ->get();

        $roleIds = $rows->pluck('id')->all();

        // Selected view-* permissions per role.
        $sectionsByRole = $this->loadSectionsByRole($roleIds);

        // User counts per role from Spatie's pivot.
        $userCountByRole = $this->loadUserCounts($roleIds);

        $roles = $rows->map(function ($r) use ($locale, $sectionsByRole, $userCountByRole, $catalog) {
            $selected = $sectionsByRole[$r->id] ?? [];
            return $this->shape($r, $selected, (int) ($userCountByRole[$r->id] ?? 0), $catalog['total'], $locale);
        })->all();

        return [
            'total_views' => $catalog['total'],
            'roles'       => $roles,
        ];
    }

    /* ------------------------------------------------------------------ *
     |  SHOW                                                              |
     * ------------------------------------------------------------------ */

    public function show(int $id): array
    {
        $row = DB::table('roles')->where('id', $id)->where('guard_name', 'admin')->first();
        if (!$row) {
            throw (new ModelNotFoundException())->setModel(\Spatie\Permission\Models\Role::class, [$id]);
        }

        $catalog = $this->sectionCatalog();
        $sections = $this->loadSectionsByRole([$row->id])[$row->id] ?? [];
        $userCount = (int) ($this->loadUserCounts([$row->id])[$row->id] ?? 0);

        return $this->shape($row, $sections, $userCount, $catalog['total'], app()->getLocale());
    }

    /* ------------------------------------------------------------------ *
     |  CREATE / UPDATE                                                   |
     * ------------------------------------------------------------------ */

    /**
     * @param  array{
     *   name_en:string, name_ar:string,
     *   description_en?:string|null, description_ar?:string|null,
     *   color?:string|null, view_keys?:array<int,string>|null
     * }  $data
     */
    public function create(array $data): array
    {
        $color = $this->normaliseColor($data['color'] ?? null);
        $name  = $this->machineNameFor($data['name_en']);

        return DB::transaction(function () use ($data, $color, $name) {
            $now = now();

            $id = (int) DB::table('roles')->insertGetId([
                'name'           => $name,
                'guard_name'     => 'admin',
                'name_en'        => trim($data['name_en']),
                'name_ar'        => trim($data['name_ar']),
                'description_en' => $data['description_en'] ?? null,
                'description_ar' => $data['description_ar'] ?? null,
                'color'          => $color,
                'is_system'      => false,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $this->syncSections($id, $data['view_keys'] ?? []);

            return $this->show($id);
        });
    }

    /**
     * @param  array<string,mixed>  $data
     */
    public function update(int $id, array $data): array
    {
        $row = DB::table('roles')->where('id', $id)->where('guard_name', 'admin')->first();
        if (!$row) {
            throw (new ModelNotFoundException())->setModel(\Spatie\Permission\Models\Role::class, [$id]);
        }

        DB::transaction(function () use ($row, $data) {
            $payload = [
                'updated_at' => now(),
            ];

            // System roles preserve their canonical machine `name` and
            // their `is_system` flag — only the cosmetic fields and
            // section assignments may change.
            if (!$row->is_system && array_key_exists('name_en', $data) && trim((string) $data['name_en']) !== '') {
                $payload['name_en'] = trim((string) $data['name_en']);
            }
            if (!$row->is_system && array_key_exists('name_ar', $data)) {
                $payload['name_ar'] = trim((string) $data['name_ar']);
            }

            if ($row->is_system && array_key_exists('name_en', $data) && trim((string) $data['name_en']) !== '') {
                $payload['name_en'] = trim((string) $data['name_en']);
            }
            if ($row->is_system && array_key_exists('name_ar', $data)) {
                $payload['name_ar'] = trim((string) $data['name_ar']);
            }

            if (array_key_exists('description_en', $data)) {
                $payload['description_en'] = $data['description_en'] ?: null;
            }
            if (array_key_exists('description_ar', $data)) {
                $payload['description_ar'] = $data['description_ar'] ?: null;
            }
            if (array_key_exists('color', $data) && $data['color'] !== null) {
                $payload['color'] = $this->normaliseColor((string) $data['color']);
            }

            DB::table('roles')->where('id', $row->id)->update($payload);

            if (array_key_exists('view_keys', $data)) {
                $this->syncSections((int) $row->id, $data['view_keys'] ?? []);
            }
        });

        return $this->show($id);
    }

    /* ------------------------------------------------------------------ *
     |  DELETE                                                            |
     * ------------------------------------------------------------------ */

    public function delete(int $id): void
    {
        $row = DB::table('roles')->where('id', $id)->where('guard_name', 'admin')->first();
        if (!$row) {
            throw (new ModelNotFoundException())->setModel(\Spatie\Permission\Models\Role::class, [$id]);
        }

        if ($row->is_system) {
            throw ValidationException::withMessages([
                'role' => ['System roles cannot be deleted.'],
            ]);
        }

        // Surface a clear 422 if the role is still assigned — Spatie
        // would otherwise leave dangling rows in model_has_roles.
        $usersAttached = DB::table('model_has_roles')->where('role_id', $row->id)->count();
        if ($usersAttached > 0) {
            throw ValidationException::withMessages([
                'role' => ["Unassign all {$usersAttached} users before deleting this role."],
            ]);
        }

        DB::transaction(function () use ($row) {
            DB::table('role_has_permissions')->where('role_id', $row->id)->delete();
            DB::table('roles')->where('id', $row->id)->delete();
        });
    }

    /* ------------------------------------------------------------------ *
     |  INTERNALS                                                         |
     * ------------------------------------------------------------------ */

    /**
     * Persist the role↔view-permission selection without touching any of
     * the role's *other* (legacy CRUD) permission assignments.  Only the
     * 16 view-* keys are mutated, so admins can keep their granular
     * "courses-create"/"users-edit"/… permissions intact.
     *
     * @param  array<int,string>  $viewKeys
     */
    private function syncSections(int $roleId, array $viewKeys): void
    {
        $allViewIds = DB::table('permissions')
            ->where('guard_name', 'admin')
            ->whereIn('name', array_keys(self::SECTION_LABELS_EN))
            ->pluck('id', 'name');

        $desired = collect($viewKeys)
            ->map(static fn ($v) => (string) $v)
            ->unique()
            ->filter(fn ($key) => isset($allViewIds[$key]))
            ->map(fn ($key) => (int) $allViewIds[$key])
            ->values()
            ->all();

        $currentView = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->whereIn('permission_id', $allViewIds->values()->all())
            ->pluck('permission_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $toAdd    = array_diff($desired,     $currentView);
        $toRemove = array_diff($currentView, $desired);

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

    /**
     * @param  array<int,int>  $roleIds
     * @return array<int,array<int,string>>  role_id => list of view-* keys
     */
    private function loadSectionsByRole(array $roleIds): array
    {
        if (empty($roleIds)) return [];

        return DB::table('role_has_permissions as rhp')
            ->join('permissions as p', 'p.id', '=', 'rhp.permission_id')
            ->whereIn('rhp.role_id', $roleIds)
            ->whereIn('p.name', array_keys(self::SECTION_LABELS_EN))
            ->where('p.guard_name', 'admin')
            ->select('rhp.role_id', 'p.name')
            ->get()
            ->groupBy('role_id')
            ->map(fn ($group) => $group->pluck('name')->map(fn ($v) => (string) $v)->all())
            ->all();
    }

    /**
     * @param  array<int,int>  $roleIds
     * @return array<int,int>  role_id => user_count
     */
    private function loadUserCounts(array $roleIds): array
    {
        if (empty($roleIds)) return [];

        return DB::table('model_has_roles')
            ->whereIn('role_id', $roleIds)
            ->select('role_id', DB::raw('COUNT(*) AS c'))
            ->groupBy('role_id')
            ->pluck('c', 'role_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * Build the API row shape for a single role.
     *
     * @param  array<int,string>  $selectedKeys
     * @return array<string,mixed>
     */
    private function shape(object $r, array $selectedKeys, int $userCount, int $totalViews, string $locale): array
    {
        $nameEn = $r->name_en ?: $this->humanise((string) $r->name);
        $nameAr = $r->name_ar ?: null;
        $display = $locale === 'ar' ? ($nameAr ?: $nameEn) : ($nameEn ?: $nameAr);

        $descEn = $r->description_en ?: null;
        $descAr = $r->description_ar ?: null;
        $descDisplay = $locale === 'ar' ? ($descAr ?: $descEn) : ($descEn ?: $descAr);

        $color = (string) ($r->color ?? 'teal');

        return [
            'id'              => (int) $r->id,
            'machine_name'    => (string) $r->name,
            'guard_name'      => (string) $r->guard_name,
            'name'            => (string) $display,
            'name_en'         => $nameEn ?: null,
            'name_ar'         => $nameAr,
            'description'     => $descDisplay,
            'description_en'  => $descEn,
            'description_ar'  => $descAr,
            'color'           => $color,
            'is_system'       => (bool) $r->is_system,
            'user_count'      => $userCount,
            'view_keys'       => array_values($selectedKeys),
            'view_count'      => count($selectedKeys),
            'view_total'      => $totalViews,
            'view_percentage' => $totalViews > 0 ? (int) round((count($selectedKeys) / $totalViews) * 100) : 0,
            'avatar_initial'  => $this->initial($display ?: 'R'),
            'created_at'      => isset($r->created_at) ? (string) $r->created_at : null,
        ];
    }

    private function normaliseColor(?string $color): string
    {
        $color = strtolower(trim((string) $color));
        return in_array($color, self::COLORS, true) ? $color : 'teal';
    }

    private function machineNameFor(string $nameEn): string
    {
        $base = trim($nameEn) !== '' ? trim($nameEn) : 'role';
        $slug = preg_replace('/[^A-Za-z0-9]+/', '-', $base) ?? 'role';
        $slug = strtolower(trim($slug, '-')) ?: 'role';

        // Ensure uniqueness (Spatie unique key is name+guard).
        $candidate = $slug;
        $i = 2;
        while (DB::table('roles')
            ->where('name', $candidate)
            ->where('guard_name', 'admin')
            ->exists()
        ) {
            $candidate = "{$slug}-{$i}";
            $i++;
        }

        return $candidate;
    }

    private function groupKey(string $group): string
    {
        return strtolower(str_replace(' ', '_', $group));
    }

    private function translateGroupLabel(string $group, string $locale): string
    {
        if ($locale !== 'ar') return $group;

        return match ($group) {
            'Main'                => 'الرئيسية',
            'Learning Operation'  => 'العمليات التعليمية',
            'Manage Competency'   => 'إدارة الكفاءات',
            'System'              => 'النظام',
            default               => $group,
        };
    }

    private function humanise(string $machine): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $machine));
    }

    private function initial(string $name): string
    {
        $first = mb_substr(trim($name), 0, 1);
        return $first === '' ? 'R' : mb_strtoupper($first);
    }
}
