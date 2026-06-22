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
        Schema::table('admin_message_recipients', function (Blueprint $table) {
            // Allow a recipient to be either a learner (user_id) or an instructor (instructor_id).
            // Existing rows keep user_id; new instructor recipients set instructor_id only.
            $table->unsignedBigInteger('instructor_id')->nullable()->after('user_id');
            $table->foreign('instructor_id')->references('id')->on('instructors')->nullOnDelete();

            // user_id was NOT NULL before — make it nullable so instructor-only rows are valid.
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admin_message_recipients', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
