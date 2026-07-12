<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Learner-facing intent storage for "Notify me for next cohort"
 * (NAS-LMS-Website-Business-Flows.md GAP 4).
 *
 * A learner can tap "Notify me" on a course whose next cohort isn't
 * currently joinable (CourseCtaState::GetNotified). This table only
 * records that ONE row of intent per learner+course — the admin-side
 * trigger that actually fires a notification when a new cohort opens
 * enrolment is a separate, larger piece of work (wiring into the cohort
 * creation flow) and is intentionally NOT part of this change.
 *
 * One row per learner+course: re-tapping "Notify me" is an idempotent
 * upsert (see CourseNotifyInterest / AcademyService::notifyMeForNextCohort),
 * enforced here with a unique index as the DB-level safety net.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_notify_interests')) {
            return;
        }

        Schema::create('course_notify_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_notify_interests');
    }
};
