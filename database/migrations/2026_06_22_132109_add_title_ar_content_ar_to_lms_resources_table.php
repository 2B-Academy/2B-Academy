<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_resources', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->longText('content_ar')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('lms_resources', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'content_ar']);
        });
    }
};
