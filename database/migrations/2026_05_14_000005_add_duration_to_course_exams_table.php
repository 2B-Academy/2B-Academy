<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_exams') || Schema::hasColumn('course_exams', 'duration')) {
            return;
        }

        Schema::table('course_exams', function (Blueprint $table) {
            $table->bigInteger('duration')->default(60)->after('degree');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('course_exams') || ! Schema::hasColumn('course_exams', 'duration')) {
            return;
        }

        Schema::table('course_exams', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
