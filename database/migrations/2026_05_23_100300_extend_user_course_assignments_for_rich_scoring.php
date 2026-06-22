<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds aggregate scoring + submission timing to `user_course_assignments` so
 * the new rich-question flow can compute / display learner results without
 * touching legacy file-submission rows.
 *
 *  • total_score   → sum of per-question awarded_score for this submission
 *  • max_score     → snapshot of assignment.total_score at submission time
 *  • submitted_at  → moment the learner finalised their answers
 *  • reviewed_at   → moment the last manual grading happened
 *  • reviewed_by   → grader user id (FK to users)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_course_assignments', function (Blueprint $table) {
            $table->unsignedInteger('total_score')->nullable()->after('score');
            $table->unsignedInteger('max_score')->nullable()->after('total_score');
            $table->timestamp('submitted_at')->nullable()->after('max_score');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');

            $table->index(['course_assignment_id', 'submitted_at'], 'uca_assignment_submitted_idx');
        });
    }

    public function down(): void
    {
        Schema::table('user_course_assignments', function (Blueprint $table) {
            $table->dropIndex('uca_assignment_submitted_idx');
            $table->dropColumn([
                'total_score',
                'max_score',
                'submitted_at',
                'reviewed_at',
                'reviewed_by',
            ]);
        });
    }
};
