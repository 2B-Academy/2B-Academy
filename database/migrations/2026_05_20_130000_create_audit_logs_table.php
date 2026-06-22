<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type')->default('admin'); // 'admin' or 'user'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // denormalized for performance
            $table->string('action'); // 'rpl_approved', 'rpl_rejected', 'course_published', etc.
            $table->string('model_type')->nullable(); // e.g. 'Course', 'User'
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['user_type', 'user_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
