<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $rows = [
            [
                'id' => 3,
                'evaluation_category_id' => 2,
                'type' => 'five',
                'ar' => 'هل المحاضر على دراية بالمادة التعليمة التي يقوم بشرحها ؟',
                'en' => 'Is the instructor knowledgeable about the material being taught?',
                'is_required' => 1,
                'created_at' => '2026-01-31 12:22:51',
            ],
            [
                'id' => 4,
                'evaluation_category_id' => 2,
                'type' => 'five',
                'ar' => 'هل المحاضرشجع الحضور على الاشتراك وتبادل الاراء ؟',
                'en' => 'Did the instructor encourage attendees to participate and exchange ideas?',
                'is_required' => 1,
                'created_at' => '2026-01-31 12:23:04',
            ],
            [
                'id' => 5,
                'evaluation_category_id' => 2,
                'type' => 'ten',
                'ar' => 'المدرب دعم المادة العلمية بتدريبات وأنشطة متنوعة وهادفة ووثيقة الصلة بموضوع التدريب ؟',
                'en' => 'Did the trainer support the material with varied, purposeful and relevant activities?',
                'is_required' => 1,
                'created_at' => '2026-01-31 12:23:17',
            ],
            [
                'id' => 6,
                'evaluation_category_id' => 2,
                'type' => 'text',
                'ar' => 'قام المحاضر بتغطية كافة الاهداف المرجوة من التدريب ؟',
                'en' => 'Did the instructor cover all of the intended training objectives?',
                'is_required' => 1,
                'created_at' => '2026-01-31 12:27:58',
            ],
            [
                'id' => 7,
                'evaluation_category_id' => 3,
                'type' => 'five',
                'ar' => 'هل الماده التعليمية معدة بشكل جيد ؟',
                'en' => 'Was the training material well prepared?',
                'is_required' => 1,
                'created_at' => '2026-02-03 08:54:37',
            ],
            [
                'id' => 8,
                'evaluation_category_id' => 3,
                'type' => 'ten',
                'ar' => 'هل كان مضمون التدريب منظم وسهل المتابعه؟',
                'en' => 'Was the training content organised and easy to follow?',
                'is_required' => 1,
                'created_at' => '2026-02-03 08:54:55',
            ],
            [
                'id' => 9,
                'evaluation_category_id' => 3,
                'type' => 'text',
                'ar' => 'اذكر/ اذكري النقاط الايجابية في التدريب وفي المحاضر ؟',
                'en' => 'List the positive points about the training and the instructor.',
                'is_required' => 1,
                'created_at' => '2026-02-03 08:55:12',
            ],
            [
                'id' => 10,
                'evaluation_category_id' => 4,
                'type' => 'ten',
                'ar' => 'هل ترشح حضور زملاء اخرين لنفس الكورس مع المحاضر ؟',
                'en' => 'Would you recommend other colleagues attend the same course with this instructor?',
                'is_required' => 1,
                'created_at' => '2026-02-09 11:27:46',
            ],
        ];

        $payload = array_map(static fn (array $row) => [
            'id' => $row['id'],
            'evaluation_category_id' => $row['evaluation_category_id'],
            'type' => $row['type'],
            'title' => json_encode(['ar' => $row['ar'], 'en' => $row['en']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'title_backup' => $row['ar'],
            'is_required' => $row['is_required'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['created_at'],
        ], $rows);

        $this->schemaAwareUpsert('evaluations', $payload, ['id'], ['evaluation_category_id', 'type', 'title', 'title_backup', 'is_required', 'updated_at']);
    }
}
