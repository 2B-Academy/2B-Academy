<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_qualification_skills', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('qualification_skill_id')
                ->constrained('qualification_skills')
                ->cascadeOnDelete();

            $table->unique(
                ['course_id', 'qualification_skill_id'],
                'course_qualification_skill_unique'
            );

            $table->index('qualification_skill_id', 'course_qs_qs_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_qualification_skills');
    }
};
