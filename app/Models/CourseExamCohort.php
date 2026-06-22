<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Pivot model linking a rich Quiz (course_exams) to a specific cohort
 * (course_sessions). Used only when CourseExam::cohort_scope === 'specific'.
 */
class CourseExamCohort extends Model
{
    use HasFactory;

    protected $table = 'course_exam_cohorts';

    protected $guarded = ['id'];

    public function exam()
    {
        return $this->belongsTo(CourseExam::class, 'course_exam_id');
    }

    public function session()
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }
}
