<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Re-establish the users ↔ job_titles link.
 *
 * The 2026 admin Users redesign dropped the denormalized
 * `users.job_title` string column (because admins no longer pick a
 * free-text job role on the user form). That same migration broke
 * the {@see \App\Models\JobTitle::users()} relationship which used
 * to join on the natural string key, and as a result the Job Titles
 * admin screen now always shows `employees_count = 0`.
 *
 * Replace the string-join with a proper FK so the catalogue can grow
 * independently of how users are entered, and so HR sync can stitch
 * the link back together programmatically (see
 * `GetAllEmployeesFromHRSystemJob` / `GetAllEmployeesFromHRSystemCommand`).
 *
 *   - Nullable so existing rows aren't broken.
 *   - ON DELETE SET NULL so deleting a job title doesn't cascade out
 *     to user rows; the bridge just gets cleared.
 *   - Indexed for the `withCount('users as employees_count')` path
 *     used by the Job Titles paginate query.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'job_title_id')) {
                $table->foreignId('job_title_id')
                    ->nullable()
                    ->after('department_name')
                    ->constrained('job_titles')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'job_title_id')) {
                $table->dropForeign(['job_title_id']);
                $table->dropColumn('job_title_id');
            }
        });
    }
};
