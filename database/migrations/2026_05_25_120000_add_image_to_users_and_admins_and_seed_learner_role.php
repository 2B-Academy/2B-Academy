<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026 Admin "Users" form redesign — Figma 529:38878:
 *
 *   1. Adds a nullable `image` column to both `users` and `admins`
 *      so every person table can store an avatar. `instructors` already
 *      has the column (and we relax it to nullable here for parity).
 *
 *   2. Seeds the missing `learner` system role on the admin guard so
 *      the Add/Edit User form can render the "Role" dropdown straight
 *      from the `roles` table (single source of truth) instead of the
 *      previously hardcoded `['admin','instructor','learner']` array
 *      that AdminUserService used to build by hand.
 *
 * Strictly additive — no legacy columns are renamed or dropped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'image')) {
                $table->string('image')->nullable()->after('name_ar');
            }
        });

        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'image')) {
                $table->string('image')->nullable()->after('email');
            }
        });

        // Relax `instructors.image` to nullable so HR webhook syncs and
        // the new Add User modal can omit it. Existing non-empty values
        // are kept untouched.
        if (Schema::hasColumn('instructors', 'image')) {
            Schema::table('instructors', function (Blueprint $table) {
                $table->string('image')->nullable()->change();
            });
        }

        if (! Schema::hasTable('roles')) {
            return;
        }

        $exists = DB::table('roles')
            ->where('name', 'learner')
            ->where('guard_name', 'admin')
            ->exists();

        if (! $exists) {
            $now = Carbon::now();
            DB::table('roles')->insert([
                'name'           => 'learner',
                'guard_name'     => 'admin',
                'name_en'        => 'Learner',
                'name_ar'        => 'متدرّب',
                'description_en' => 'A learner enrolled in courses across the academy.',
                'description_ar' => 'متدرّب مسجّل في دورات الأكاديمية.',
                'color'          => 'teal',
                'is_system'      => true,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'image')) {
                $table->dropColumn('image');
            }
        });

        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'image')) {
                $table->dropColumn('image');
            }
        });

        if (Schema::hasTable('roles')) {
            DB::table('roles')
                ->where('name', 'learner')
                ->where('guard_name', 'admin')
                ->delete();
        }
    }
};
