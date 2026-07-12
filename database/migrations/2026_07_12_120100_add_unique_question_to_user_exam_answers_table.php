<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The legacy MCQ-only flow (UserExamService::submit) always creates a brand
 * new `UserExam` attempt per submission and writes each answer exactly once,
 * so no (user_exam_id, question_id) duplicate was ever possible.
 *
 * The new learner-facing rich quiz flow (LearnerQuizService) persists one
 * answer PER QUESTION as the learner progresses, upserting on
 * (user_exam_id, question_id) so a returning/disconnected learner resumes
 * cleanly. This unique index makes that upsert safe at the DB level.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_exam_answers')) {
            return;
        }

        try {
            Schema::table('user_exam_answers', function (Blueprint $table) {
                $table->unique(['user_exam_id', 'question_id'], 'user_exam_answers_exam_question_unique');
            });
        } catch (\Throwable) {
            // Already exists / duplicate legacy rows on a dev DB — skip.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_exam_answers')) {
            return;
        }

        try {
            Schema::table('user_exam_answers', function (Blueprint $table) {
                $table->dropUnique('user_exam_answers_exam_question_unique');
            });
        } catch (\Throwable) {
        }
    }
};
