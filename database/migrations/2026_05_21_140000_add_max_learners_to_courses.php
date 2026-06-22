<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a per-course override for the maximum learners per cohort. When this
 * column is null the admin UI falls back to the platform-wide
 * `default_cohort_size` setting so existing courses continue to work without
 * any explicit override.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('max_learners')->nullable()->after('hours');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('max_learners');
        });
    }
};
