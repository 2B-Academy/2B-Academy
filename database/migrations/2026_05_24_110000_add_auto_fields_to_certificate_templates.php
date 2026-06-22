<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the `auto_fields` JSON column to `certificate_templates`.
 *
 * The 2026 Certificates redesign exposes a configurable list of
 * "auto-filled fields" on each template — previously hard-coded inside
 * AdminCertificateService. Persisting them on the template row keeps
 * the admin UI 100% dynamic / database-driven, in line with the project
 * rule that no list shown to the user may live in PHP arrays.
 *
 * Strictly additive — no MVC or legacy code path reads this column,
 * and the existing rows are back-filled with the canonical defaults.
 */
return new class extends Migration
{
    /** @var array<int,string> */
    private const DEFAULT_FIELDS = [
        'Learner full name',
        'Course name',
        'Completion date',
        'Instructor name',
        'Certificate ID',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('certificate_templates')) {
            return;
        }

        if (!Schema::hasColumn('certificate_templates', 'auto_fields')) {
            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->json('auto_fields')->nullable()->after('description_ar');
            });
        }

        // Back-fill: any existing row with NULL/empty auto_fields gets the
        // canonical defaults. Done with a raw update so we don't trigger
        // any model events.
        $defaultsJson = json_encode(self::DEFAULT_FIELDS, JSON_UNESCAPED_UNICODE);

        DB::table('certificate_templates')
            ->where(function ($q) {
                $q->whereNull('auto_fields')->orWhere('auto_fields', '');
            })
            ->update(['auto_fields' => $defaultsJson]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('certificate_templates')) {
            return;
        }

        if (Schema::hasColumn('certificate_templates', 'auto_fields')) {
            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->dropColumn('auto_fields');
            });
        }
    }
};
