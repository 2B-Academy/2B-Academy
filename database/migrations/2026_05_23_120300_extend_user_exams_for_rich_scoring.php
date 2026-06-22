<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add submission tracking + per-question grading columns to `user_exams`.
     *
     * Additive. The existing `user_degree` and `status` ("success"/"fail")
     * columns remain so the legacy QuizController continues to work.
     */
    public function up(): void
    {
        if (! Schema::hasTable('user_exams')) {
            return;
        }

        Schema::table('user_exams', function (Blueprint $table) {
            if (! Schema::hasColumn('user_exams', 'total_score')) {
                $table->integer('total_score')->nullable()->after('user_degree');
            }
            if (! Schema::hasColumn('user_exams', 'max_score')) {
                $table->integer('max_score')->default(0)->after('total_score');
            }
            if (! Schema::hasColumn('user_exams', 'submission_status')) {
                $table->string('submission_status', 16)->default('pending')->after('status');
            }
            if (! Schema::hasColumn('user_exams', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('submission_status');
            }
            if (! Schema::hasColumn('user_exams', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            }
            if (! Schema::hasColumn('user_exams', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_exams')) {
            return;
        }

        Schema::table('user_exams', function (Blueprint $table) {
            foreach ([
                'total_score',
                'max_score',
                'submission_status',
                'submitted_at',
                'reviewed_at',
                'reviewed_by',
            ] as $col) {
                if (Schema::hasColumn('user_exams', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
