<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Single question on a course_assignment. See migration for `type` semantics.
 */
class CourseAssignmentQuestion extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'position'   => 'integer',
        'score'      => 'integer',
        'options_en' => 'array',
        'options_ar' => 'array',
    ];

    public const TYPE_MCQ     = 'mcq';
    public const TYPE_YES_NO  = 'yes_no';
    public const TYPE_OPEN    = 'open';
    public const TYPE_REORDER = 'reorder';

    public const TYPES = [self::TYPE_MCQ, self::TYPE_YES_NO, self::TYPE_OPEN, self::TYPE_REORDER];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CourseAssignment::class, 'course_assignment_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UserCourseAssignmentAnswer::class, 'course_assignment_question_id');
    }
}
