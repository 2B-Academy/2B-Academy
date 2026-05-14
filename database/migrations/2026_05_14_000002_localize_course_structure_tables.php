<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── course_sections ───────────────────────────────────────────────────
        Schema::table('course_sections', function (Blueprint $table) {
            $table->renameColumn('name', 'name_backup');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->json('name')->nullable()->after('course_id');
        });

        DB::statement("UPDATE course_sections SET name = JSON_OBJECT('ar', name_backup)");

        // ── course_lectures ───────────────────────────────────────────────────
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->renameColumn('title', 'title_backup');
        });

        Schema::table('course_lectures', function (Blueprint $table) {
            $table->json('title')->nullable()->after('section_id');
        });

        DB::statement("UPDATE course_lectures SET title = JSON_OBJECT('ar', title_backup)");

        // ── course_exams ──────────────────────────────────────────────────────
        Schema::table('course_exams', function (Blueprint $table) {
            $table->renameColumn('title', 'title_backup');
        });

        Schema::table('course_exams', function (Blueprint $table) {
            $table->json('title')->nullable()->after('section_id');
        });

        DB::statement("UPDATE course_exams SET title = JSON_OBJECT('ar', title_backup)");

        // ── course_exam_questions ─────────────────────────────────────────────
        Schema::table('course_exam_questions', function (Blueprint $table) {
            $table->renameColumn('question', 'question_backup');
        });

        Schema::table('course_exam_questions', function (Blueprint $table) {
            $table->json('question')->nullable()->after('course_exam_id');
        });

        DB::statement("UPDATE course_exam_questions SET question = JSON_OBJECT('ar', question_backup)");

        // ── course_exam_question_answers ──────────────────────────────────────
        Schema::table('course_exam_question_answers', function (Blueprint $table) {
            $table->renameColumn('answer', 'answer_backup');
        });

        Schema::table('course_exam_question_answers', function (Blueprint $table) {
            $table->json('answer')->nullable()->after('question_id');
        });

        DB::statement("UPDATE course_exam_question_answers SET answer = JSON_OBJECT('ar', answer_backup)");
    }

    public function down(): void
    {
        // ── course_sections ───────────────────────────────────────────────────
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->renameColumn('name_backup', 'name');
        });

        // ── course_lectures ───────────────────────────────────────────────────
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('course_lectures', function (Blueprint $table) {
            $table->renameColumn('title_backup', 'title');
        });

        // ── course_exams ──────────────────────────────────────────────────────
        Schema::table('course_exams', function (Blueprint $table) {
            $table->dropColumn('title');
        });

        Schema::table('course_exams', function (Blueprint $table) {
            $table->renameColumn('title_backup', 'title');
        });

        // ── course_exam_questions ─────────────────────────────────────────────
        Schema::table('course_exam_questions', function (Blueprint $table) {
            $table->dropColumn('question');
        });

        Schema::table('course_exam_questions', function (Blueprint $table) {
            $table->renameColumn('question_backup', 'question');
        });

        // ── course_exam_question_answers ──────────────────────────────────────
        Schema::table('course_exam_question_answers', function (Blueprint $table) {
            $table->dropColumn('answer');
        });

        Schema::table('course_exam_question_answers', function (Blueprint $table) {
            $table->renameColumn('answer_backup', 'answer');
        });
    }
};
