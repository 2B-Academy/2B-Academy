<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot linking rich quizzes (course_exams) to specific cohorts
     * (course_sessions). Only used when `course_exams.cohort_scope` = 'specific'.
     */
    public function up(): void
    {
        if (Schema::hasTable('course_exam_cohorts')) {
            return;
        }

        Schema::create('course_exam_cohorts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_exam_id');
            $table->unsignedBigInteger('course_session_id');
            $table->timestamps();

            $table->unique(['course_exam_id', 'course_session_id'], 'course_exam_cohorts_unique');
            $table->foreign('course_exam_id')
                ->references('id')->on('course_exams')->cascadeOnDelete();
            $table->foreign('course_session_id')
                ->references('id')->on('course_sessions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_exam_cohorts');
    }
};
