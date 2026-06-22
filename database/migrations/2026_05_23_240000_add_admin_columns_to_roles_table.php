<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the additive columns required by the 2026 admin Roles redesign:
 *
 *   - name_en / name_ar           bilingual display name (Spatie's `name`
 *                                 stays as the canonical machine key so
 *                                 every legacy role-check keeps working).
 *   - description_en / description_ar
 *   - color                       one of: teal | green | orange | red | blue
 *   - is_system                   true for built-in roles that should not
 *                                 be deletable from the UI (Super Admin,
 *                                 Admin, Instructor).
 *
 * Strictly additive — the existing Spatie schema is untouched, every
 * `role:Admin` middleware call and every `$user->hasRole('superAdmin')`
 * check continues to work exactly as before.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'name_en')) {
                $table->string('name_en', 191)->nullable()->after('name');
            }
            if (!Schema::hasColumn('roles', 'name_ar')) {
                $table->string('name_ar', 191)->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('roles', 'description_en')) {
                $table->string('description_en', 500)->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('roles', 'description_ar')) {
                $table->string('description_ar', 500)->nullable()->after('description_en');
            }
            if (!Schema::hasColumn('roles', 'color')) {
                $table->string('color', 20)->default('teal')->nullable()->after('description_ar');
            }
            if (!Schema::hasColumn('roles', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('color');
            }
        });

        // Backfill existing rows so the admin UI never displays NULL names.
        DB::table('roles')->whereNull('name_en')->update([
            'name_en' => DB::raw('`name`'),
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            foreach (['is_system', 'color', 'description_ar', 'description_en', 'name_ar', 'name_en'] as $col) {
                if (Schema::hasColumn('roles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
