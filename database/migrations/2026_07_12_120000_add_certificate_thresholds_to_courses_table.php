<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Configurable certificate-eligibility thresholds for the learner-facing
 * certificate status projection (On track / At risk / Blocked).
 *
 * Prior to this migration `courses` only had a binary `certificate` flag
 * (does this course offer one at all) — there was no way to express HOW a
 * learner earns it (attendance vs score vs both) or by how much. This is
 * genuinely new configuration, not a rename of something that already
 * existed — confirmed by grepping every migration/model for
 * "attendance_percent|threshold|certificate_mode" before adding this.
 *
 * Strictly additive: existing binary issuance rules in CertificateService
 * (final-exam pass / evaluation submission) are untouched. These columns
 * only feed the new CertificateProjectionService, which predicts eligibility
 * ahead of actual issuance — it never gates issuance itself.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'certificate_mode')) {
                // 'attendance' | 'score' | 'both'
                $table->string('certificate_mode', 16)->default('score')->after('certificate');
            }
            if (! Schema::hasColumn('courses', 'certificate_attendance_threshold')) {
                // Minimum % of cohort sessions the learner must attend.
                $table->unsignedTinyInteger('certificate_attendance_threshold')->nullable()->default(75)->after('certificate_mode');
            }
            if (! Schema::hasColumn('courses', 'certificate_score_threshold')) {
                // Minimum % of the final exam's total score the learner must reach.
                $table->unsignedTinyInteger('certificate_score_threshold')->nullable()->default(60)->after('certificate_attendance_threshold');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            foreach (['certificate_mode', 'certificate_attendance_threshold', 'certificate_score_threshold'] as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
