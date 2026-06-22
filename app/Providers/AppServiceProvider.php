<?php

namespace App\Providers;

use App\Repositories\Contracts\AboutRepositoryInterface;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use App\Repositories\Contracts\CourseAssignmentRepositoryInterface;
use App\Repositories\Contracts\CourseExamRepositoryInterface;
use App\Repositories\Contracts\CourseLectureRepositoryInterface;
use App\Repositories\Contracts\CourseLectureQuestionRepositoryInterface;
use App\Repositories\Contracts\CourseRatingRepositoryInterface;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\CourseSectionRepositoryInterface;
use App\Repositories\Contracts\CourseSessionRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\EvaluationCategoryRepositoryInterface;
use App\Repositories\Contracts\EvaluationReportRepositoryInterface;
use App\Repositories\Contracts\EvaluationRepositoryInterface;
use App\Repositories\Contracts\FormRepositoryInterface;
use App\Repositories\Contracts\InstructorRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\OnlineEnrollmentRepositoryInterface;
use App\Repositories\Contracts\QualificationSkillRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Contracts\UserCertificateRepositoryInterface;
use App\Repositories\Contracts\UserEnrollmentRepositoryInterface;
use App\Repositories\Contracts\UserProgressRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
// Mobile (NAS-LMS Mobile) repository contracts ────────────────────────
use App\Repositories\Contracts\Mobile\AcademyRepositoryInterface;
use App\Repositories\Contracts\Mobile\MobileAttendanceRepositoryInterface;
use App\Repositories\Contracts\Mobile\MobileCertificateRepositoryInterface;
use App\Repositories\Contracts\Mobile\MobileEnrolmentRepositoryInterface;
use App\Repositories\Contracts\Mobile\MyLearningRepositoryInterface;
use App\Repositories\Eloquents\Mobile\AcademyRepository as MobileAcademyRepository;
use App\Repositories\Eloquents\Mobile\MobileAttendanceRepository;
use App\Repositories\Eloquents\Mobile\MobileCertificateRepository;
use App\Repositories\Eloquents\Mobile\MobileEnrolmentRepository;
use App\Repositories\Eloquents\Mobile\MyLearningRepository;
use App\Repositories\Eloquents\AboutRepository;
use App\Repositories\Eloquents\JobTitleRepository;
use App\Repositories\Eloquents\AdminRepository;
use App\Repositories\Eloquents\ArticleRepository;
use App\Repositories\Eloquents\AttendanceRepository;
use App\Repositories\Eloquents\CategoryRepository;
use App\Repositories\Eloquents\CertificateRepository;
use App\Repositories\Eloquents\CourseAssignmentRepository;
use App\Repositories\Eloquents\CourseExamRepository;
use App\Repositories\Eloquents\CourseLectureRepository;
use App\Repositories\Eloquents\CourseLectureQuestionRepository;
use App\Repositories\Eloquents\CourseRatingRepository;
use App\Repositories\Eloquents\CourseRepository;
use App\Repositories\Eloquents\CourseSectionRepository;
use App\Repositories\Eloquents\CourseSessionRepository;
use App\Repositories\Eloquents\DashboardRepository;
use App\Repositories\Eloquents\EvaluationCategoryRepository;
use App\Repositories\Eloquents\EvaluationReportRepository;
use App\Repositories\Eloquents\EvaluationRepository;
use App\Repositories\Eloquents\FormRepository;
use App\Repositories\Eloquents\InstructorRepository;
use App\Repositories\Eloquents\NotificationRepository;
use App\Repositories\Eloquents\OnlineEnrollmentRepository;
use App\Repositories\Eloquents\QualificationSkillRepository;
use App\Repositories\Eloquents\RoleRepository;
use App\Repositories\Eloquents\SettingRepository;
use App\Repositories\Eloquents\TestimonialRepository;
use App\Repositories\Eloquents\UserCertificateRepository;
use App\Repositories\Eloquents\UserEnrollmentRepository;
use App\Repositories\Eloquents\UserProgressRepository;
use App\Repositories\Eloquents\UserRepository;
use App\Models\Category;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings — interface → Eloquent implementation
        $this->app->bind(CategoryRepositoryInterface::class,           CategoryRepository::class);
        $this->app->bind(CourseRepositoryInterface::class,             CourseRepository::class);
        $this->app->bind(CourseSectionRepositoryInterface::class,      CourseSectionRepository::class);
        $this->app->bind(CourseExamRepositoryInterface::class,         CourseExamRepository::class);
        $this->app->bind(UserRepositoryInterface::class,               UserRepository::class);
        $this->app->bind(InstructorRepositoryInterface::class,         InstructorRepository::class);
        $this->app->bind(EvaluationCategoryRepositoryInterface::class, EvaluationCategoryRepository::class);
        $this->app->bind(EvaluationRepositoryInterface::class,         EvaluationRepository::class);

        // New repository bindings
        $this->app->bind(ArticleRepositoryInterface::class,            ArticleRepository::class);
        $this->app->bind(AdminRepositoryInterface::class,              AdminRepository::class);
        $this->app->bind(AboutRepositoryInterface::class,              AboutRepository::class);
        $this->app->bind(TestimonialRepositoryInterface::class,        TestimonialRepository::class);
        $this->app->bind(CourseSessionRepositoryInterface::class,      CourseSessionRepository::class);
        $this->app->bind(CourseAssignmentRepositoryInterface::class,   CourseAssignmentRepository::class);
        $this->app->bind(CourseLectureRepositoryInterface::class,      CourseLectureRepository::class);
        $this->app->bind(FormRepositoryInterface::class,               FormRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class,       NotificationRepository::class);
        $this->app->bind(CertificateRepositoryInterface::class,        CertificateRepository::class);
        $this->app->bind(UserCertificateRepositoryInterface::class,    UserCertificateRepository::class);
        $this->app->bind(UserEnrollmentRepositoryInterface::class,     UserEnrollmentRepository::class);
        $this->app->bind(UserProgressRepositoryInterface::class,       UserProgressRepository::class);
        $this->app->bind(RoleRepositoryInterface::class,               RoleRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class,          DashboardRepository::class);
        $this->app->bind(EvaluationReportRepositoryInterface::class,   EvaluationReportRepository::class);

        // New feature repository bindings
        $this->app->bind(CourseRatingRepositoryInterface::class,        CourseRatingRepository::class);
        $this->app->bind(CourseLectureQuestionRepositoryInterface::class, CourseLectureQuestionRepository::class);
        $this->app->bind(SettingRepositoryInterface::class,             SettingRepository::class);
        $this->app->bind(OnlineEnrollmentRepositoryInterface::class,    OnlineEnrollmentRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class,          AttendanceRepository::class);
        $this->app->bind(QualificationSkillRepositoryInterface::class,  QualificationSkillRepository::class);
        $this->app->bind(JobTitleRepositoryInterface::class,            JobTitleRepository::class);

        // ─── Mobile (NAS-LMS Mobile, S-01 → S-07) ─────────────────────
        // Bind every mobile repository contract to its Eloquent
        // implementation. The mobile services / controllers depend on
        // these contracts only — the concrete classes never leak past
        // the container.
        $this->app->bind(AcademyRepositoryInterface::class,             MobileAcademyRepository::class);
        $this->app->bind(MyLearningRepositoryInterface::class,          MyLearningRepository::class);
        $this->app->bind(MobileAttendanceRepositoryInterface::class,    MobileAttendanceRepository::class);
        $this->app->bind(MobileEnrolmentRepositoryInterface::class,     MobileEnrolmentRepository::class);
        $this->app->bind(MobileCertificateRepositoryInterface::class,   MobileCertificateRepository::class);
    }

    public function boot(): void
    {
        if (request()->isSecure()) {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        // Both the admin dashboard and front layouts ship Bootstrap 5,
        // so render the paginator with the matching markup instead of
        // Laravel 11's Tailwind default.
        Paginator::useBootstrapFive();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Event::listen(
            Registered::class,
            SendEmailVerificationNotification::class,
        );

        // Keep the Job Titles catalogue in lock-step with the HR roster
        // (users.department_name is the source of truth — see
        // App\Services\JobTitleSyncService for the full rationale).
        User::observe(UserObserver::class);

        $this->shareGlobalFrontData();
    }

    /**
     * Share globally-required data with every front-facing view.
     *
     * The header / footer / homepage / course-listing partials all
     * reference `$settings` and `$front_categories` directly; instead
     * of hydrating them in each controller we register a single view
     * composer scoped to the `front.*` namespace and cache the lookups
     * to avoid extra queries per request.
     */
    private function shareGlobalFrontData(): void
    {
        $targets = ['front.*', 'front.layouts.*', 'front.includes.*', 'front.auth.*', 'front.courses.*'];

        View::composer($targets, function ($view) {
            $view->with('settings', $this->loadSettingsMap());
            $view->with('front_categories', $this->loadFrontCategories());
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function loadSettingsMap(): array
    {
        return Cache::remember('cms.settings.map', now()->addMinutes(10), function () {
            try {
                if (! Schema::hasTable('settings')) {
                    return [];
                }

                return DB::table('settings')
                    ->pluck('value', 'key')
                    ->all();
            } catch (Throwable) {
                return [];
            }
        });
    }

    private function loadFrontCategories()
    {
        try {
            if (! Schema::hasTable('categories')) {
                return collect();
            }

            return Cache::remember('cms.front_categories', now()->addMinutes(10), function () {
                return Category::active()
                    ->withCount('courses')
                    ->orderBy('id')
                    ->get();
            });
        } catch (Throwable) {
            return collect();
        }
    }
}
