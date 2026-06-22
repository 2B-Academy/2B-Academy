<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 2026 Admin "Users" form redesign — Figma 529:38878 drops the legacy
 * "Job Role" field. The free-text `job_title` column was added in
 * 2026_05_19_140000_add_job_title_to_users_table.php (and again to
 * `instructors` / `admins` in the May 2026 unification migrations) — it
 * is no longer surfaced by the UI nor referenced by any production
 * report, so we remove it cleanly from all three person tables.
 *
 * Down restores the column as nullable (data is not preserved).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'instructors', 'admins'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'job_title')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('job_title');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['users', 'instructors', 'admins'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (! Schema::hasColumn($table, 'job_title')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('job_title')->nullable();
                });
            }
        }
    }
};
