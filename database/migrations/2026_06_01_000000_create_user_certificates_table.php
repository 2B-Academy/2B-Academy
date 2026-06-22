<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * First-class Certificate entity (2026 redesign).
 *
 * Certificates used to be a *computed view* over `user_exams` /
 * `user_course_evaluations`, surfaced to clients as compound ids
 * (`exam:123` / `evaluation:456`). A certificate is now a standalone
 * business document with its own identity and lifecycle:
 *
 *   - own primary key (`id`) + public `uuid` + human `certificate_number`
 *   - `status` (active | revoked) with an audit trail
 *     (issued_at/by, revoked_at/by) and a free-form `metadata` snapshot
 *   - `source_type` / `source_id` preserve provenance (which exam or
 *     evaluation triggered issuance) for traceability ONLY — clients
 *     never see them.
 *
 * Business rule: one learner + one course = one ACTIVE certificate.
 * Enforced in CertificateService; a composite index supports the lookup.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_certificates')) {
            return;
        }

        Schema::create('user_certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();

            // Provenance — never exposed through any API. Audit/traceability only.
            $table->string('source_type', 20)->comment('exam | evaluation');
            $table->unsignedBigInteger('source_id')->nullable();

            $table->string('certificate_number', 40)->unique();

            $table->string('status', 20)->default('active')->comment('active | revoked');

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            $table->unsignedBigInteger('issued_by')->nullable();
            $table->unsignedBigInteger('revoked_by')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'course_id']);
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_certificates');
    }
};
