<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * After the 2026_05_14_* localization migrations, every translatable text
 * column was renamed to "*_backup" and left in place as a safety net.
 * Those columns are NOT NULL with no default, so any new INSERT now fails
 * with "Field 'title_backup' doesn't have a default value".
 *
 * Translation data has already been copied into the new JSON columns, so
 * we drop every backup column. Uses Schema::hasColumn to stay idempotent
 * across environments where some/all backups may already be gone.
 */
return new class extends Migration
{
    /** @var array<string, string[]> */
    private array $map = [
        'courses' => [
            'title_backup',
            'description_backup',
            'title_for_certificate_backup',
            'notification_text_backup',
        ],
        'categories'                  => ['name_backup'],
        'instructors'                 => ['name_backup', 'bio_backup', 'job_title_backup'],
        'course_sections'             => ['name_backup'],
        'course_lectures'             => ['title_backup'],
        'course_exams'                => ['title_backup'],
        'course_exam_questions'       => ['question_backup'],
        'course_exam_question_answers'=> ['answer_backup'],
        'evaluation_categories'       => ['name_backup'],
        'evaluations'                 => ['title_backup'],
        'forms'                       => ['title_backup'],
        'form_questions'              => ['question_backup'],
        'form_question_answers'       => ['answer_backup'],
        'public_notifications'        => ['title_backup', 'body_backup'],
    ];

    public function up(): void
    {
        foreach ($this->map as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $present = array_values(array_filter(
                $columns,
                fn (string $col) => Schema::hasColumn($table, $col),
            ));

            if ($present === []) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($present) {
                $blueprint->dropColumn($present);
            });
        }
    }

    public function down(): void
    {
        // No-op: backup columns are intentionally not recreated.
    }
};
