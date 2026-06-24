<?php

namespace App\Http\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Shared helper for stamping a model's `last_active_at` timestamp so the
 * admin Users overview can show real activity for every role (learners,
 * instructors and admins alike) — not just whoever happens to use the
 * dashboard. Used by the auth middleware (ongoing activity) and by the
 * login services/controllers (so a fresh login always registers).
 */
trait TracksLastActive
{
    /**
     * Whether the model is due for a `last_active_at` refresh — i.e. it has
     * the column and the value is null or older than the throttle window.
     */
    protected function isLastActiveStale(?object $model, int $minutes = 5): bool
    {
        if (! $model
            || ! method_exists($model, 'getAttributes')
            || ! array_key_exists('last_active_at', $model->getAttributes())) {
            return false;
        }

        $last = $model->getAttribute('last_active_at');
        if ($last === null) {
            return true;
        }

        try {
            return Carbon::parse($last)->lte(now()->subMinutes($minutes));
        } catch (\Throwable) {
            return true;
        }
    }

    /**
     * Stamp `last_active_at = now()` on the model, throttled so we never
     * write more than once every few minutes. Silently no-ops when the
     * model's table doesn't have the column or the write fails — activity
     * tracking must never break authentication.
     */
    protected function stampLastActive(?object $model, bool $force = false): void
    {
        if (! $model
            || ! method_exists($model, 'getAttributes')
            || ! array_key_exists('last_active_at', $model->getAttributes())) {
            return;
        }

        if (! $force) {
            $last = $model->getAttribute('last_active_at');
            if ($last !== null) {
                try {
                    if (Carbon::parse($last)->gt(now()->subMinutes(5))) {
                        return; // Stamped recently — skip the write.
                    }
                } catch (\Throwable) {
                    // Unparseable value — fall through and overwrite it.
                }
            }
        }

        try {
            $model->forceFill(['last_active_at' => now()])->saveQuietly();
        } catch (\Throwable) {
            // Never let activity tracking break the request lifecycle.
        }
    }

    /**
     * Stamp `last_active_at = now()` on EVERY person table row that shares
     * the given email (users / instructors / admins).
     *
     * A single human is often represented across more than one table — e.g.
     * an instructor who signs in through the HR/user flow. The instructors
     * table has no login of its own, so without this cross-table stamp an
     * instructor row would never reflect activity. Called on login where the
     * email is known; cheap and infrequent.
     */
    protected function stampLastActiveByEmail(?string $email): void
    {
        $email = trim((string) $email);
        if ($email === '') {
            return;
        }

        foreach (['users', 'instructors', 'admins'] as $table) {
            try {
                if (Schema::hasColumn($table, 'last_active_at')) {
                    DB::table($table)
                        ->where('email', $email)
                        ->update(['last_active_at' => now()]);
                }
            } catch (\Throwable) {
                // Activity tracking must never break authentication.
            }
        }
    }
}
