<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseAssignment extends Model
{
    use HasFactory, HasFile;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assignment()
    {
        return $this->belongsTo(CourseAssignment::class , 'course_assignment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Additive relationships for the rich-question assignment system.
    | These are NEW methods only — no existing behaviour is modified.
    |--------------------------------------------------------------------------
    */

    public function answers()
    {
        return $this->hasMany(UserCourseAssignmentAnswer::class, 'user_course_assignment_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];
}
