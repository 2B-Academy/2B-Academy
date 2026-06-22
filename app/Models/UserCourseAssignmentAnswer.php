<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Individual question answer captured for a user's submission. `answer` is a
 * JSON column whose shape depends on the parent question's `type` — see the
 * migration comment for the contract.
 */
class UserCourseAssignmentAnswer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'answer'        => 'array',
        'awarded_score' => 'integer',
        'is_correct'    => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(UserCourseAssignment::class, 'user_course_assignment_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(CourseAssignmentQuestion::class, 'course_assignment_question_id');
    }
}
