<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-question grading metadata for the 2026 admin Quiz workflow.
     *
     * Strictly additive. `answer_payload` stores the discriminated
     * learner response ({ value: ... } | { order: [...] }) used by
     * the new rich-question types; the existing `answer` text column
     * is left alone for legacy callers.
     */
    public function up(): void
    {
        if (! Schema::hasTable('user_exam_answers')) {
            return;
        }

        Schema::table('user_exam_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('user_exam_answers', 'awarded_score')) {
                $table->integer('awarded_score')->nullable()->default(0)->after('is_correct');
            }
            if (! Schema::hasColumn('user_exam_answers', 'feedback')) {
                $table->text('feedback')->nullable()->after('awarded_score');
            }
            if (! Schema::hasColumn('user_exam_answers', 'answer_payload')) {
                $table->json('answer_payload')->nullable()->after('feedback');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_exam_answers')) {
            return;
        }

        Schema::table('user_exam_answers', function (Blueprint $table) {
            foreach (['awarded_score', 'feedback', 'answer_payload'] as $col) {
                if (Schema::hasColumn('user_exam_answers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
