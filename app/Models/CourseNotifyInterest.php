<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A learner's recorded intent to be notified when `course`'s next cohort
 * opens for enrolment (GAP 4 — see the migration docblock). Storage only:
 * nothing currently reads this table to actually send a notification —
 * that admin-side trigger is a separate follow-up.
 */
class CourseNotifyInterest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
