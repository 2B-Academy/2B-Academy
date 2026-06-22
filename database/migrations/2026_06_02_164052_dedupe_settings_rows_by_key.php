<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Collapse duplicate `settings` rows that share the same `key` down to a
 * single canonical row — the lowest id, which is exactly the row that
 * SettingRepository::updateByKey() upserts (firstOrNew by key) and the admin
 * dashboard Settings screen edits.
 *
 * Background: `course_attendance_enabled` and `passcode_reset_seconds` were
 * seeded BOTH by PlatformConfigSeeder (module `platform`) and the old
 * MobileSettingSeeder (module `mobile_attendance`). The settings table treats
 * `key` as the unique lookup everywhere (getMap / updateByKey), so the extra
 * row was never a feature — it just made the admin Settings page render a
 * stale value (last-row-wins) and the passcode engine read the wrong row.
 *
 * Keeping the lowest id preserves whatever value the user last saved from the
 * dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        $keepIds = DB::table('settings')
            ->selectRaw('MIN(id) as id')
            ->groupBy('key')
            ->pluck('id')
            ->all();

        if ($keepIds !== []) {
            DB::table('settings')->whereNotIn('id', $keepIds)->delete();
        }
    }

    public function down(): void
    {
        // Irreversible: removed duplicate rows cannot be meaningfully restored.
    }
};
