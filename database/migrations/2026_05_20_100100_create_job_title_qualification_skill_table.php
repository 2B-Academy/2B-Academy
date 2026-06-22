<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_title_qualification_skill', function (Blueprint $table) {
            $table->foreignId('job_title_id')
                  ->constrained('job_titles')
                  ->cascadeOnDelete();
            $table->foreignId('qualification_skill_id')
                  ->constrained('qualification_skills')
                  ->cascadeOnDelete();
            $table->primary(['job_title_id', 'qualification_skill_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_title_qualification_skill');
    }
};
