<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-question learner answers for a submission.
 *
 *  • answer          → JSON shape varies by question type:
 *      - mcq      : { "value": "Safety helmet" }
 *      - yes_no   : { "value": "yes" }
 *      - open     : { "value": "<free text>" }
 *      - reorder  : { "order": ["Step 1", "Step 2", ...] }
 *  • awarded_score   → auto-computed for objective types, manually set for `open`
 *  • is_correct      → true | false | null (open questions default to null until graded)
 *  • feedback        → optional grader comment (mainly for `open` questions)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_course_assignment_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_course_assignment_id');
            $table->unsignedBigInteger('course_assignment_question_id');
            $table->json('answer')->nullable();
            $table->unsignedInteger('awarded_score')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('user_course_assignment_id', 'ucaa_submission_fk')
                ->references('id')->on('user_course_assignments')
                ->onDelete('cascade');

            $table->foreign('course_assignment_question_id', 'ucaa_question_fk')
                ->references('id')->on('course_assignment_questions')
                ->onDelete('cascade');

            $table->unique(['user_course_assignment_id', 'course_assignment_question_id'], 'ucaa_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_course_assignment_answers');
    }
};
