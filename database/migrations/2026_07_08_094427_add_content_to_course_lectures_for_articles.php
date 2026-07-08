<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Give "article" modules a proper home. Previously the rich-text HTML body
 * was dumped into the `video` column (with type=url) which was misleading —
 * an article is not a video/URL. This adds a dedicated nullable `content`
 * column, makes `video` nullable (link/article rows legitimately have none),
 * and migrates any existing article rows so their HTML moves out of `video`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_lectures')) {
            return;
        }

        Schema::table('course_lectures', function (Blueprint $table): void {
            if (! Schema::hasColumn('course_lectures', 'content')) {
                $table->longText('content')->nullable()->after('video');
            }
        });

        // `video` was NOT NULL — relax it so article/link modules can leave
        // it empty instead of stuffing unrelated data in.
        Schema::table('course_lectures', function (Blueprint $table): void {
            $table->text('video')->nullable()->change();
        });

        // Move legacy article bodies out of `video` into `content` and stamp
        // the clearer `type = article`. Idempotent: only touches rows still
        // holding their HTML in `video`.
        DB::table('course_lectures')
            ->where('content_type', 'article')
            ->whereNull('content')
            ->update([
                'content' => DB::raw('`video`'),
                'video'   => null,
                'type'    => 'article',
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_lectures')) {
            return;
        }

        // Fold article content back into `video` before dropping the column
        // so the legacy shape is restored on rollback.
        DB::table('course_lectures')
            ->where('content_type', 'article')
            ->whereNotNull('content')
            ->update([
                'video' => DB::raw('`content`'),
                'type'  => 'url',
            ]);

        Schema::table('course_lectures', function (Blueprint $table): void {
            if (Schema::hasColumn('course_lectures', 'content')) {
                $table->dropColumn('content');
            }
        });
    }
};
