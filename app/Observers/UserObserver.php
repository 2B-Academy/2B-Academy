<?php

namespace App\Observers;

use App\Models\User;
use App\Services\JobTitleSyncService;

/**
 * Keeps the Job Titles catalogue self-healing.
 *
 * Whenever a user is created — or has their department reassigned —
 * we make sure the corresponding `job_titles.name` row exists. That
 * way the admin never needs to remember to "refresh the catalogue":
 * the screen always reflects the live HR state, and any new card
 * shows up the next time the page is opened.
 *
 * The hook is intentionally cheap (one indexed lookup, at most one
 * insert) and exception-safe — see
 * {@see JobTitleSyncService::ensureExists()} — so it never blocks an
 * employee save even under contention.
 */
class UserObserver
{
    public function __construct(private readonly JobTitleSyncService $sync) {}

    public function created(User $user): void
    {
        $this->sync->ensureExists($user->job_title ?? null);
    }

    public function updated(User $user): void
    {
        if (! $user->wasChanged('job_title')) {
            return;
        }

        $this->sync->ensureExists($user->job_title ?? null);
    }
}
