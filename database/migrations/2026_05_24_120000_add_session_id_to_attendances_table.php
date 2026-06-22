<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cohort Attendance — pin each attendance row to a specific course_session.
 *
 * Why this column exists
 *   The legacy attendance flow only stored (user_id, course_id, section_id,
 *   created_at) and inferred "which session was this for?" by matching the
 *   row's date against course_sessions.session_date. That heuristic breaks
 *   when two sessions fall on the same day (morning + afternoon batches)
 *   and makes the Cohort Attendance drawer's per-session rollup ambiguous.
 *
 *   By recording session_id explicitly we get an unambiguous, indexable
 *   link from attendance → session. Existing rows stay valid (NULL means
 *   "fall back to the date-match heuristic in CohortAttendanceService").
 *
 * Why nullable
 *   Strict additive change — no backfill required, no breakage for older
 *   AttendanceService::record() callers that don't yet pass session_id,
 *   and no foreign-key cascade surprises if a session is deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('section_id');
            $table->index(['course_id', 'section_id', 'session_id'], 'attendances_cohort_session_idx');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_cohort_session_idx');
            $table->dropColumn('session_id');
        });
    }
};
