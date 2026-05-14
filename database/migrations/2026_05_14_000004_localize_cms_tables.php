<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Pattern A: tables already have _ar/_en split columns.
 * Strategy: add new JSON columns alongside the existing split columns,
 * populate from them, and leave the originals as backup.
 * down() drops only the new JSON columns — originals remain untouched.
 */

return new class extends Migration
{
    public function up(): void
    {
        // ── articles ──────────────────────────────────────────────────────────
        Schema::table('articles', function (Blueprint $table) {
            $table->json('title')->nullable()->after('type');
            $table->json('description')->nullable()->after('title');
        });

        DB::statement("UPDATE articles SET title = JSON_OBJECT('ar', COALESCE(title_ar, ''), 'en', COALESCE(title_en, ''))");
        DB::statement("UPDATE articles SET description = JSON_OBJECT('ar', COALESCE(description_ar, ''), 'en', COALESCE(description_en, ''))");

        // ── abouts ────────────────────────────────────────────────────────────
        Schema::table('abouts', function (Blueprint $table) {
            $table->json('about')->nullable()->after('id');
            $table->json('mission')->nullable()->after('about');
            $table->json('vision')->nullable()->after('mission');
            $table->json('goals')->nullable()->after('vision');
        });

        DB::statement("UPDATE abouts SET about   = JSON_OBJECT('ar', COALESCE(about_ar, ''),   'en', COALESCE(about_en, ''))");
        DB::statement("UPDATE abouts SET mission = JSON_OBJECT('ar', COALESCE(mission_ar, ''), 'en', COALESCE(mission_en, ''))");
        DB::statement("UPDATE abouts SET vision  = JSON_OBJECT('ar', COALESCE(vision_ar, ''),  'en', COALESCE(vision_en, ''))");
        DB::statement("UPDATE abouts SET goals   = JSON_OBJECT('ar', COALESCE(goals_ar, ''),   'en', COALESCE(goals_en, ''))");

        // ── testimonials ──────────────────────────────────────────────────────
        Schema::table('testimonials', function (Blueprint $table) {
            $table->json('name')->nullable()->after('id');
            $table->json('description')->nullable()->after('name');
        });

        DB::statement("UPDATE testimonials SET name        = JSON_OBJECT('ar', COALESCE(name_ar, ''),        'en', COALESCE(name_en, ''))");
        DB::statement("UPDATE testimonials SET description = JSON_OBJECT('ar', COALESCE(description_ar, ''), 'en', COALESCE(description_en, ''))");
    }

    public function down(): void
    {
        // Drop only the new JSON columns — leave _ar/_en originals intact
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('abouts', function (Blueprint $table) {
            $table->dropColumn(['about', 'mission', 'vision', 'goals']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });
    }
};
