<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseExam extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['title'];

    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function questions()
    {
        return $this->hasMany(CourseExamQuestion::class, 'course_exam_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Additive relationships for the 2026 rich-question Quiz workflow.
    | These are NEW methods only — existing behaviour is untouched.
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function richQuestions()
    {
        return $this->hasMany(CourseExamQuestion::class, 'course_exam_id')
            ->orderBy('position');
    }

    public function cohorts()
    {
        return $this->hasMany(CourseExamCohort::class, 'course_exam_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(UserExam::class, 'exam_id');
    }
}
