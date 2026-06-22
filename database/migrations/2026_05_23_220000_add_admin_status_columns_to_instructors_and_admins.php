<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add the same activity / status columns that the 2026 admin Users redesign
 * already added to the `users` table to the sibling `instructors` and
 * `admins` tables, so that the unified admin Users view can surface all
 * three personas through a single filterable list with consistent KPIs.
 *
 * Everything here is strictly additive — no legacy columns are renamed or
 * dropped — and the new columns are nullable with sensible defaults so
 * existing code paths that never read them stay correct.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('instructors')) {
            Schema::table('instructors', function (Blueprint $table) {
                if (! Schema::hasColumn('instructors', 'status')) {
                    $table->string('status', 20)
                        ->default('active')
                        ->nullable()
                        ->after('job_title');
                }
                if (! Schema::hasColumn('instructors', 'last_active_at')) {
                    $table->timestamp('last_active_at')->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (! Schema::hasColumn('admins', 'status')) {
                    $table->string('status', 20)
                        ->default('active')
                        ->nullable()
                        ->after('email');
                }
                if (! Schema::hasColumn('admins', 'last_active_at')) {
                    $table->timestamp('last_active_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('admins', 'job_title')) {
                    $table->string('job_title')->nullable()->after('last_active_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('instructors')) {
            Schema::table('instructors', function (Blueprint $table) {
                foreach (['status', 'last_active_at'] as $col) {
                    if (Schema::hasColumn('instructors', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                foreach (['status', 'last_active_at', 'job_title'] as $col) {
                    if (Schema::hasColumn('admins', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
