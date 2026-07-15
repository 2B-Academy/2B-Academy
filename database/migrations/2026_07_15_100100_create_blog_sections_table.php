<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Blog sections
|--------------------------------------------------------------------------
| An ordered content block within a blog: a heading, an optional image, the
| rich-text body, and an optional pull-quote. `title`, `body` and `quote`
| are translatable (Spatie JSON columns). `body` holds sanitised HTML.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')
                  ->constrained('blogs')
                  ->cascadeOnDelete();
            $table->json('title');
            $table->string('image')->nullable();
            $table->json('body');
            $table->json('quote')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['blog_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_sections');
    }
};
