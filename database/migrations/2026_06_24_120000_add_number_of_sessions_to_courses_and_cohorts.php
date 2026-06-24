<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Number of Sessions" (Figma 321:7349 / 332:10708).
 *
 * Captured once at course creation and then *read-only* on the course —
 * each cohort defaults to the course value but the admin may override it
 * per cohort. Once a cohort has held that many sessions it flips to
 * `completed`; raising the count re-opens it (handled in
 * Course::deriveCohortStatus).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'number_of_sessions')) {
                $table->unsignedInteger('number_of_sessions')->nullable()->after('max_learners');
            }
        });

        Schema::table('course_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('course_sections', 'number_of_sessions')) {
                $table->unsignedInteger('number_of_sessions')->nullable()->after('capacity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'number_of_sessions')) {
                $table->dropColumn('number_of_sessions');
            }
        });

        Schema::table('course_sections', function (Blueprint $table) {
            if (Schema::hasColumn('course_sections', 'number_of_sessions')) {
                $table->dropColumn('number_of_sessions');
            }
        });
    }
};
