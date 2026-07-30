<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Related to session number" — the week/session a module belongs to (Figma
 * 355-9951). Drives the course-player's "Week N" grouping on the website.
 * Nullable so existing lectures keep working (they fall into "General
 * content" until an admin assigns a session number).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->unsignedInteger('session_number')->nullable()->after('session_id');
            $table->index(['course_id', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'session_number']);
            $table->dropColumn('session_number');
        });
    }
};
