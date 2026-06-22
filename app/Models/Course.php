<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use HasFactory, HasFile, HasTranslations;

    public array $translatable = ['title', 'description', 'title_for_certificate', 'notification_text'];

    protected $guarded = ['id'];

    public function scopeActive($q)
    {
        return $q->whereActive(true);
    }

    public function getSlugAttribute()
    {
        return str_replace(' ', '-', $this->title);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function resources()
    {
        return $this->hasMany(CourseResource::class);
    }
    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    public function lectures()
    {
        return $this->hasMany(CourseLecture::class);
    }

    public function sessions()
    {
        return $this->hasMany(CourseSession::class);
    }

    public function exams()
    {
        return $this->hasMany(CourseExam::class);
    }

    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'courses_instructors', 'course_id', 'instructor_id');
    }

    public function qualificationSkills()
    {
        return $this->belongsToMany(
            QualificationSkill::class,
            'course_qualification_skills',
            'course_id',
            'qualification_skill_id',
        );
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_courses', 'course_id', 'user_id')
            ->withPivot('group_id');
    }

    public function getCourseUsers()
    {
        if ($this->for_public) {
            return User::where(function ($query) {
                $query->whereHas('exams', function ($q) {
                    $q->where('course_id', $this->id);
                })
                    ->orWhereHas('lectureProgress.lecture', function ($q) {
                        $q->where('course_id', $this->id);
                    })
                ->orWhereHas('evaluations', function ($q) {
                    $q->where('course_id', $this->id);
                });
            })
                ->join('users_courses', 'users.id', '=', 'users_courses.user_id')
                ->where('users_courses.course_id', $this->id)
                ->select('users.*');
        }
        return $this->users()->withPivot('group_id');
    }



    public function evaluations()
    {
        return $this->hasMany(UserCourseEvaluation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }


    public function usersCourses()
    {
        return $this->hasMany(UsersCourse::class);
    }

    /**
     * Resolve the *effective* status of a single cohort from its date
     * window and stored status. A stored `inactive` value is treated as
     * a manual override and always wins — that's the only way for an
     * admin to take a cohort offline outside of the calendar flow.
     *
     * Logic:
     *   - stored `inactive`             → `inactive`
     *   - today < start_date            → `scheduled` | `open_for_enrollment`
     *                                      (the admin's manual pre-start
     *                                      enrolment-window choice is kept)
     *   - start_date ≤ today ≤ end_date → `active`
     *   - today > end_date              → `completed`
     *   - no dates                      → fall back to stored status
     *
     * Kept on the Course model (rather than CourseSection) so other
     * services like AdminReportService can derive cohort status without
     * dragging in extra dependencies.
     */
    public static function deriveCohortStatus(?string $storedStatus, ?Carbon $startDate, ?Carbon $endDate): string
    {
        if ($storedStatus === 'inactive') {
            return 'inactive';
        }

        if ($startDate === null && $endDate === null) {
            // No window provided — respect whatever was persisted last.
            // Defaults to `scheduled` for newly-seeded cohorts that
            // predate the start/end columns.
            return $storedStatus ?: 'scheduled';
        }

        $today = Carbon::today();

        if ($startDate !== null && $today->lt($startDate)) {
            // Before the cohort starts the only two meaningful states are
            // the manual enrolment-window choices; `open_for_enrollment`
            // is surfaced as-is so it can drive earlier app visibility,
            // everything else collapses to `scheduled`.
            return $storedStatus === 'open_for_enrollment'
                ? 'open_for_enrollment'
                : 'scheduled';
        }

        if ($endDate !== null && $today->gt($endDate)) {
            return 'completed';
        }

        return 'active';
    }

    /**
     * Roll cohort statuses up into a single, effective course status.
     * Mirrors the `course_status` enum (`active`/`upcoming`/`inactive`)
     * so resources can drop this directly into the `status` field.
     *
     * Rules:
     *   - any cohort currently `active` → course `active`
     *   - else any cohort `scheduled`   → course `upcoming`
     *   - else cohorts exist but none active/scheduled → `inactive`
     *   - no cohorts at all             → fall back to stored `active`
     */
    public function effectiveStatus(): string
    {
        $sections = $this->relationLoaded('sections')
            ? $this->sections
            : $this->sections()->get(['id', 'course_id', 'start_date', 'end_date', 'status']);

        if ($sections->isEmpty()) {
            return $this->active ? 'active' : 'inactive';
        }

        $hasScheduled = false;
        foreach ($sections as $section) {
            $start = $section->start_date instanceof Carbon
                ? $section->start_date
                : ($section->start_date ? Carbon::parse($section->start_date) : null);
            $end   = $section->end_date instanceof Carbon
                ? $section->end_date
                : ($section->end_date ? Carbon::parse($section->end_date) : null);

            $status = static::deriveCohortStatus($section->status, $start, $end);
            if ($status === 'active') {
                return 'active';
            }
            if ($status === 'scheduled' || $status === 'open_for_enrollment') {
                $hasScheduled = true;
            }
        }

        return $hasScheduled ? 'upcoming' : 'inactive';
    }
}
