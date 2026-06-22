<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds composite indexes on the most filtered/ordered columns so the admin
 * list endpoints (courses, users) don't run unindexed table scans on every
 * search/filter/tab-change.
 *
 * Indexes are added defensively — if one already exists we skip it so this
 * migration can be re-run safely on any environment.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('courses',       'courses_active_idx',         ['active']);
        $this->addIndexIfMissing('courses',       'courses_course_type_idx',    ['course_type']);
        $this->addIndexIfMissing('courses',       'courses_category_id_idx',    ['category_id']);
        $this->addIndexIfMissing('courses',       'courses_active_type_idx',    ['active', 'course_type']);

        $this->addIndexIfMissing('users',         'users_learner_type_idx',     ['learner_type']);
        $this->addIndexIfMissing('users',         'users_name_idx',             ['name']);
        $this->addIndexIfMissing('users',         'users_machine_code_idx',     ['machine_code']);
        $this->addIndexIfMissing('users',         'users_department_name_idx',  ['department_name']);

        $this->addIndexIfMissing('course_sessions', 'course_sessions_course_id_idx', ['course_id']);
    }

    public function down(): void
    {
        $this->dropIndexIfExists('courses',       'courses_active_idx');
        $this->dropIndexIfExists('courses',       'courses_course_type_idx');
        $this->dropIndexIfExists('courses',       'courses_category_id_idx');
        $this->dropIndexIfExists('courses',       'courses_active_type_idx');

        $this->dropIndexIfExists('users',         'users_learner_type_idx');
        $this->dropIndexIfExists('users',         'users_name_idx');
        $this->dropIndexIfExists('users',         'users_machine_code_idx');
        $this->dropIndexIfExists('users',         'users_department_name_idx');

        $this->dropIndexIfExists('course_sessions', 'course_sessions_course_id_idx');
    }

    private function addIndexIfMissing(string $table, string $name, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        foreach ($columns as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return;
            }
        }
        if ($this->indexExists($table, $name)) {
            return;
        }
        Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $name)) {
            return;
        }
        Schema::table($table, fn (Blueprint $t) => $t->dropIndex($name));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::connection()->getDatabaseName();
        $row = DB::selectOne(
            'SELECT COUNT(1) AS c
               FROM information_schema.statistics
              WHERE table_schema = ?
                AND table_name   = ?
                AND index_name   = ?',
            [$database, $table, $indexName],
        );

        return ($row->c ?? 0) > 0;
    }
};
