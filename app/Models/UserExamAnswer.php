<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamAnswer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'is_correct'     => 'boolean',
        'awarded_score'  => 'integer',
        'answer_payload' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Additive relationships for the 2026 admin Quiz grading workflow.
    |--------------------------------------------------------------------------
    */
    public function submission()
    {
        return $this->belongsTo(UserExam::class, 'user_exam_id');
    }

    public function question()
    {
        return $this->belongsTo(CourseExamQuestion::class, 'question_id');
    }
}
