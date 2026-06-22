<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseExamQuestion extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['question'];

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Rich-question metadata (added by 2026_05_23_120100 migration).
    |--------------------------------------------------------------------------
    */
    public const TYPES = ['mcq', 'yes_no', 'open', 'reorder'];

    protected $casts = [
        'position'   => 'integer',
        'score'      => 'integer',
        'options_en' => 'array',
        'options_ar' => 'array',
    ];

    public function answers()
    {
        return $this->hasMany(CourseExamQuestionAnswer::class, 'question_id');
    }

    /** Learner answers for this question across all submissions. */
    public function userAnswers()
    {
        return $this->hasMany(UserExamAnswer::class, 'question_id');
    }

    public function exam()
    {
        return $this->belongsTo(CourseExam::class, 'course_exam_id');
    }
}
