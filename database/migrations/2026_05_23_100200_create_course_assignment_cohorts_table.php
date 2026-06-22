<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot linking an assignment to specific cohorts (a.k.a. course_sessions in
 * this project). Only used when `course_assignments.cohort_scope = 'specific'`.
 * If scope is `all`, no rows are written and the assignment targets every
 * cohort of the parent course.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_assignment_cohorts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_assignment_id');
            $table->unsignedBigInteger('course_session_id');
            $table->timestamps();

            $table->foreign('course_assignment_id', 'cac_assignment_fk')
                ->references('id')->on('course_assignments')
                ->onDelete('cascade');

            $table->foreign('course_session_id', 'cac_session_fk')
                ->references('id')->on('course_sessions')
                ->onDelete('cascade');

            $table->unique(['course_assignment_id', 'course_session_id'], 'cac_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_assignment_cohorts');
    }
};
