<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_course_assignments', function (Blueprint $table) {
            $table->text('feedback')->nullable()->after('user_file');
            $table->string('score')->nullable()->after('feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_course_assignments', function (Blueprint $table) {
           $table->dropColumn('feedback');
              $table->dropColumn('score');
        });
    }
};
