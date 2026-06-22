<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The mobile rating flow (S-05) stores a NULL comment whenever the
 * learner rates a course above the "comment required" threshold — i.e.
 * the common happy path of a high rating with no written feedback.
 *
 * The original course_ratings table declared `comment` as NOT NULL,
 * which made MobileRatingService::submit() crash with an integrity
 * constraint violation on every comment-less rating. Relaxing the
 * column to nullable matches the service's intent and is strictly more
 * permissive (existing admin flows that always send a comment are
 * unaffected).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_ratings', function (Blueprint $table) {
            $table->text('comment')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('course_ratings', function (Blueprint $table) {
            $table->text('comment')->nullable(false)->change();
        });
    }
};
