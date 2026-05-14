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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->text('about_en')->nullable();
            $table->text('about_ar')->nullable();
            $table->text('mission_en')->nullable();
            $table->text('mission_ar')->nullable();
            $table->text('vision_en')->nullable();
            $table->text('vision_ar')->nullable();
            $table->text('goals_en')->nullable();
            $table->text('goals_ar')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
