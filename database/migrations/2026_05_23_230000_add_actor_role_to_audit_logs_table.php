<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add an `actor_role` column to `audit_logs` so the 2026 admin Audit Log
 * screen can render a precise role badge ("Admin" vs "Instructor") and
 * support its role-based filter pills without re-deriving the persona
 * from joins on every row.
 *
 * Strictly additive — the legacy logger
 * (App\Services\AuditLogService::log()) continues writing the existing
 * `user_type` column; new code paths populate `actor_role`, and the
 * admin read endpoint falls back to `user_type` when the column is null
 * so historical rows still render correctly.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'actor_role')) {
                $table->string('actor_role', 30)->nullable()->after('user_name');
                $table->index('actor_role');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'actor_role')) {
                $table->dropIndex(['actor_role']);
                $table->dropColumn('actor_role');
            }
        });
    }
};
