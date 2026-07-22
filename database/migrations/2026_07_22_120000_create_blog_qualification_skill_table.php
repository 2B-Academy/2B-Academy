<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Blogs move from a single `qualification_skill_id` FK to a many-to-many
 * relation: a blog can now surface under multiple qualification skills.
 * Existing single assignments are backfilled into the pivot, then the legacy
 * column is dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_qualification_skill', function (Blueprint $table) {
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->foreignId('qualification_skill_id')->constrained('qualification_skills')->cascadeOnDelete();
            $table->primary(['blog_id', 'qualification_skill_id']);
        });

        if (Schema::hasColumn('blogs', 'qualification_skill_id')) {
            // Backfill: preserve every existing single assignment in the pivot.
            DB::table('blogs')
                ->whereNotNull('qualification_skill_id')
                ->select('id', 'qualification_skill_id')
                ->orderBy('id')
                ->chunk(200, function ($rows) {
                    DB::table('blog_qualification_skill')->insertOrIgnore(
                        $rows->map(fn ($r) => [
                            'blog_id'                => $r->id,
                            'qualification_skill_id' => $r->qualification_skill_id,
                        ])->all(),
                    );
                });

            Schema::table('blogs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('qualification_skill_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('qualification_skill_id')
                ->nullable()
                ->after('reading_time')
                ->constrained('qualification_skills')
                ->nullOnDelete();
            $table->index('qualification_skill_id');
        });

        // Best-effort restore: put the first pivot qualification back on the blog.
        foreach (DB::table('blog_qualification_skill')->orderBy('blog_id')->get()->groupBy('blog_id') as $blogId => $rows) {
            DB::table('blogs')->where('id', $blogId)->update([
                'qualification_skill_id' => $rows->first()->qualification_skill_id,
            ]);
        }

        Schema::dropIfExists('blog_qualification_skill');
    }
};
