<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extend `course_lectures` so each row represents a full "Module" as designed
 * in Figma (Content tab of Course Detail):
 *   - content_type      → video | document | article | link
 *   - learner_scope     → all   | cohort
 *   - session_id        → FK to course_sessions (when learner_scope = cohort)
 *   - duration_minutes  → approximate watch/read time in minutes
 *   - instructions      → translatable JSON instructions text
 *   - require_completion → must the module be completed before the next?
 *
 * Existing columns (title, type, video) are retained and continue to power
 * the legacy lecture API — the additions are purely additive and nullable
 * with safe defaults, so old rows keep working.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_lectures')) {
            return;
        }

        Schema::table('course_lectures', function (Blueprint $table): void {
            if (!Schema::hasColumn('course_lectures', 'content_type')) {
                $table->string('content_type', 16)
                    ->default('video')
                    ->after('type');
            }
            if (!Schema::hasColumn('course_lectures', 'learner_scope')) {
                $table->string('learner_scope', 16)
                    ->default('all')
                    ->after('content_type');
            }
            if (!Schema::hasColumn('course_lectures', 'session_id')) {
                $table->unsignedBigInteger('session_id')
                    ->nullable()
                    ->after('learner_scope');
            }
            if (!Schema::hasColumn('course_lectures', 'duration_minutes')) {
                $table->unsignedInteger('duration_minutes')
                    ->nullable()
                    ->after('session_id');
            }
            if (!Schema::hasColumn('course_lectures', 'instructions')) {
                $table->json('instructions')->nullable()->after('duration_minutes');
            }
            if (!Schema::hasColumn('course_lectures', 'require_completion')) {
                $table->boolean('require_completion')->default(false)->after('instructions');
            }
        });

        // Index commonly-filtered columns. Wrap in try/catch so the migration
        // is idempotent against partial/duplicate runs on developer DBs.
        try {
            Schema::table('course_lectures', function (Blueprint $table): void {
                $table->index(['course_id', 'content_type'], 'course_lectures_course_content_type_idx');
            });
        } catch (\Throwable) {}

        try {
            Schema::table('course_lectures', function (Blueprint $table): void {
                $table->index('session_id', 'course_lectures_session_id_idx');
            });
        } catch (\Throwable) {}
    }

    public function down(): void
    {
        if (!Schema::hasTable('course_lectures')) {
            return;
        }

        Schema::table('course_lectures', function (Blueprint $table): void {
            try { $table->dropIndex('course_lectures_course_content_type_idx'); } catch (\Throwable) {}
            try { $table->dropIndex('course_lectures_session_id_idx'); } catch (\Throwable) {}

            foreach (['require_completion', 'instructions', 'duration_minutes', 'session_id', 'learner_scope', 'content_type'] as $column) {
                if (Schema::hasColumn('course_lectures', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
