<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the new `certificate_templates` table that powers the 2026 admin
 * Certificates redesign.
 *
 * The legacy hard-coded template at
 *   public/front/assets/images/thumbs/certificate-two-img.jpg
 * is left in place — `HelperTrait::generateCertificate()` and every
 * learner-facing certificate flow continue to work exactly as before.
 * The new admin UI uploads a template into this table, which the new
 * AdminCertificateService consumes. The legacy generator continues to
 * fall back to the hard-coded image when no active row exists in this
 * table, so the migration is strictly additive.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('certificate_templates')) {
            $this->seedDefaultRow();
            return;
        }

        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('name_ar', 191)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('description_ar', 500)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('original_filename', 191)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('uploaded_by');
        });

        $this->seedDefaultRow();
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }

    /** Seed the single default "NAS Standard Certificate" placeholder row. */
    private function seedDefaultRow(): void
    {
        $exists = DB::table('certificate_templates')->where('is_active', true)->exists();
        if ($exists) {
            return;
        }

        $now = Carbon::now();
        DB::table('certificate_templates')->insert([
            'name'           => 'NAS Standard Certificate',
            'name_ar'        => 'شهادة NAS القياسية',
            'description'    => 'Default template — Auto-fills learner name, course, date, instructor',
            'description_ar' => 'القالب الافتراضي — يملأ تلقائيًا اسم المتدرب والدورة والتاريخ والمدرب',
            'file_path'      => null,
            'original_filename' => null,
            'mime_type'      => null,
            'file_size'      => null,
            'uploaded_by'    => null,
            'is_active'      => true,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
    }
};
