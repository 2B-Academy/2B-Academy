<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend `course_exams` so it can back the 2026 rich-question Quiz workflow.
     *
     * This migration is strictly additive — every new column is nullable or
     * defaulted. The legacy QuizController (which only reads `UserExam`
     * attempts) is unaffected, as is the section-bound exam flow exposed
     * to the learner-facing API.
     */
    public function up(): void
    {
        if (! Schema::hasTable('course_exams')) {
            return;
        }

        Schema::table('course_exams', function (Blueprint $table) {
            if (! Schema::hasColumn('course_exams', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }
            if (! Schema::hasColumn('course_exams', 'instructions_en')) {
                $table->text('instructions_en')->nullable()->after('title_ar');
            }
            if (! Schema::hasColumn('course_exams', 'instructions_ar')) {
                $table->text('instructions_ar')->nullable()->after('instructions_en');
            }
            if (! Schema::hasColumn('course_exams', 'due_date')) {
                $table->date('due_date')->nullable()->after('instructions_ar');
            }
            if (! Schema::hasColumn('course_exams', 'cohort_scope')) {
                $table->string('cohort_scope', 16)->default('all')->after('due_date');
            }
            if (! Schema::hasColumn('course_exams', 'pass_score')) {
                $table->integer('pass_score')->nullable()->after('cohort_scope');
            }
            if (! Schema::hasColumn('course_exams', 'total_score')) {
                $table->integer('total_score')->default(0)->after('pass_score');
            }
            if (! Schema::hasColumn('course_exams', 'status')) {
                $table->string('status', 16)->default('draft')->after('total_score');
            }
            if (! Schema::hasColumn('course_exams', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
            }
        });

        // Make section_id nullable so quizzes that are NOT bound to a course
        // section (the 2026 redesign uses course + cohort scope instead) can be
        // persisted without inventing a placeholder section.
        if (Schema::hasColumn('course_exams', 'section_id')) {
            try {
                Schema::table('course_exams', function (Blueprint $table) {
                    $table->unsignedBigInteger('section_id')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // Older MariaDB versions without doctrine/dbal — skip silently.
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_exams')) {
            return;
        }

        Schema::table('course_exams', function (Blueprint $table) {
            foreach ([
                'title_ar',
                'instructions_en',
                'instructions_ar',
                'due_date',
                'cohort_scope',
                'pass_score',
                'total_score',
                'status',
                'created_by',
            ] as $col) {
                if (Schema::hasColumn('course_exams', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
