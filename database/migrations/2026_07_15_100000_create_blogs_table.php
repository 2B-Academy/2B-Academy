<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Blogs
|--------------------------------------------------------------------------
| A learner-facing editorial blog. A blog is a rich article composed of an
| ordered set of `blog_sections`. Translatable fields (title, subtitle) are
| stored as Spatie JSON columns ({"ar": "...", "en": "..."}).
|
| `author_user_id`  — the public author ("Owner" in the dashboard). When
|                     `is_anonymous` is true the site shows the org name.
| `created_by_user_id` — the admin who authored the entry ("Added by …").
| `qualification_skill_id` — surfaces the blog in the matching qualification
|                     view and drives the topic chip on the cards.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('level')->nullable(); // beginner | intermediate | professional

            $table->foreignId('author_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->boolean('is_anonymous')->default(false);

            $table->unsignedSmallInteger('reading_time')->nullable(); // minutes

            $table->foreignId('qualification_skill_id')
                  ->nullable()
                  ->constrained('qualification_skills')
                  ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->boolean('active')->default(true);
            $table->date('published_at')->nullable();
            $table->timestamps();

            $table->index(['active', 'published_at']);
            $table->index('qualification_skill_id');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
