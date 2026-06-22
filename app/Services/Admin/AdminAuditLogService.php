<?php

namespace App\Services\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorImpl;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Service backing the 2026 admin Audit Log redesign.
 *
 * The legacy `audit_logs` table stores its actor as `user_type`
 * ('admin' | 'user' | 'system') which predates the new persona model
 * (Admin / Instructor / Learner). To render the Figma "Admin" /
 * "Instructor" badge precisely without modifying any legacy writers,
 * this service:
 *
 *   1. Reads the additive `actor_role` column when present.
 *   2. Falls back to `user_type` and a one-shot lookup against the
 *      `instructors` table for legacy rows (so historical entries still
 *      render with the correct badge).
 *
 * It is strictly additive — no existing services, models, or controllers
 * are touched. The legacy public endpoint
 * (App\Http\Controllers\apis\AuditLogController @ /api/v1/audit-log)
 * continues to work as before.
 */
class AdminAuditLogService
{
    /** @var array<int,string> Roles the UI filters by (drives the pills). */
    public const ROLES = ['admin', 'instructor'];

    /**
     * Default page size — chosen to match the Figma list which displays
     * ~10 rows above the fold.
     */
    public const PER_PAGE_DEFAULT = 25;

    /* ------------------------------------------------------------------ *
     |  PAGINATED LIST                                                    |
     * ------------------------------------------------------------------ */

    /**
     * Paginate audit log rows for the admin overview.
     *
     * @param  array<int,int>|null  $instructorIds  Filter to actions
     *                                              performed by specific
     *                                              instructors (drives
     *                                              the "Instructors"
     *                                              sub-modal).
     */
    public function paginate(
        ?string $role          = null,
        ?string $search        = null,
        ?string $dateFrom      = null,
        ?string $dateTo        = null,
        ?array  $instructorIds = null,
        int     $perPage       = self::PER_PAGE_DEFAULT,
    ): LengthAwarePaginator {
        $instructorIdSet = $this->resolveInstructorIds();

        $query = $this->baseQuery();

        $this->applyRoleFilter($query, $role, $instructorIdSet, $instructorIds);
        $this->applyDateRange($query, $dateFrom, $dateTo);
        $this->applySearch($query, $search);

        $query->orderByDesc('audit_logs.created_at')
              ->orderByDesc('audit_logs.id');

        $page  = (int) (Paginator::resolveCurrentPage() ?: 1);
        $total = (clone $query)->count();
        $rows  = $query->forPage($page, $perPage)->get();

        $rows = $this->decorate($rows, $instructorIdSet);

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
     |  FILTER OPTIONS                                                    |
     * ------------------------------------------------------------------ */

    /**
     * Return the lookups powering the filter modal:
     *
     *   roles        →  the role pills with live counts
     *   instructors  →  every instructor row (id + localised name)
     *   admins       →  every admin row (used for the "Admin" sub-modal
     *                   should the design later add an admin sub-filter)
     *   actions      →  distinct action verbs seen so far (datalist hint
     *                   for the search box)
     *
     * @return array{
     *   roles:       array<int,array{key:string,label:string,count:int}>,
     *   instructors: array<int,array{id:int,name:string}>,
     *   admins:      array<int,array{id:int,name:string}>,
     *   actions:     array<int,string>
     * }
     */
    public function filterOptions(): array
    {
        $locale          = app()->getLocale();
        $instructorIdSet = $this->resolveInstructorIds();

        $instructors = DB::table('instructors')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn ($row) => [
                'id'   => (int) $row->id,
                'name' => $this->translateJsonName((string) $row->name, $locale),
            ])
            ->all();

        $admins = DB::table('admins')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn ($row) => [
                'id'   => (int) $row->id,
                'name' => (string) $row->name,
            ])
            ->all();

        // Live counts per role (cheap — one query each, bounded by the
        // size of the audit log).
        $counts = [
            'admin'      => $this->countRowsForRole('admin', $instructorIdSet),
            'instructor' => $this->countRowsForRole('instructor', $instructorIdSet),
        ];

        $roles = [];
        foreach (self::ROLES as $role) {
            $roles[] = [
                'key'   => $role,
                'label' => $this->labelForRole($role, $locale),
                'count' => $counts[$role] ?? 0,
            ];
        }

        $actions = DB::table('audit_logs')
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->distinct()
            ->orderBy('action')
            ->limit(100)
            ->pluck('action')
            ->all();

