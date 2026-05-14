<?php

namespace App\Providers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CourseExamRepositoryInterface;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Repositories\Contracts\CourseSectionRepositoryInterface;
use App\Repositories\Contracts\EvaluationCategoryRepositoryInterface;
use App\Repositories\Contracts\EvaluationRepositoryInterface;
use App\Repositories\Contracts\InstructorRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquents\CategoryRepository;
use App\Repositories\Eloquents\CourseExamRepository;
use App\Repositories\Eloquents\CourseRepository;
use App\Repositories\Eloquents\CourseSectionRepository;
use App\Repositories\Eloquents\EvaluationCategoryRepository;
use App\Repositories\Eloquents\EvaluationRepository;
use App\Repositories\Eloquents\InstructorRepository;
use App\Models\Category;
use App\Repositories\Eloquents\UserRepository;
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
        $this->app->bind(CategoryRepositoryInterface::class,          CategoryRepository::class);
        $this->app->bind(CourseRepositoryInterface::class,            CourseRepository::class);
        $this->app->bind(CourseSectionRepositoryInterface::class,     CourseSectionRepository::class);
        $this->app->bind(CourseExamRepositoryInterface::class,        CourseExamRepository::class);
        $this->app->bind(UserRepositoryInterface::class,              UserRepository::class);
        $this->app->bind(InstructorRepositoryInterface::class,        InstructorRepository::class);
        $this->app->bind(EvaluationCategoryRepositoryInterface::class, EvaluationCategoryRepository::class);
        $this->app->bind(EvaluationRepositoryInterface::class,        EvaluationRepository::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
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
