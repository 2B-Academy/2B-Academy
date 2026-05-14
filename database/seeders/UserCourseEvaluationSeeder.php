<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SchemaAwareUpsert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Historic evaluation answers — these snapshots are intentionally
 * denormalised, so we keep the original Arabic copy verbatim
 * (no translation needed; the live source data already mirrors the
 * Arabic labels chosen by the trainee).
 */
class UserCourseEvaluationSeeder extends Seeder
{
    use SchemaAwareUpsert;

    public function run(): void
    {
        $this->schemaAwareUpsert('user_course_evaluations', [
            ['id' => 32, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 2, 'evaluation_category_name' => 'تقييم  المحاضر', 'evaluation_id' => 3, 'evaluation_title' => 'هل المحاضر على دراية بالمادة التعليمة التي يقوم بشرحها ؟', 'evaluation_type' => 5,  'answer' => '3',    'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 33, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 2, 'evaluation_category_name' => 'تقييم  المحاضر', 'evaluation_id' => 4, 'evaluation_title' => 'هل المحاضرشجع الحضور على الاشتراك وتبادل الاراء ؟', 'evaluation_type' => 5,  'answer' => '4',    'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 34, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 2, 'evaluation_category_name' => 'تقييم  المحاضر', 'evaluation_id' => 5, 'evaluation_title' => 'المدرب دعم المادة العلمية بتدريبات وأنشطة متنوعة وهادفة ووثيقة الصلة بموضوع التدريب ؟', 'evaluation_type' => 10, 'answer' => '7',    'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 35, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 2, 'evaluation_category_name' => 'تقييم  المحاضر', 'evaluation_id' => 6, 'evaluation_title' => 'قام المحاضر بتغطية كافة الاهداف المرجوة من التدريب ؟', 'evaluation_type' => 0,  'answer' => 'test', 'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 36, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 3, 'evaluation_category_name' => 'تقييم الكورس', 'evaluation_id' => 7, 'evaluation_title' => 'هل الماده التعليمية معدة بشكل جيد ؟', 'evaluation_type' => 5,  'answer' => '4',    'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 37, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 3, 'evaluation_category_name' => 'تقييم الكورس', 'evaluation_id' => 8, 'evaluation_title' => 'هل كان مضمون التدريب منظم وسهل المتابعه؟', 'evaluation_type' => 10, 'answer' => '7',    'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 38, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 3, 'evaluation_category_name' => 'تقييم الكورس', 'evaluation_id' => 9, 'evaluation_title' => 'اذكر/ اذكري النقاط الايجابية في التدريب وفي المحاضر ؟', 'evaluation_type' => 0,  'answer' => 'test', 'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
            ['id' => 39, 'user_id' => 1801, 'user_machine_code' => '1000', 'user_department' => null, 'course_id' => 8, 'course_name' => 'كورس الإدارة', 'instructor_id' => 1, 'instructor_name' => 'محمد سعيد', 'evaluation_category_id' => 4, 'evaluation_category_name' => 'NPS',            'evaluation_id' => 10,'evaluation_title' => 'هل ترشح حضور زملاء اخرين لنفس الكورس مع المحاضر ؟', 'evaluation_type' => 10, 'answer' => '7',    'created_at' => '2026-02-09 11:33:59', 'updated_at' => '2026-02-09 11:33:59'],
        ], ['id'], [
            'user_id', 'user_machine_code', 'user_department', 'course_id', 'course_name', 'instructor_id', 'instructor_name',
            'evaluation_category_id', 'evaluation_category_name', 'evaluation_id', 'evaluation_title', 'evaluation_type',
            'answer', 'updated_at',
        ]);
    }
}
