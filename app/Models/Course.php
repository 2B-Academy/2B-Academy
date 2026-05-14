<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
