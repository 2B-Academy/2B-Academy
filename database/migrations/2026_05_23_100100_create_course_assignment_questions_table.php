<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rich question bank attached to each course_assignment.
 *
 * Supported question types:
 *  • mcq       — multiple-choice (single correct) — options stored in `options_*`
 *  • yes_no    — boolean answer
 *  • open      — free-text/short-answer question, manually graded
 *  • reorder   — learner reorders a list — items stored in `options_*`, correct
 *                sequence stored in `correct_answer_*`
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_assignment_id');
            $table->unsignedInteger('position')->default(0);
            $table->enum('type', ['mcq', 'yes_no', 'open', 'reorder']);
            $table->unsignedInteger('score')->default(0);
            $table->text('question_en');
            $table->text('question_ar')->nullable();
            $table->json('options_en')->nullable();
            $table->json('options_ar')->nullable();
            $table->text('correct_answer_en')->nullable();
            $table->text('correct_answer_ar')->nullable();
            $table->text('explanation_en')->nullable();
            $table->text('explanation_ar')->nullable();
            $table->timestamps();

            $table->foreign('course_assignment_id', 'caq_assignment_fk')
                ->references('id')->on('course_assignments')
                ->onDelete('cascade');

            $table->index(['course_assignment_id', 'position'], 'caq_assignment_position_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_assignment_questions');
    }
};
