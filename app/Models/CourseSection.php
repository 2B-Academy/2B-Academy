<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseSection extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name'];

    protected $guarded = ['id'];

    /**
     * Casts so the new cohort date columns hydrate as Carbon instances —
     * lets the resource call `->format('Y-m-d')` without null gymnastics.
     */
    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'capacity'         => 'integer',
        // Average session length in HOURS (decimal). Drives the live
        // attendance-window length when a session is started for this cohort.
        'avg_session_time' => 'decimal:2',
    ];

    public function lectures()
    {
        return $this->hasMany(CourseLecture::class, 'section_id');
    }

    public function sessions()
    {
        return $this->hasMany(CourseSession::class, 'section_id');
    }

    public function exams()
    {
        return $this->hasMany(CourseExam::class, 'section_id');
    }

    /**
     * Learners enrolled into this cohort (= section). `users_courses.group_id`
     * is the FK back to `course_sections.id`. Wired so the repository can
     * `withCount(['enrollments as enrolled_count'])` in a single query.
     */
    public function enrollments()
    {
        return $this->hasMany(UsersCourse::class, 'group_id');
    }

}
