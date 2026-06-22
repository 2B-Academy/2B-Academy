<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `file_name` (original filename) to `course_lectures` so the admin
 * "Edit Module" dialog can render `File Title.pdf - 313 KB` for uploaded
 * documents instead of the obfuscated storage hash. Stays nullable to remain
 * a no-op for URL-based content types (video / article / link).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_lectures')) {
            return;
        }

        Schema::table('course_lectures', function (Blueprint $table): void {
            if (!Schema::hasColumn('course_lectures', 'file_name')) {
                $table->string('file_name', 255)->nullable()->after('video');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('course_lectures')) {
            return;
        }

        Schema::table('course_lectures', function (Blueprint $table): void {
            if (Schema::hasColumn('course_lectures', 'file_name')) {
                $table->dropColumn('file_name');
            }
        });
    }
};
