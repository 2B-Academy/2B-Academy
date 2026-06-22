<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive columns that back the 2026 admin Users redesign.
 *
 *   - name_en / name_ar  → bilingual names captured by the "Add User" modal.
 *     The legacy `name` column is preserved untouched as the single-language
 *     fallback so existing flows (HR webhook sync, learner self-service)
 *     keep working without changes.
 *   - status             → 'active' | 'inactive' | 'deactivated'. The legacy
 *     code never read this column, so introducing it is purely additive.
 *   - last_active_at     → surfaced by the new admin list ("Last Active"
 *     column) and profile drawer. Nullable, kept in sync by future audit
 *     hooks or login events.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)
                    ->default('active')
                    ->nullable()
                    ->after('learner_type');
            }
            if (! Schema::hasColumn('users', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['name_en', 'name_ar', 'status', 'last_active_at'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
