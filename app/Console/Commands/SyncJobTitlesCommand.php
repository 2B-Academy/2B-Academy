<?php

namespace App\Console\Commands;

use App\Services\JobTitleSyncService;
use Illuminate\Console\Command;

/**
 * php artisan job-titles:sync [--offline] [--prune]
 *
 * Refreshes the local Job Titles catalogue (`job_titles`) from the
 * upstream HR system.
 *
 *   • Default mode pulls the authoritative catalogue from the HR
 *     `/Job` endpoint and cross-references it with
 *     `/Employee/GetCurrentEmployees` so only jobs with at least
 *     one assigned employee end up in the catalogue.
 *
 *   • `--offline` skips the HR round-trip and projects the catalogue
 *     from whatever is already in the local `users` table — useful
 *     in CI, on disconnected dev machines, or as a recovery path
 *     if the HR API is temporarily down.
 *
 *   • `--prune` deletes catalogue rows that no longer match the
 *     resolved source. Off by default so curated qualification
 *     mappings survive an accidental drift; cascade FK cleans up
 *     the pivot table whenever it is enabled.
 *
 * Idempotent + race-safe — re-running is always safe.
 */
class SyncJobTitlesCommand extends Command
{
    protected $signature = 'job-titles:sync
        {--offline : Skip the HR API and derive the catalogue from the local users table}
        {--prune   : Delete catalogue rows that no longer match the resolved source}';

    protected $description = 'Refresh the Job Titles catalogue from the HR /Job endpoint (filtered by employees > 0)';

    public function handle(JobTitleSyncService $sync): int
    {
        $prune    = (bool) $this->option('prune');
        $offline  = (bool) $this->option('offline');

        $this->info($offline
            ? 'Syncing job titles from local users table (offline mode)…'
            : 'Syncing job titles from HR /Job endpoint (filtered by employees > 0)…');

        $report = $offline
            ? $sync->syncFromUsers($prune)
            : $sync->syncFromHr($prune);

        $this->table(
            ['HR jobs', 'Eligible (emp > 0)', 'Created', 'Unchanged', 'Orphaned', 'Pruned'],
            [[
                $report['source_rows'],
                $report['eligible'],
                $report['created'],
                $report['unchanged'],
                $report['orphaned'],
                $report['pruned'],
            ]],
        );

        if (! $offline && $report['source_rows'] === 0) {
            $this->error('HR API returned zero jobs — credentials or connectivity issue. Re-run with --offline to fall back to the local users table.');

            return self::FAILURE;
        }

        if ($report['orphaned'] > 0 && ! $prune) {
            $this->warn(sprintf(
                '%d catalogue row(s) no longer match the resolved HR jobs — re-run with --prune to delete.',
                $report['orphaned'],
            ));
        }

        if ($report['pruned'] > 0) {
            $this->info(sprintf(
                '%d orphan row(s) and any associated qualification mappings were deleted (cascade FK).',
                $report['pruned'],
            ));
        }

        return self::SUCCESS;
    }
}
