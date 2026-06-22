<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['article', 'link', 'file']);
            $table->longText('content')->nullable();
            $table->string('url')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('qualification_skill_id')
                  ->nullable()
                  ->constrained('qualification_skills')
                  ->nullOnDelete();
            $table->foreignId('created_by_admin_id')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_resources');
    }
};
