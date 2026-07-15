<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Blogs are authored from the admin dashboard, whose authenticated entity
| is an Admin (admins table) — not a User. Re-point the creator FK to
| `admins`, mirroring lms_resources.created_by_admin_id.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn('created_by_user_id');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('created_by_admin_id')
                  ->nullable()
                  ->after('qualification_skill_id')
                  ->constrained('admins')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign(['created_by_admin_id']);
            $table->dropColumn('created_by_admin_id');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')
                  ->nullable()
                  ->after('qualification_skill_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }
};
