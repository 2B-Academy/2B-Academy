<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-cohort "Avg. Session Time" (Figma 332:9988 / 332:10708).
 *
 * Stored in HOURS as a small decimal (e.g. 1.5 = ninety minutes) so the
 * admin can think in whole/half hours. When an instructor starts a live
 * session for the cohort the attendance window length is driven by this
 * value instead of the global `attendance_window_minutes` setting, which
 * lets each cohort run sessions of a different length.
 *
 * Nullable + additive — cohorts without a value fall back to the global
 * default, so existing rows and older API callers keep working.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->decimal('avg_session_time', 5, 2)->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn('avg_session_time');
        });
    }
};
