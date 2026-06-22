<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cohort metadata on course_sections.
 *
 * The Cohort tab on the course detail screen (Figma 332:8125 → 332:9988)
 * shows Cohort name + Start/End date + Enrolled/Capacity + Status. Today
 * `course_sections` only stores `name`, and the frontend fakes the rest
 * by mapping `course_sessions` rows into cohorts via `mapSessionToCohort`.
 *
 * That mapping is wrong semantically — a cohort is the *group of learners*
 * (one row in `course_sections`), and a session is one *meeting* of that
 * cohort (one row in `course_sessions`). Promoting the cohort to a real,
 * first-class concept on the backend means:
 *   - the cohort table can show real dates, capacity and status;
 *   - the Cohort Attendance drawer can stay tagged by `course_sections.id`;
 *   - users_courses.group_id keeps its existing meaning.
 *
 * All new columns are nullable so this is a strict additive change — the
 * existing CourseSectionController/Resource keep working for callers that
 * don't yet send the new fields.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('name');
            $table->date('end_date')->nullable()->after('start_date');
            $table->unsignedInteger('capacity')->nullable()->after('end_date');
            // 'scheduled' | 'active' | 'completed' | 'inactive' — kept as
            // a string so the catalogue can grow without another migration.
            $table->string('status', 32)->nullable()->default('scheduled')->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'capacity', 'status']);
        });
    }
};
