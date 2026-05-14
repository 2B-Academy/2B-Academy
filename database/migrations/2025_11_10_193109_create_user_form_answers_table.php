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
        Schema::create('user_form_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_form_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->string('question')->nullable();
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('is_true')->default(false);
            $table->foreign('user_form_id')->references('id')->on('user_forms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_form_answers');
    }
};
