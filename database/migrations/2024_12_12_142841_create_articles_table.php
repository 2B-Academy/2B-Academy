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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->enum('type' , ['news', 'blogs', 'event']);
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar');
            $table->string('slug')->unique();
            $table->date('date_publish')->nullable();
            $table->string('image');
            $table->boolean('is_home')->default(false)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
