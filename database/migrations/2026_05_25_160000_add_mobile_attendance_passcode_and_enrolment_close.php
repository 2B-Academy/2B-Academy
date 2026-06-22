<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mobile Attendance — passcode flow on course_sessions, and
 * cohort enrolment deadline on course_sections.
 *
 * Why these columns exist
 *   The NAS mobile UX (S-06 "Mark Present & Passcode") requires the
 *   instructor to issue a short-lived numeric code, and the employee
 *   to enter it inside an open attendance window. None of that state
 *   exists on `course_sessions` today — sessions only carry date /
 *   time / location. We need:
 *
 *     - passcode                : the short numeric token the
 *                                  instructor reads out loud
 *     - passcode_issued_at      : when the code was created (for audit
 *                                  and "code expires at HH:MM" UI)
 *     - passcode_expires_at     : the hard server-side cutoff used by
 *                                  MobileAttendanceService::markPresent
 *     - attendance_window_minutes
 *                                : default validity used when the
 *                                  instructor issues a code without
 *                                  specifying an explicit expiry
 *
 *   The mobile UX (S-03 "Course Detail") also surfaces a cohort
 *   enrolment deadline ("Enrolment closes: 5 June 2026") that today
 *   has to be inferred from start_date. Adding `enrolment_closes_at`
 *   makes the deadline first-class and lets us hide cohorts from the
 *   Academy list once that date has passed without conflating it with
 *   "cohort is currently running".
 *
 * Why nullable / additive
 *   Strict additive change. Existing CourseSessionController +
 *   CohortAttendanceService keep working — they never read these
 *   columns. The new MobileAttendanceService is the only code path
 *   that depends on them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_sessions', function (Blueprint $table): void {
            $table->string('passcode', 10)->nullable()->after('location');
            $table->timestamp('passcode_issued_at')->nullable()->after('passcode');
            $table->timestamp('passcode_expires_at')->nullable()->after('passcode_issued_at');
            $table->unsignedInteger('attendance_window_minutes')->nullable()->after('passcode_expires_at');

            $table->index('passcode_expires_at', 'course_sessions_passcode_expiry_idx');
            $table->index(['section_id', 'session_date'], 'course_sessions_cohort_date_idx');
        });

        Schema::table('course_sections', function (Blueprint $table): void {
            $table->date('enrolment_closes_at')->nullable()->after('end_date');
            $table->index('enrolment_closes_at', 'course_sections_enrolment_close_idx');
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table): void {
            $table->dropIndex('course_sections_enrolment_close_idx');
            $table->dropColumn('enrolment_closes_at');
        });

        Schema::table('course_sessions', function (Blueprint $table): void {
            $table->dropIndex('course_sessions_cohort_date_idx');
            $table->dropIndex('course_sessions_passcode_expiry_idx');
            $table->dropColumn([
                'passcode',
                'passcode_issued_at',
                'passcode_expires_at',
                'attendance_window_minutes',
            ]);
        });
    }
};
