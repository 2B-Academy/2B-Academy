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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('user_machine_code')->nullable();
            $table->string('user_department')->nullable();
            $table->unsignedBigInteger('course_category_id')->nullable();
            $table->string('course_category_name')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->string('course_name')->nullable();
            $table->bigInteger('course_hours')->nullable()->default(0);
            $table->unsignedBigInteger('section_id')->nullable();
            $table->float('attendance_hours')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
