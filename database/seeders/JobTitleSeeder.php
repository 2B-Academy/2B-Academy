<?php

namespace Database\Seeders;

use App\Services\JobTitleSyncService;
use Illuminate\Container\Container;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Projects the Job Titles catalogue from the canonical HR roster
 * (the `users` table imported by {@see UserSeeder}).
 *
 * Historically this seeder shipped a hardcoded list of test titles
 * ("Site Engineer", "Project Manager", …) that had **zero overlap**
 * with the real `users.department_name` values from the production
 * dump — so every card on the Job Titles screen rendered
 * "0 Employee". We replaced that with a thin delegation to
 * {@see JobTitleSyncService::syncFromUsers()}, which derives the
 * catalogue from HR truth and is also reused by the
 * `php artisan job-titles:sync` command for ongoing reconciliation.
 *
 * DatabaseSeeder runs JobTitleSeeder **after** UserSeeder, so the
 * source rows are already in place when this fires. On a fresh
 * database without a users fixture the sync simply yields zero rows
 * and exits — no errors, no test data left behind.
 */
class JobTitleSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('job_titles')) {
            $this->command?->warn('JobTitleSeeder: required tables missing; skipping.');

            return;
        }

        /**
         * Seeders run in CI and on fresh local DBs that have no
         * outbound network. Use the offline path here — the
         * `job-titles:sync` (HR) and `sync:employees` commands
         * handle the live-HR refresh in their own contexts.
         */
        /** @var JobTitleSyncService $sync */
        $sync = Container::getInstance()->make(JobTitleSyncService::class);

        $report = $sync->syncFromUsers();

        $this->command?->info(sprintf(
            'JobTitleSeeder: synced %d catalogue rows (created: %d, unchanged: %d, orphaned: %d) from %d local users.',
            $report['created'] + $report['unchanged'],
            $report['created'],
            $report['unchanged'],
            $report['orphaned'],
            $report['source_rows'],
        ));
    }
}
