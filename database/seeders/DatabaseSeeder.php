<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Master orchestrator.
 *
 * Seeders are called in a strict dependency order:
 *  1. Authorisation primitives (permissions / roles / pivots / admins)
 *  2. CMS / settings (no foreign keys, safe to load early)
 *  3. Catalogue tables (categories, instructors)
 *  4. Course tree (course → sections → assignments → sessions → exams → questions → answers)
 *  5. Forms tree (forms → questions → answers)
 *  6. Evaluations
 *  7. Users (bulk fixture)
 *  8. User activity (enrolments, assignments, evaluations, exam answers, forms, attendance, logs)
 *
 * Translatable columns are written as JSON objects shaped as
 * `{ "ar": "...", "en": "..." }`, matching the schema introduced by
 * the 2026-05-14 localization migrations and the spatie/laravel-translatable
 * configuration on each Eloquent model.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Authorisation primitives.
        $this->call(PermissionTableSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RoleHasPermissionSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(AdminLoginLogSeeder::class);

        // CMS / settings.
        $this->call(SettingSeeder::class);
        $this->call(MobileSettingSeeder::class);
        $this->call(PlatformConfigSeeder::class);
        $this->call(AboutSeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(TestimonialSeeder::class);
        $this->call(ContactSeeder::class);
        $this->call(PublicNotificationSeeder::class);
        $this->call(PublicNotificationUserSeeder::class);

        // Catalogue.
        $this->call(CategorySeeder::class);
        $this->call(InstructorSeeder::class);
        // JobTitleSeeder is intentionally NOT called here: it projects
        // its rows from `users.department_name` and so must wait for
        // UserSeeder. See the call further down, right after UserSeeder.

        // Course tree.
        $this->call(CourseSeeder::class);
        $this->call(CoursesInstructorSeeder::class);
        $this->call(CourseSectionSeeder::class);
        $this->call(CourseAssignmentSeeder::class);
        $this->call(CourseSessionSeeder::class);
        $this->call(CourseExamSeeder::class);
        $this->call(CourseExamQuestionSeeder::class);
        $this->call(CourseExamQuestionAnswerSeeder::class);

        // Forms tree.
        $this->call(FormSeeder::class);
        $this->call(FormQuestionSeeder::class);
        $this->call(FormQuestionAnswerSeeder::class);

        // Evaluations.
        $this->call(EvaluationCategorySeeder::class);
        $this->call(EvaluationSeeder::class);

        // End-user bulk fixture.
        $this->call(UserSeeder::class);

        // Catalogue rows derived from the HR roster (must run after UserSeeder).
        $this->call(JobTitleSeeder::class);

        // User activity.
        $this->call(UsersCourseSeeder::class);
        $this->call(UserCourseAssignmentSeeder::class);
        $this->call(UserCourseEvaluationSeeder::class);
        $this->call(UserExamSeeder::class);
        $this->call(UserExamAnswerSeeder::class);
        $this->call(UserFormSeeder::class);
        $this->call(UserFormAnswerSeeder::class);
        $this->call(AttendanceSeeder::class);
        $this->call(AttendanceLogSeeder::class);
    }
}
