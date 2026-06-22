<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Models\Course;
use App\Models\CourseRating;
use App\Models\User;

/**
 * Submit / update the user's cohort feedback (Figma 543:41637 →
 * 543:41844). Uses the existing `course_ratings` table (one row per
 * user per course) — re-submitting overwrites the previous rating.
 *
 * The negative-rating-requires-comment policy is enforced upstream
 * by SubmitRatingRequest using the `rating_comment_required_at_or_below`
 * platform setting, so the service can assume a valid payload.
 */
final class MobileRatingService
{
    public function submit(User $user, Course $course, int $rating, ?string $comment): CourseRating
    {
        return CourseRating::query()->updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'rating'  => $rating,
                'comment' => $comment !== null && trim($comment) !== '' ? trim($comment) : null,
            ],
        );
    }
}
