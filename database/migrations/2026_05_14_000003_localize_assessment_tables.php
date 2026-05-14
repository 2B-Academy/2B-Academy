<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── evaluation_categories ─────────────────────────────────────────────
        Schema::table('evaluation_categories', function (Blueprint $table) {
            $table->renameColumn('name', 'name_backup');
        });

        Schema::table('evaluation_categories', function (Blueprint $table) {
            $table->json('name')->nullable()->after('id');
        });

        DB::statement("UPDATE evaluation_categories SET name = JSON_OBJECT('ar', name_backup)");

        // ── evaluations ───────────────────────────────────────────────────────
        Schema::table('evaluations', function (Blueprint $table) {
            $table->renameColumn('title', 'title_backup');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->json('title')->nullable()->after('type');
        });

        DB::statement("UPDATE evaluations SET title = JSON_OBJECT('ar', title_backup)");

        // ── forms ─────────────────────────────────────────────────────────────
        Schema::table('forms', function (Blueprint $table) {
            $table->renameColumn('title', 'title_backup');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->json('title')->nullable()->after('uuid');
        });

        DB::statement("UPDATE forms SET title = JSON_OBJECT('ar', title_backup)");

        // ── form_questions ────────────────────────────────────────────────────
        Schema::table('form_questions', function (Blueprint $table) {
            $table->renameColumn('question', 'question_backup');
        });

        Schema::table('form_questions', function (Blueprint $table) {
            $table->json('question')->nullable()->after('type');
        });

        DB::statement("UPDATE form_questions SET question = JSON_OBJECT('ar', question_backup)");

        // ── form_question_answers ─────────────────────────────────────────────
        Schema::table('form_question_answers', function (Blueprint $table) {
            $table->renameColumn('answer', 'answer_backup');
        });

        Schema::table('form_question_answers', function (Blueprint $table) {
            $table->json('answer')->nullable()->after('form_question_id');
        });

        DB::statement("UPDATE form_question_answers SET answer = JSON_OBJECT('ar', answer_backup)");

        // ── public_notifications ──────────────────────────────────────────────
        Schema::table('public_notifications', function (Blueprint $table) {
            $table->renameColumn('title', 'title_backup');
            $table->renameColumn('body', 'body_backup');
        });

        Schema::table('public_notifications', function (Blueprint $table) {
            $table->json('title')->nullable()->after('id');
            $table->json('body')->nullable()->after('title');
        });

        DB::statement("UPDATE public_notifications SET title = JSON_OBJECT('ar', title_backup)");
        DB::statement("UPDATE public_notifications SET body = JSON_OBJECT('ar', body_backup)");
    }

    public function down(): void
    {
        // ── evaluation_categories ─────────────────────────────────────────────
        Schema::table('evaluation_categories', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('evaluation_categories', function (Blueprint $table) {
            $table->renameColumn('name_backup', 'name');
        });

        // ── evaluations ───────────────────────────────────────────────────────
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->renameColumn('title_backup', 'title');
        });

        // ── forms ─────────────────────────────────────────────────────────────
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->renameColumn('title_backup', 'title');
        });

        // ── form_questions ────────────────────────────────────────────────────
        Schema::table('form_questions', function (Blueprint $table) {
            $table->dropColumn('question');
        });

        Schema::table('form_questions', function (Blueprint $table) {
            $table->renameColumn('question_backup', 'question');
        });

        // ── form_question_answers ─────────────────────────────────────────────
        Schema::table('form_question_answers', function (Blueprint $table) {
            $table->dropColumn('answer');
        });

        Schema::table('form_question_answers', function (Blueprint $table) {
            $table->renameColumn('answer_backup', 'answer');
        });

        // ── public_notifications ──────────────────────────────────────────────
        Schema::table('public_notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'body']);
        });

        Schema::table('public_notifications', function (Blueprint $table) {
            $table->renameColumn('title_backup', 'title');
            $table->renameColumn('body_backup', 'body');
        });
    }
};
