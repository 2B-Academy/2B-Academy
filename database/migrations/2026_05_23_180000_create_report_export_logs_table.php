<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive migration backing the 2026 Reports redesign.
 *
 * Tracks when each report type was last exported so the admin overview can
 * surface "Last generated" dates for each card. This table is purely
 * additive — no existing tables are touched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_export_logs', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 64)->index();
            $table->string('format', 8); // 'csv' | 'xlsx'
            $table->unsignedBigInteger('exported_by_admin_id')->nullable();
            $table->timestamp('exported_at')->useCurrent();
            $table->timestamps();

            $table->index(['report_type', 'exported_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_export_logs');
    }
};
