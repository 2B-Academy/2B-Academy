<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive extension to `course_assignments` for the new rich-assignment system.
 *
 *  • title_ar          → Arabic counterpart of `title`
 *  • instructions_en   → bilingual learner instructions (EN)
 *  • instructions_ar   → bilingual learner instructions (AR)
 *  • cohort_scope      → 'all' or 'specific' — drives the cohort pivot
 *  • pass_score        → score required for "Pass"
 *  • total_score       → cached sum of all question scores (for fast listing)
 *  • status            → 'draft' or 'active' (publish workflow)
 *  • created_by        → admin/instructor who authored the assignment
 *
 * `file` is made nullable so rich (question-based) assignments don't require an
 * uploaded instructions file. Existing file-based rows keep working unchanged.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_assignments', function (Blueprint $table) {
            $table->string('title_ar', 255)->nullable()->after('title');
            $table->text('instructions_en')->nullable()->after('due_date');
            $table->text('instructions_ar')->nullable()->after('instructions_en');
            $table->enum('cohort_scope', ['all', 'specific'])->default('all')->after('instructions_ar');
            $table->unsignedInteger('pass_score')->nullable()->after('cohort_scope');
            $table->unsignedInteger('total_score')->default(0)->after('pass_score');
            $table->enum('status', ['draft', 'active'])->default('draft')->after('total_score');
            $table->unsignedBigInteger('created_by')->nullable()->after('status');

            $table->index(['course_id', 'status'], 'course_assignments_course_status_idx');
            $table->index('created_by', 'course_assignments_created_by_idx');
        });

        Schema::table('course_assignments', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('course_assignments', function (Blueprint $table) {
            $table->dropIndex('course_assignments_course_status_idx');
            $table->dropIndex('course_assignments_created_by_idx');
            $table->dropColumn([
                'title_ar',
                'instructions_en',
                'instructions_ar',
                'cohort_scope',
                'pass_score',
                'total_score',
                'status',
                'created_by',
            ]);
            $table->string('file')->nullable(false)->change();
        });
    }
};
