<?php

namespace App\Services;

use App\Models\JobTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Keeps the `job_titles` catalogue in lock-step with the upstream
 * HR system of record.
 *
 * Sourcing strategy
 * -----------------
 *  - **Primary** ({@see self::syncFromHr()}): pulls the authoritative
 *    job catalogue from `HRSystemService::getAllJobs()` and cross-
 *    references it with `HRSystemService::getAllEmployees()` to keep
 *    only jobs that currently have at least one assigned employee.
 *    This is the production path — invoked by
 *    `php artisan job-titles:sync` and by the existing
 *    `sync:employees` command after every HR pull.
 *
 *  - **Offline fallback** ({@see self::syncFromUsers()}): when the HR
 *    API is unreachable (CI, local dev with no outbound network) the
 *    catalogue can still be projected from whatever is already in the
 *    local `users` table. The artisan command exposes this behind
 *    an `--offline` flag.
 *
 * Both paths upsert by `job_titles.name` so admin-curated qualification
 * mappings on the existing rows survive a sync. Orphans (catalogue rows
 * whose name no longer matches the HR source) are reported, but only
 * deleted when the caller explicitly asks — that decision lives at the
 * console boundary, never in the service.
 */
class JobTitleSyncService
{
    public function __construct(private readonly HRSystemService $hr) {}

    /**
     * Refresh the catalogue from the HR Jobs API.
     *
     * Pulls `/Job` and `/Employee/GetCurrentEmployees`, groups
     * employees by `jobName`, then upserts only those jobs whose
     * name has ≥ 1 employee assigned.
     *
     * Returns a structured report:
     *   ['source_rows' => 214, 'eligible' => 165, 'created' => 0,
     *    'unchanged' => 165, 'orphaned' => 0, 'pruned' => 0]
     *
     * Where `source_rows` is the total jobs HR sent back, `eligible`
     * is the subset retained after the `employees > 0` filter, and
     * the rest mirror the catalogue effect.
     *
     * @return array{source_rows:int, eligible:int, created:int, unchanged:int, orphaned:int, pruned:int}
     */
    public function syncFromHr(bool $pruneOrphans = false): array
    {
        $jobs      = $this->hr->getAllJobs();
        $employees = $this->hr->getAllEmployees();

        if ($jobs->isEmpty()) {
            Log::warning('JobTitleSyncService::syncFromHr — HR /Job returned no rows; skipping sync.');

            return $this->emptyReport();
        }

        $countsByJobName = $employees
            ->map(fn ($e) => is_object($e) ? trim((string) ($e->jobName ?? '')) : null)
            ->filter()
            ->countBy()
            ->all();

        $eligibleNames = $jobs
            ->map(fn ($j) => is_object($j) ? trim((string) ($j->name ?? '')) : null)
            ->filter()
            ->unique()
            ->values()
            ->filter(fn (string $name) => ($countsByJobName[$name] ?? 0) > 0)
            ->values()
            ->all();

        $report = $this->upsertCatalogue($eligibleNames, $pruneOrphans);

        return ['source_rows' => $jobs->count(), 'eligible' => count($eligibleNames)] + $report;
    }

    /**
     * Offline projection: derive the catalogue from the local `users`
     * table when HR is unreachable. The 2026 admin Users redesign
     * dropped `users.job_title`, so we now bucket strictly by
     * `department_name` for the offline fallback. The HR-driven path
     * ({@see syncFromHr()}) remains the canonical source of truth.
     *
     * @return array{source_rows:int, eligible:int, created:int, unchanged:int, orphaned:int, pruned:int}
     */
    public function syncFromUsers(bool $pruneOrphans = false): array
    {
        $names = $this->distinctLocalUserNames();

        if ($names === []) {
            return $this->emptyReport();
        }

        $report = $this->upsertCatalogue($names, $pruneOrphans);

        return ['source_rows' => count($names), 'eligible' => count($names)] + $report;
    }

    /**
     * Upsert a single job name. Used by {@see \App\Observers\UserObserver}
     * so an employee's HR job change instantly appears in the catalogue.
     */
    public function ensureExists(?string $name): bool
    {
        $name = $this->normalise($name);
        if ($name === null) {
            return false;
        }

        try {
            $created = JobTitle::query()->firstOrCreate(['name' => $name]);

            return $created->wasRecentlyCreated;
        } catch (\Throwable $e) {
            Log::warning('JobTitleSyncService::ensureExists swallowed: '.$e->getMessage(), [
                'name' => $name,
            ]);

            return false;
        }
    }

    /**
     * Shared upsert + orphan-prune routine used by both the HR-driven
     * and offline-fallback paths.
     *
     * @param  array<int, string>  $names
     * @return array{created:int, unchanged:int, orphaned:int, pruned:int}
     */
    private function upsertCatalogue(array $names, bool $pruneOrphans): array
    {
        if ($names === []) {
            return ['created' => 0, 'unchanged' => 0, 'orphaned' => 0, 'pruned' => 0];
        }

        $existing = JobTitle::query()
            ->whereIn('name', $names)
            ->pluck('name')
            ->all();

        $missing = array_values(array_diff($names, $existing));
        $now     = now();

        if ($missing !== []) {
            JobTitle::query()->insert(array_map(
                static fn (string $name): array => [
                    'name'       => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $missing,
            ));
        }

        $orphanQuery = JobTitle::query()->whereNotIn('name', $names);
        $orphaned    = $orphanQuery->count();
        $pruned      = 0;

        if ($pruneOrphans && $orphaned > 0) {
            $pruned = $orphanQuery->delete();
        }

        return [
            'created'   => count($missing),
            'unchanged' => count($existing),
            'orphaned'  => $orphaned,
            'pruned'    => $pruned,
        ];
    }

    /**
     * Distinct, trimmed department names from the local users table —
     * the offline fallback bucket since `users.job_title` was dropped
     * by the 2026 admin Users redesign.
     *
     * @return array<int, string>
     */
    private function distinctLocalUserNames(): array
    {
        return DB::table('users')
            ->selectRaw('DISTINCT TRIM(department_name) AS name')
            ->whereNotNull('department_name')
            ->whereRaw('TRIM(department_name) <> ""')
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    private function normalise(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        $trimmed = trim($name);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * @return array{source_rows:int, eligible:int, created:int, unchanged:int, orphaned:int, pruned:int}
     */
    private function emptyReport(): array
    {
        return [
            'source_rows' => 0,
            'eligible'    => 0,
            'created'     => 0,
            'unchanged'   => 0,
            'orphaned'    => 0,
            'pruned'      => 0,
        ];
    }
}
