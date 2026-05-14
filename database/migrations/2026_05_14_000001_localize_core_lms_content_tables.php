<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── courses ──────────────────────────────────────────────────────────
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('title', 'title_backup');
            $table->renameColumn('description', 'description_backup');
            $table->renameColumn('title_for_certificate', 'title_for_certificate_backup');
            $table->renameColumn('notification_text', 'notification_text_backup');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->json('title')->nullable()->after('id');
            $table->json('description')->nullable()->after('title');
            $table->json('title_for_certificate')->nullable()->after('description');
            $table->json('notification_text')->nullable()->after('title_for_certificate');
        });

        DB::statement("UPDATE courses SET title = JSON_OBJECT('ar', title_backup)");
        DB::statement("UPDATE courses SET description = JSON_OBJECT('ar', description_backup)");
        DB::statement("UPDATE courses SET title_for_certificate = IF(title_for_certificate_backup IS NOT NULL, JSON_OBJECT('ar', title_for_certificate_backup), NULL)");
        DB::statement("UPDATE courses SET notification_text = IF(notification_text_backup IS NOT NULL, JSON_OBJECT('ar', notification_text_backup), NULL)");

        // ── categories ───────────────────────────────────────────────────────
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name', 'name_backup');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->json('name')->nullable()->after('id');
        });

        DB::statement("UPDATE categories SET name = JSON_OBJECT('ar', name_backup)");

        // ── instructors ──────────────────────────────────────────────────────
        Schema::table('instructors', function (Blueprint $table) {
            $table->renameColumn('name', 'name_backup');
            $table->renameColumn('bio', 'bio_backup');
            $table->renameColumn('job_title', 'job_title_backup');
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->json('name')->nullable()->after('id');
            $table->json('bio')->nullable()->after('name');
            $table->json('job_title')->nullable()->after('bio');
        });

        DB::statement("UPDATE instructors SET name = JSON_OBJECT('ar', name_backup)");
        DB::statement("UPDATE instructors SET bio = JSON_OBJECT('ar', bio_backup)");
        DB::statement("UPDATE instructors SET job_title = JSON_OBJECT('ar', job_title_backup)");
    }

    public function down(): void
    {
        // ── courses ──────────────────────────────────────────────────────────
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'title_for_certificate', 'notification_text']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('title_backup', 'title');
            $table->renameColumn('description_backup', 'description');
            $table->renameColumn('title_for_certificate_backup', 'title_for_certificate');
            $table->renameColumn('notification_text_backup', 'notification_text');
        });

        // ── categories ───────────────────────────────────────────────────────
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name_backup', 'name');
        });

        // ── instructors ──────────────────────────────────────────────────────
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn(['name', 'bio', 'job_title']);
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->renameColumn('name_backup', 'name');
            $table->renameColumn('bio_backup', 'bio');
            $table->renameColumn('job_title_backup', 'job_title');
        });
    }
};
