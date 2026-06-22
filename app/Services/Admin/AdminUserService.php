<?php

namespace App\Services\Admin;

use App\Http\Traits\HasFile;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorImpl;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Service backing the 2026 admin Users redesign.
 *
 * The LMS keeps three separate person-tables that the Figma redesign
 * unifies in a single list:
 *
 *   - users        →  learners + HR-synced employees
 *   - instructors  →  course instructor profiles (bilingual names)
 *   - admins       →  back-office login accounts
 *
 * This service therefore exposes a "virtual" composite resource keyed by a
 * (`source`, `id`) tuple.  Every read path UNION-ALLs across the three
 * tables; every write path routes to the table indicated by `source`.
 *
 * Strict additivity is preserved — none of the legacy services, models, or
 * controllers (App\Services\UserService, App\Services\AdminService, etc.)
 * are touched.
 */
class AdminUserService
{
    use HasFile;

    /** @var array<int,string> Source identifiers used by the API. */
    public const SOURCES = ['user', 'instructor', 'admin'];

    /**
     * @var array<int,string>
     *
     * Roles whose machine names map to a dedicated person-table bucket.
     * Anything outside this list lands in `users` and is attached to the
     * Spatie role via `model_has_roles`. This list is intentionally not
     * exposed to the UI — the dropdown is sourced from the `roles` table
     * (Figma 529:38878), so admins can add new custom roles without a
     * code change.
     */
    private const BUCKETED_ROLES = ['admin', 'instructor', 'learner'];

    /* ------------------------------------------------------------------ *
     |  PAGINATED LIST (UNION across the three tables)                    |
     * ------------------------------------------------------------------ */

