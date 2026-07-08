<?php

namespace App\Observers;

use App\Events\CourseRatingSubmitted;
use App\Models\CourseRating;

/**
 * Fire CourseRatingSubmitted from the model layer so the abnormal-rating
 * check runs no matter which path wrote the rating — the learner API
 * (CourseRatingService), the mobile app (MobileRatingService) and the
 * public website (FrontControllers\CourseController) all persist through
 * this model. Keeping the trigger here (rather than in one service) is the
 * single choke point that guarantees "whenever the course rating changes".
 */
class CourseRatingObserver
{
    public function created(CourseRating $rating): void
    {
        $this->dispatch($rating);
    }

    public function updated(CourseRating $rating): void
    {
        // Only re-check when the score actually moved — a comment-only edit
        // or a no-op save shouldn't re-notify.
        if ($rating->wasChanged('rating')) {
            $this->dispatch($rating);
        }
    }

    private function dispatch(CourseRating $rating): void
    {
        $course = $rating->course;
        if ($course !== null) {
            event(new CourseRatingSubmitted($course, $rating));
        }
    }
}
