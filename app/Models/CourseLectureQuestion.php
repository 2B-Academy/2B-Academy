<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLectureQuestion extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lecture()
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    public function answeredBy()
    {
        return $this->belongsTo(Admin::class, 'answered_by');
    }

}