    /**
     * Paginate the unified people list.
     *
     * @param  string|null         $role           One of self::ROLES or null/"all".
     * @param  string|null         $status         active | inactive | deactivated
     * @param  string|null         $search         Free-text filter on name / email.
     * @param  array<int,int>|null $instructorIds  Optional instructor-ids filter
     *                                             (sub-filter on the Instructors pill).
     */
    public function paginate(
        ?string $role          = null,
        ?string $status        = null,
        ?string $search        = null,
        ?array  $instructorIds = null,
        int     $perPage       = 15,
    ): LengthAwarePaginator {
        $sub = $this->unifiedQuery();

        $query = DB::query()->fromSub($sub, 'p');

        if ($role) {
            // Accept either a bucketed system role (`admin`/`instructor`/
            // `learner`) — which matches `role_key` 1:1 — or any custom
            // role machine name from the `roles` table; the latter is
            // matched against the Spatie `model_has_roles` pivot.
            if (in_array($role, self::BUCKETED_ROLES, true)) {
                $query->where('p.role_key', $role);
            } else {
                $customIds = $this->idsForCustomRole($role);
                if ($customIds === []) {
                    // Force an empty result set if no users are attached.
                    $query->whereRaw('1=0');
                } else {
                    $query->whereIn(DB::raw('CONCAT(p.source, ":", p.id)'), $customIds);
                }
            }
        }

        if ($status) {
            $query->where('p.status', $status);
        }

        if (!empty($instructorIds)) {
            $query->where('p.source', 'instructor')
                  ->whereIn('p.id', $instructorIds);
        }

        if ($search) {
            $needle = "%{$search}%";
            $query->where(function ($w) use ($needle) {
                $w->where('p.name_en', 'LIKE', $needle)
                  ->orWhere('p.name_ar', 'LIKE', $needle)
                  ->orWhere('p.email',   'LIKE', $needle);
            });
        }

        $query->orderBy('p.name_en');

        $page  = (int) (Paginator::resolveCurrentPage() ?: 1);
        $total = (clone $query)->count();
        $rows  = $query->forPage($page, $perPage)->get();

        // Attach computed compliance % for the learner rows.
        $rows = $this->attachCompliance($rows);

        return new PaginatorImpl(
            items:       $rows,
            total:       $total,
            perPage:     $perPage,
            currentPage: $page,
            options:     [
                'path'     => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        );
    }

    /* ------------------------------------------------------------------ *
     |  SUMMARY (KPI cards)                                               |
     * ------------------------------------------------------------------ */

    /**
     * Build the four KPI cards shown at the top of the page.
     *
     * @return array{
     *   total_users:int,
     *   instructors:int,
     *   admins:int,
     *   inactive:int
     * }
     */
    public function summary(): array
    {
        $users       = (int) DB::table('users')->count();
        $instructors = (int) DB::table('instructors')->count();
        $admins      = (int) DB::table('admins')->count();

        $inactive = 0;
        foreach (['users', 'instructors', 'admins'] as $table) {
            if (Schema::hasColumn($table, 'status')) {
                $inactive += (int) DB::table($table)
                    ->whereIn('status', ['inactive', 'deactivated'])
                    ->count();
            }
        }

        return [
            'total_users' => $users + $instructors + $admins,
            'instructors' => $instructors,
            'admins'      => $admins,
            'inactive'    => $inactive,
        ];
    }

    /* ------------------------------------------------------------------ *
     |  FILTER OPTIONS (instructors sub-filter modal)                     |
     * ------------------------------------------------------------------ */

    /**
     * Payload for the "Instructors" filter modal + the Add/Edit modal's
     * supporting lookups.
     *
     *   roles        →  every admin-guard role from the `roles` table —
     *                   bilingual labels included so the Figma role
     *                   dropdown stays dynamic. Custom roles created by
     *                   the admin appear here without any code change.
     *   instructors  →  every row in the `instructors` table (the
     *                   Instructors filter sub-modal).
     *
     * @return array{
     *   roles:       array<int,array{key:string,label:string,count:int}>,
     *   instructors: array<int,array{id:int,name:string,email:string|null}>
     * }
     */
    public function filterOptions(): array
    {
        $locale = app()->getLocale();

        $roleRows = DB::table('roles')
            ->where('guard_name', 'admin')
            ->orderByDesc('is_system')
            ->orderBy('name_en')
            ->get(['name', 'name_en', 'name_ar']);

        // Counts come from the underlying person table for bucketed
        // system roles (admin/instructor/learner) and from the Spatie
        // pivot for everything else.
        $roles = $roleRows->map(function ($r) use ($locale) {
            $key = (string) $r->name;
            return [
                'key'   => $key,
                'label' => $this->labelForRoleRow($r, $locale),
                'count' => $this->countUsersInRole($key),
            ];
        })->values()->all();

        $instructors = Instructor::query()
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->map(fn (Instructor $i) => [
                'id'    => (int) $i->id,
                'name'  => $this->translateJsonName((string) $i->name, $locale),
                'email' => $i->email,
            ])
            ->values()
            ->all();

        return [
            'roles'       => $roles,
            'instructors' => $instructors,
        ];
    }

    /**
     * Map a persona key to its corresponding `source` token. Bucketed
     * system roles route to their dedicated tables; every other role is
     * a custom Spatie role and lands in `users`.
     */
    private function sourceForRole(string $role): string
    {
        return match ($role) {
            'admin', 'superAdmin' => 'admin',
            'instructor'          => 'instructor',
            default               => 'user',
        };
    }

    /**
     * Translate a `roles` row into a user-facing label, honouring the
     * current Accept-Language locale.
     */
    private function labelForRoleRow(object $row, string $locale): string
    {
        $en = (string) ($row->name_en ?? '');
        $ar = $row->name_ar !== null ? (string) $row->name_ar : '';

        return $locale === 'ar'
            ? ($ar ?: ($en ?: ucfirst((string) $row->name)))
            : ($en ?: ($ar ?: ucfirst((string) $row->name)));
    }

    /**
     * Count how many people are attached to `$roleKey`. For bucketed
     * system roles the count comes from the underlying person table
     * (so a Learner reflects every row in `users`); for everything else
     * we read `model_has_roles` which Spatie populates via assignRole().
     */
    private function countUsersInRole(string $roleKey): int
    {
        if (in_array($roleKey, self::BUCKETED_ROLES, true)) {
            return (int) DB::table($this->tableFor($this->sourceForRole($roleKey)))->count();
        }

        return (int) DB::table('model_has_roles as mhr')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->where('r.name', $roleKey)
            ->where('r.guard_name', 'admin')
            ->count();
    }

    /**
     * Build the `source:id` whitelist for a custom-role filter. Used by
     * `paginate()` to scope the unified query when the dropdown is set
     * to a non-bucketed role.
     *
     * @return array<int,string>
     */
    private function idsForCustomRole(string $roleKey): array
    {
        $rows = DB::table('model_has_roles as mhr')
            ->join('roles as r', 'r.id', '=', 'mhr.role_id')
            ->where('r.name', $roleKey)
            ->where('r.guard_name', 'admin')
            ->get(['mhr.model_type', 'mhr.model_id']);

        return $rows->map(static function ($row) {
            $source = match ($row->model_type) {
                Admin::class      => 'admin',
                Instructor::class => 'instructor',
                User::class       => 'user',
                default           => null,
            };
            return $source ? sprintf('%s:%d', $source, (int) $row->model_id) : null;
        })->filter()->values()->all();
    }

    /* ------------------------------------------------------------------ *
     |  DETAIL                                                            |
     * ------------------------------------------------------------------ */

    /**
     * Look up a single row across the three tables. Returns a stdClass
     * shaped identically to the rows returned by paginate().
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $source, int $id): \stdClass
    {
        $this->assertSource($source);

        $sub = $this->unifiedQuery();

        $row = DB::query()
            ->fromSub($sub, 'p')
            ->where('p.source', $source)
            ->where('p.id', $id)
            ->first();

        if (!$row) {
            throw (new \Illuminate\Database\Eloquent\ModelNotFoundException())
                ->setModel($this->modelClassFor($source), [$id]);
        }

        $rows = $this->attachCompliance(collect([$row]));

        return $rows->first();
    }

    /* ------------------------------------------------------------------ *
     |  CREATE                                                            |
     * ------------------------------------------------------------------ */

    /**
     * Create a row in whichever table matches the chosen role.
     *
     * Bucketed system roles route to their dedicated person table; any
     * other (custom) role lands in `users` and is also attached via the
     * Spatie `model_has_roles` pivot so the role's permissions take
     * effect across the app.
     *
     * @param  array{
     *   name_en:string, name_ar:string, email:string, role:string,
     *   department_name?:string|null, phone?:string|null,
     *   learner_type?:string|null, image?:UploadedFile|null
     * }  $data
     *
     * @return array{source:string,id:int}
     */
    public function create(array $data): array
    {
        $role  = (string) ($data['role'] ?? '');
        $image = $data['image'] ?? null;
        unset($data['image']);

        return DB::transaction(function () use ($data, $role, $image) {
            $created = match ($role) {
                'admin', 'superAdmin' => $this->createAdmin($data, $image),
                'instructor'          => $this->createInstructor($data, $image),
                'learner'             => $this->createLearner($data, $image),
                default               => $this->createLearner($data, $image),
            };

            // For non-bucketed roles, attach the Spatie role so the
            // permissions take effect (we still parked the row in the
            // `users` table as the default learner bucket).
            if (! in_array($role, self::BUCKETED_ROLES, true) && $role !== '' && $role !== 'superAdmin') {
                $model = $this->modelInstance($created['source'], $created['id']);
                if (method_exists($model, 'assignRole')) {
                    try { $model->assignRole($role); } catch (\Throwable $e) { /* no-op */ }
                }
            }

            return $created;
        });
    }

    /**
     * @param  UploadedFile|null  $image
     */
    private function createLearner(array $data, ?UploadedFile $image = null): array
    {
        $systemId = $this->allocateSystemId();

        $user = User::query()->create([
            'system_id'       => $systemId,
            'name'            => $data['name_en'],
            'name_en'         => $data['name_en'],
            'name_ar'         => $data['name_ar'],
            'email'           => $data['email'],
            'phone'           => $data['phone']           ?? null,
            'department_name' => $data['department_name'] ?? null,
            'learner_type'    => $data['learner_type']    ?? 'online',
            'machine_code'    => Str::upper(Str::random(4)),
            'status'          => 'active',
            'image'           => $image ? $this->uploadRequestFile('users', null, null, $image) : null,
        ]);

        return ['source' => 'user', 'id' => (int) $user->id];
    }

    /**
     * @param  UploadedFile|null  $image
     */
    private function createInstructor(array $data, ?UploadedFile $image = null): array
    {
        $instructor = Instructor::query()->create([
            'name'           => ['en' => $data['name_en'], 'ar' => $data['name_ar']],
            'email'          => $data['email'],
            'image'          => $image ? $this->uploadRequestFile('instructors', null, null, $image) : null,
            'bio'            => '',
            'status'         => 'active',
        ]);

        return ['source' => 'instructor', 'id' => (int) $instructor->id];
    }

    /**
     * @param  UploadedFile|null  $image
     */
    private function createAdmin(array $data, ?UploadedFile $image = null): array
    {
        // Admin's $fillable list intentionally omits status / image to
        // protect the legacy login flow — set them directly so we still
        // honour mass-assignment guards.
        $admin = new Admin();
        $admin->name              = $data['name_en'];
        $admin->email             = $data['email'];
        $admin->password          = bcrypt(Str::random(24));
        if (Schema::hasColumn('admins', 'status')) { $admin->status = 'active'; }
        if (Schema::hasColumn('admins', 'image') && $image) {
            $admin->image = $this->uploadRequestFile('admins', null, null, $image);
        }
        $admin->save();

        return ['source' => 'admin', 'id' => (int) $admin->id];
    }

    /* ------------------------------------------------------------------ *
     |  UPDATE                                                            |
     * ------------------------------------------------------------------ */

    /**
     * Patch an existing row.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(string $source, int $id, array $data): \stdClass
    {
        $this->assertSource($source);

        $image = $data['image'] ?? null;
        unset($data['image']);

        DB::transaction(function () use ($source, $id, $data, $image) {
            match ($source) {
                'user'       => $this->updateLearner($id, $data, $image),
                'instructor' => $this->updateInstructor($id, $data, $image),
                'admin'      => $this->updateAdmin($id, $data, $image),
            };

            // Custom-role re-assignment: when the admin picks a
            // non-bucketed role in the Edit modal we sync the Spatie
            // pivot. Bucketed roles can't be changed in-place (they'd
            // require moving the row across person tables) so we leave
            // them to a future migration tool.
            $role = (string) ($data['role'] ?? '');
            if ($role !== '' && ! in_array($role, self::BUCKETED_ROLES, true) && $role !== 'superAdmin') {
                $model = $this->modelInstance($source, $id);
                if (method_exists($model, 'syncRoles')) {
                    try { $model->syncRoles([$role]); } catch (\Throwable $e) { /* no-op */ }
                }
            }
        });

        return $this->show($source, $id);
    }

    private function updateLearner(int $id, array $data, ?UploadedFile $image = null): void
    {
        $user = User::query()->findOrFail($id);

        $payload = collect($data)
            ->only(['name_en', 'name_ar', 'email', 'phone',
                    'department_name', 'learner_type', 'status'])
            ->all();

        if (array_key_exists('name_en', $payload) && $payload['name_en']) {
            $payload['name'] = $payload['name_en'];
        }

        if ($image) {
            $payload['image'] = $this->uploadRequestFile('users', null, null, $image);
        }

        if (!empty($payload)) {
            $user->fill($payload)->save();
        }
    }

    private function updateInstructor(int $id, array $data, ?UploadedFile $image = null): void
    {
        $instructor = Instructor::query()->findOrFail($id);

        if (array_key_exists('name_en', $data) || array_key_exists('name_ar', $data)) {
            // Preserve existing translations and overwrite only the supplied keys.
            $current = $this->currentJsonName((string) $instructor->name);
            $current['en'] = $data['name_en'] ?? $current['en'] ?? '';
            $current['ar'] = $data['name_ar'] ?? $current['ar'] ?? '';
            $instructor->name = $current;
        }

        foreach (['email', 'status'] as $key) {
            if (array_key_exists($key, $data) && Schema::hasColumn('instructors', $key)) {
                $instructor->{$key} = $data[$key];
            }
        }

        if ($image) {
            $instructor->image = $this->uploadRequestFile('instructors', null, null, $image);
        }

        $instructor->save();
    }

    private function updateAdmin(int $id, array $data, ?UploadedFile $image = null): void
    {
        $admin = Admin::query()->findOrFail($id);

        if (!empty($data['name_en'])) {
            $admin->name = $data['name_en'];
        }
        if (array_key_exists('email', $data)) {
            $admin->email = $data['email'];
        }
        if (array_key_exists('status', $data) && Schema::hasColumn('admins', 'status')) {
            $admin->status = $data['status'];
        }
        if ($image && Schema::hasColumn('admins', 'image')) {
            $admin->image = $this->uploadRequestFile('admins', null, null, $image);
        }

        $admin->save();
    }

    /**
     * Resolve an Eloquent instance for a (source, id) tuple — used by
     * `create()` / `update()` to attach Spatie roles after the row has
     * been persisted.
     */
    private function modelInstance(string $source, int $id): \Illuminate\Database\Eloquent\Model
    {
        return match ($source) {
            'admin'      => Admin::query()->findOrFail($id),
            'instructor' => Instructor::query()->findOrFail($id),
            default      => User::query()->findOrFail($id),
        };
    }

    /* ------------------------------------------------------------------ *
     |  DEACTIVATE                                                        |
     * ------------------------------------------------------------------ */

    /**
     * Soft-deactivate by setting `status = 'deactivated'`. Rows are never
     * hard-deleted from any of the three tables so existing relationships
     * (enrollments, exam attempts, course→instructor pivots) remain intact.
     */
    public function deactivate(string $source, int $id): \stdClass
    {
        $this->assertSource($source);

        $table = $this->tableFor($source);
        if (Schema::hasColumn($table, 'status')) {
            DB::table($table)->where('id', $id)->update(['status' => 'deactivated']);
        }

        return $this->show($source, $id);
    }

    /**
     * Reverse of {@see deactivate()} — flips a previously deactivated row
     * back to `status = 'active'`. Used by the "Reactivate" row action.
     */
    public function reactivate(string $source, int $id): \stdClass
    {
        $this->assertSource($source);

        $table = $this->tableFor($source);
        if (Schema::hasColumn($table, 'status')) {
            DB::table($table)->where('id', $id)->update(['status' => 'active']);
        }

        return $this->show($source, $id);
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL — Unified query builder                                  |
     * ------------------------------------------------------------------ */

    /**
     * Build the UNION ALL subquery that projects each table onto a
     * uniform schema.  All callers (paginate / show) layer their filters
     * on top of this query so the column contract stays consistent.
     */
    private function unifiedQuery(): QueryBuilder
    {
        $usersHasStatus       = Schema::hasColumn('users',       'status');
        $usersHasLastActive   = Schema::hasColumn('users',       'last_active_at');
        $usersHasNameEn       = Schema::hasColumn('users',       'name_en');
        $usersHasNameAr       = Schema::hasColumn('users',       'name_ar');
        $usersHasImage        = Schema::hasColumn('users',       'image');

        $instHasStatus        = Schema::hasColumn('instructors', 'status');
        $instHasLastActive    = Schema::hasColumn('instructors', 'last_active_at');
        $instHasImage         = Schema::hasColumn('instructors', 'image');

        $adminHasStatus       = Schema::hasColumn('admins',      'status');
        $adminHasLastActive   = Schema::hasColumn('admins',      'last_active_at');
        $adminHasImage        = Schema::hasColumn('admins',      'image');

        $nameEnExpr = $usersHasNameEn
            ? 'COALESCE(NULLIF(name_en, ""), name) AS name_en'
            : 'name AS name_en';
        $nameArExpr = $usersHasNameAr
            ? 'NULLIF(name_ar, "") AS name_ar'
            : 'NULL AS name_ar';

        $usersSub = DB::table('users')
            ->selectRaw('"user" AS source')
            ->selectRaw('id')
            ->selectRaw($nameEnExpr)
            ->selectRaw($nameArExpr)
            ->selectRaw('email AS email')
            ->selectRaw('phone AS phone')
            ->selectRaw('machine_code AS machine_code')
            ->selectRaw('department_name AS department_name')
            ->selectRaw($usersHasImage ? 'image AS image' : 'NULL AS image')
            ->selectRaw('"learner" AS role_key')
            ->selectRaw('"Learner" AS role_label')
            ->selectRaw($usersHasStatus ? 'COALESCE(status, "active") AS status' : '"active" AS status')
            ->selectRaw($usersHasLastActive ? 'last_active_at AS last_active_at' : 'NULL AS last_active_at')
            ->selectRaw('created_at AS created_at');

        $instructorsSub = DB::table('instructors')
            ->selectRaw('"instructor" AS source')
            ->selectRaw('id')
            // Stored as JSON {"en":"…","ar":"…"} via Spatie HasTranslations.
            ->selectRaw('COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")), "null"), name) AS name_en')
            ->selectRaw('NULLIF(JSON_UNQUOTE(JSON_EXTRACT(name, "$.ar")), "null") AS name_ar')
            ->selectRaw('email AS email')
            ->selectRaw('NULL AS phone')
            ->selectRaw('NULL AS machine_code')
            ->selectRaw('NULL AS department_name')
            ->selectRaw($instHasImage ? 'image AS image' : 'NULL AS image')
            ->selectRaw('"instructor" AS role_key')
            ->selectRaw('"Instructor" AS role_label')
            ->selectRaw($instHasStatus ? 'COALESCE(status, "active") AS status' : '"active" AS status')
            ->selectRaw($instHasLastActive ? 'last_active_at AS last_active_at' : 'NULL AS last_active_at')
            ->selectRaw('created_at AS created_at');

        $adminsSub = DB::table('admins')
            ->selectRaw('"admin" AS source')
            ->selectRaw('id')
            ->selectRaw('name AS name_en')
            ->selectRaw('NULL AS name_ar')
            ->selectRaw('email AS email')
            ->selectRaw('NULL AS phone')
            ->selectRaw('NULL AS machine_code')
            ->selectRaw('NULL AS department_name')
            ->selectRaw($adminHasImage ? 'image AS image' : 'NULL AS image')
            ->selectRaw('"admin" AS role_key')
            ->selectRaw('"Admin" AS role_label')
            ->selectRaw($adminHasStatus ? 'COALESCE(status, "active") AS status' : '"active" AS status')
            ->selectRaw($adminHasLastActive ? 'last_active_at AS last_active_at' : 'NULL AS last_active_at')
            ->selectRaw('created_at AS created_at');

        return $usersSub->unionAll($instructorsSub)->unionAll($adminsSub);
    }

    /**
     * Compute compliance % for learner rows in a batch.  Instructors and
     * admins return null (rendered as "—" by the UI).
     *
     * @template T of \Illuminate\Support\Collection
     * @param   T  $rows
     * @return  T
     */
    private function attachCompliance(\Illuminate\Support\Collection $rows): \Illuminate\Support\Collection
    {
        $learnerIds = $rows
            ->filter(fn ($r) => ($r->source ?? null) === 'user')
            ->pluck('id')
            ->all();

        if (empty($learnerIds)) {
            return $rows->map(function ($r) {
                $r->compliance_pct         = null;
                $r->enrolled_courses_count = 0;
                return $r;
            });
        }

        $enrolledByUser = DB::table('users_courses')
            ->whereIn('user_id', $learnerIds)
            ->select('user_id', DB::raw('COUNT(*) AS c'))
            ->groupBy('user_id')
            ->pluck('c', 'user_id');

        $passedByUser = DB::table('user_exams')
            ->whereIn('user_id', $learnerIds)
            ->whereRaw('LOWER(COALESCE(status, "")) IN (?, ?)', ['passed', 'completed'])
            ->select('user_id', DB::raw('COUNT(DISTINCT course_id) AS c'))
            ->groupBy('user_id')
            ->pluck('c', 'user_id');

        return $rows->map(function ($r) use ($enrolledByUser, $passedByUser) {
            if (($r->source ?? null) !== 'user') {
                $r->compliance_pct         = null;
                $r->enrolled_courses_count = 0;
                return $r;
            }

            $enrolled = (int) ($enrolledByUser[$r->id] ?? 0);
            $passed   = (int) ($passedByUser[$r->id]   ?? 0);

            $r->enrolled_courses_count = $enrolled;
            $r->compliance_pct = $enrolled > 0
                ? (int) round(($passed / $enrolled) * 100)
                : null;

            return $r;
        });
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL — Misc helpers                                           |
     * ------------------------------------------------------------------ */

    private function assertSource(string $source): void
    {
        if (!in_array($source, self::SOURCES, true)) {
            throw new \InvalidArgumentException("Unknown source: {$source}");
        }
    }

    private function tableFor(string $source): string
    {
        return match ($source) {
            'user'       => 'users',
            'instructor' => 'instructors',
            'admin'      => 'admins',
        };
    }

    private function modelClassFor(string $source): string
    {
        return match ($source) {
            'user'       => User::class,
            'instructor' => Instructor::class,
            'admin'      => Admin::class,
        };
    }

    private function allocateSystemId(): int
    {
        do {
            $candidate = random_int(1, 9_999_999);
        } while (User::query()->where('system_id', $candidate)->exists());

        return $candidate;
    }

    /**
     * Decode the {"en":"…","ar":"…"} JSON name format used by the
     * `instructors` table (Spatie\Translatable\HasTranslations).
     */
    private function translateJsonName(string $raw, string $locale): string
    {
        $parts = $this->currentJsonName($raw);

        return $locale === 'ar'
            ? ($parts['ar'] ?: ($parts['en'] ?: $raw))
            : ($parts['en'] ?: ($parts['ar'] ?: $raw));
    }

    /**
     * @return array{en:string|null,ar:string|null}
     */
    private function currentJsonName(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            return ['en' => $raw, 'ar' => null];
        }

        return [
            'en' => isset($decoded['en']) ? (string) $decoded['en'] : null,
            'ar' => isset($decoded['ar']) ? (string) $decoded['ar'] : null,
        ];
    }
}
