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

}