        return [
            'roles'       => $roles,
            'instructors' => $instructors,
            'admins'      => $admins,
            'actions'     => $actions,
        ];
    }

    /* ------------------------------------------------------------------ *
     |  EXPORT                                                            |
     * ------------------------------------------------------------------ */

    /**
     * Build a flat, audit-grade dataset for export. Same filters as
     * paginate() but materialised in full (no pagination, no row cap)
     * so the admin gets a complete trail.
     *
     * @return array{0:list<string>,1:list<list<string|int|null>>}
     */
    public function dataset(
        ?string $role          = null,
        ?string $search        = null,
        ?string $dateFrom      = null,
        ?string $dateTo        = null,
        ?array  $instructorIds = null,
    ): array {
        $instructorIdSet = $this->resolveInstructorIds();

        $query = $this->baseQuery();
        $this->applyRoleFilter($query, $role, $instructorIdSet, $instructorIds);
        $this->applyDateRange($query, $dateFrom, $dateTo);
        $this->applySearch($query, $search);

        $query->orderByDesc('audit_logs.created_at')
              ->orderByDesc('audit_logs.id');

        $rows = $this->decorate($query->get(), $instructorIdSet);

        $headings = ['Timestamp', 'Actor', 'Role', 'Action', 'Entity', 'IP Address'];

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                $row->created_at ? (string) $row->created_at : '',
                (string) ($row->user_name ?? ''),
                ucfirst((string) ($row->effective_role ?? '')),
                $this->composeAction($row),
                (string) ($row->description ?? ''),
                (string) ($row->ip_address ?? ''),
            ];
        }

        return [$headings, $data];
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL                                                          |
     * ------------------------------------------------------------------ */

    /**
     * Base query selecting only the columns we need from `audit_logs`,
     * defensively guarding the `actor_role` column so the service stays
     * functional on databases where the additive migration has not yet
     * been run.
     */
    private function baseQuery(): QueryBuilder
    {
        $hasActorRole = Schema::hasColumn('audit_logs', 'actor_role');

        $columns = [
            'audit_logs.id',
            'audit_logs.user_type',
            'audit_logs.user_id',
            'audit_logs.user_name',
            'audit_logs.action',
            'audit_logs.model_type',
            'audit_logs.model_id',
            'audit_logs.description',
            'audit_logs.ip_address',
            'audit_logs.created_at',
        ];

        if ($hasActorRole) {
            $columns[] = 'audit_logs.actor_role';
        }

        return DB::table('audit_logs')->select($columns);
    }

    /**
     * @param  QueryBuilder           $query
     * @param  array<int,int>         $instructorIdSet  All instructor ids.
     * @param  array<int,int>|null    $instructorIds    Sub-filter ids.
     */
    private function applyRoleFilter(
        QueryBuilder $query,
        ?string      $role,
        array        $instructorIdSet,
        ?array       $instructorIds,
    ): void {
        $hasActorRole = Schema::hasColumn('audit_logs', 'actor_role');

        if ($role === 'admin') {
            $query->where(function ($w) use ($hasActorRole) {
                if ($hasActorRole) {
                    $w->where('audit_logs.actor_role', 'admin')
                      ->orWhere(function ($inner) {
                          $inner->whereNull('audit_logs.actor_role')
                                ->where('audit_logs.user_type', 'admin');
                      });
                } else {
                    $w->where('audit_logs.user_type', 'admin');
                }
            });
            return;
        }

        if ($role === 'instructor') {
            $ids = !empty($instructorIds)
                ? array_values(array_intersect($instructorIds, $instructorIdSet))
                : $instructorIdSet;

            $query->where(function ($w) use ($ids, $hasActorRole) {
                if ($hasActorRole) {
                    $w->where('audit_logs.actor_role', 'instructor');
                }

                if (!empty($ids)) {
                    $w->orWhere(function ($inner) use ($ids) {
                        $inner->where('audit_logs.user_type', '!=', 'admin')
                              ->whereIn('audit_logs.user_id', $ids);
                    });
                } elseif (!$hasActorRole) {
                    // No actor_role column and no instructor ids — guarantee
                    // zero rows rather than mis-classify learners as
                    // instructors.
                    $w->whereRaw('1=0');
                }
            });
            return;
        }
    }

    private function applyDateRange(QueryBuilder $query, ?string $dateFrom, ?string $dateTo): void
    {
        if ($dateFrom) {
            try {
                $query->where('audit_logs.created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            } catch (\Throwable) {
                // ignore malformed dates rather than 500ing the list
            }
        }

        if ($dateTo) {
            try {
                $query->where('audit_logs.created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            } catch (\Throwable) {
                // ignore
            }
        }
    }

    private function applySearch(QueryBuilder $query, ?string $search): void
    {
        if (!$search) {
            return;
        }

        $needle = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';

        $query->where(function ($w) use ($needle) {
            $w->where('audit_logs.user_name',   'LIKE', $needle)
              ->orWhere('audit_logs.action',      'LIKE', $needle)
              ->orWhere('audit_logs.model_type',  'LIKE', $needle)
              ->orWhere('audit_logs.description', 'LIKE', $needle)
              ->orWhere('audit_logs.ip_address',  'LIKE', $needle);
        });
    }

    /**
     * Hydrate each row with derived fields (effective_role,
     * entity_token, action_token) so the resource layer can stay
     * trivial.
     *
     * @param  Collection<int,\stdClass>  $rows
     * @param  array<int,int>             $instructorIdSet
     * @return Collection<int,\stdClass>
     */
    private function decorate(Collection $rows, array $instructorIdSet): Collection
    {
        return $rows->map(function (\stdClass $row) use ($instructorIdSet) {
            $row->effective_role = $this->effectiveRoleFor($row, $instructorIdSet);
            [$entity, $action]   = $this->splitActionParts($row);
            $row->entity_token   = $entity;
            $row->action_token   = $action;
            return $row;
        });
    }

    /**
     * Resolve the badge to render for a single row, preferring the new
     * `actor_role` column and falling back to `user_type` + an instructor
     * id lookup for legacy rows.
     *
     * @param  array<int,int>  $instructorIdSet
     */
    private function effectiveRoleFor(\stdClass $row, array $instructorIdSet): string
    {
        $stored = isset($row->actor_role) && $row->actor_role
            ? strtolower((string) $row->actor_role)
            : null;

        if ($stored && in_array($stored, ['admin', 'instructor', 'learner', 'system'], true)) {
            return $stored;
        }

        $userType = strtolower((string) ($row->user_type ?? ''));
        if ($userType === 'admin') {
            return 'admin';
        }

        $userId = (int) ($row->user_id ?? 0);
        if ($userId > 0 && in_array($userId, $instructorIdSet, true)) {
            return 'instructor';
        }

        return $userType === 'system' ? 'system' : 'learner';
    }

    /**
     * Split the stored `model_type` + `action` pair into the two-token
     * "entity → verb" display shown by Figma.  Always returns lowercase
     * tokens so the UI's chip styling stays consistent.
     *
     * @return array{0:string,1:string}
     */
    private function splitActionParts(\stdClass $row): array
    {
        $rawAction    = (string) ($row->action ?? '');
        $rawModelType = (string) ($row->model_type ?? '');

        $entity = '';
        if ($rawModelType !== '') {
            $entity = Str::of(class_basename($rawModelType))->snake()->lower()->toString();
        }

        $action = $rawAction;

        // If `action` is itself a compound token like "course_published",
        // promote the prefix to `entity` when we don't already have one
        // and keep the suffix as the verb.
        if (Str::contains($action, '_')) {
            [$prefix, $suffix] = explode('_', $action, 2);
            if ($entity === '') {
                $entity = strtolower($prefix);
            }
            $action = $suffix;
        }

        return [$entity, strtolower($action)];
    }

    /**
     * Count rows matching a given role under the current data, used by
     * the filter modal pill badges. Cheap because the joins are minimal.
     *
     * @param  array<int,int>  $instructorIdSet
     */
    private function countRowsForRole(string $role, array $instructorIdSet): int
    {
        $query = DB::table('audit_logs');
        $this->applyRoleFilter($query, $role, $instructorIdSet, null);
        return (int) $query->count();
    }

    /**
     * @return array<int,int>
     */
    private function resolveInstructorIds(): array
    {
        if (!Schema::hasTable('instructors')) {
            return [];
        }

        return DB::table('instructors')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function labelForRole(string $role, string $locale): string
    {
        $labels = [
            'en' => ['admin' => 'Admin',  'instructor' => 'Instructor'],
            'ar' => ['admin' => 'مدير',   'instructor' => 'مُدرّب'],
        ];

        return $labels[$locale][$role] ?? $labels['en'][$role] ?? ucfirst($role);
    }

    /**
     * Decode the Spatie HasTranslations JSON name format
     * ({"en":"…","ar":"…"}) used by the `instructors` table.
     */
    private function translateJsonName(string $raw, string $locale): string
    {
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return $raw;
        }

        $en = isset($decoded['en']) ? (string) $decoded['en'] : '';
        $ar = isset($decoded['ar']) ? (string) $decoded['ar'] : '';

        return $locale === 'ar'
            ? ($ar !== '' ? $ar : $en)
            : ($en !== '' ? $en : $ar);
    }

    /**
     * Pretty-print the Figma "entity → verb" combo for the export CSV.
     */
    private function composeAction(\stdClass $row): string
    {
        $entity = (string) ($row->entity_token ?? '');
        $action = (string) ($row->action_token ?? '');

        if ($entity !== '' && $action !== '') {
            return "{$entity} -> {$action}";
        }

        return $action !== '' ? $action : $entity;
    }
}
