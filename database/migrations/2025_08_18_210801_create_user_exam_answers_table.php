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
        Schema::create('user_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_exam_id')->constrained('user_exams')->onDelete('cascade');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->string('question')->nullable();
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exam_answers');
    }
};
