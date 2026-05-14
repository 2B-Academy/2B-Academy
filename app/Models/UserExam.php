<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExam extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function exam()
    {
        return $this->belongsTo(CourseExam::class, 'exam_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function answers()
    {
        return $this->hasMany(UserExamAnswer::class,'user_exam_id');
    }

}
