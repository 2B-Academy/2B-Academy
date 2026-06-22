<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add rich-question fields to `course_exam_questions`.
     *
     * Strictly additive — the existing translatable `question` column and the
     * legacy `course_exam_question_answers` rows continue to back the existing
     * MCQ-only flow. The new admin Quiz workflow stores its bilingual prompt
     * and per-type metadata in the new columns added here.
     */
    public function up(): void
    {
        if (! Schema::hasTable('course_exam_questions')) {
            return;
        }

        Schema::table('course_exam_questions', function (Blueprint $table) {
            if (! Schema::hasColumn('course_exam_questions', 'position')) {
                $table->integer('position')->default(0)->after('course_exam_id');
            }
            if (! Schema::hasColumn('course_exam_questions', 'type')) {
                $table->string('type', 16)->default('mcq')->after('position');
            }
            if (! Schema::hasColumn('course_exam_questions', 'score')) {
                $table->integer('score')->default(0)->after('type');
            }
            if (! Schema::hasColumn('course_exam_questions', 'question_en')) {
                $table->text('question_en')->nullable()->after('score');
            }
            if (! Schema::hasColumn('course_exam_questions', 'question_ar')) {
                $table->text('question_ar')->nullable()->after('question_en');
            }
            if (! Schema::hasColumn('course_exam_questions', 'options_en')) {
                $table->json('options_en')->nullable()->after('question_ar');
            }
            if (! Schema::hasColumn('course_exam_questions', 'options_ar')) {
                $table->json('options_ar')->nullable()->after('options_en');
            }
            if (! Schema::hasColumn('course_exam_questions', 'correct_answer_en')) {
                $table->text('correct_answer_en')->nullable()->after('options_ar');
            }
            if (! Schema::hasColumn('course_exam_questions', 'correct_answer_ar')) {
                $table->text('correct_answer_ar')->nullable()->after('correct_answer_en');
            }
            if (! Schema::hasColumn('course_exam_questions', 'explanation_en')) {
                $table->text('explanation_en')->nullable()->after('correct_answer_ar');
            }
            if (! Schema::hasColumn('course_exam_questions', 'explanation_ar')) {
                $table->text('explanation_ar')->nullable()->after('explanation_en');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_exam_questions')) {
            return;
        }

        Schema::table('course_exam_questions', function (Blueprint $table) {
            foreach ([
                'position',
                'type',
                'score',
                'question_en',
                'question_ar',
                'options_en',
                'options_ar',
                'correct_answer_en',
                'correct_answer_ar',
                'explanation_en',
                'explanation_ar',
            ] as $col) {
                if (Schema::hasColumn('course_exam_questions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
