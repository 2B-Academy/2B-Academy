<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssignment extends Model
{
    use HasFactory, HasFile;
    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
    ];


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_course_assignments', 'course_assignment_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Additive relationships for the rich-question assignment system.
    | These are NEW methods only — no existing behaviour is modified.
    |--------------------------------------------------------------------------
    */

    public function questions()
    {
        return $this->hasMany(CourseAssignmentQuestion::class, 'course_assignment_id')
            ->orderBy('position');
    }

    public function cohorts()
    {
        return $this->hasMany(CourseAssignmentCohort::class, 'course_assignment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(UserCourseAssignment::class, 'course_assignment_id');
    }
}
