<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_exam_id');
            $table->text('question');
            $table->foreign('course_exam_id')->references('id')->on('course_exams')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_exam_questions');
    }
};
