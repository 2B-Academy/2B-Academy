<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bilingual bullet lists shown on the course Overview tab and edited in
     * the Add/Edit Course dialog. Each column holds a `{ "en": [...], "ar":
     * [...] }` JSON object (arrays of short strings) — cast to `array` on the
     * Course model. Nullable so existing courses stay valid and the fields
     * remain optional.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'what_students_will_learn')) {
                $table->json('what_students_will_learn')->nullable()->after('description');
            }
            if (! Schema::hasColumn('courses', 'requirements')) {
                $table->json('requirements')->nullable()->after('what_students_will_learn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['what_students_will_learn', 'requirements']);
        });
    }
};
