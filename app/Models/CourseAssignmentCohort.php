<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pivot between a course_assignment and a course_session (cohort).
 */
class CourseAssignmentCohort extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CourseAssignment::class, 'course_assignment_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }
}
