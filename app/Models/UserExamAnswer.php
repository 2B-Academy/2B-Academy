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

    /**
     * NOTE: intentionally NOT named `question()`. `user_exam_answers` also
     * has a legacy `question` STRING column (the MCQ-only flow's plain-text
     * prompt snapshot) — Eloquent's attribute resolution always prefers a
     * real column over a same-named relationship method
     * (see HasAttributes::getAttribute — `hasAttribute($key)` is checked
     * before relations, with no fallback even when the relation is eager
     * loaded), so `$answer->question` can NEVER resolve to this relation.
     * That silent collision is what `examQuestion()` avoids.
     */
    public function examQuestion()
    {
        return $this->belongsTo(CourseExamQuestion::class, 'question_id');
    }
}
