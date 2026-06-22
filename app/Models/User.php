<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'users_courses', 'user_id', 'course_id')
            ->orWhere('for_public', true);
    }


    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }

    public function lectureQuestions()
    {
        return $this->hasMany(CourseLectureQuestion::class);
    }


    public function exams()
    {
        return $this->hasMany(UserExam::class);
    }

    public function assignments()
    {
        return $this->belongsToMany(CourseAssignment::class, 'user_course_assignments', 'user_id', 'course_assignment_id');
    }


    public function lectureProgress()
    {
        return $this->hasMany(UserLectureProgress::class, 'user_id');
    }

    public function evaluations()
    {
        return $this->hasMany(UserCourseEvaluation::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    /**
     * The HR-sourced job title held by this learner. Nullable — a user
     * isn't required to have a title (e.g. external learners), and the
     * FK is set to `null` if the catalogue row is deleted (see the
     * `2026_05_25_140000_add_job_title_id_to_users_table` migration).
     */
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }
}
