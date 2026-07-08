<?php

namespace App\Events;

use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Foundation\Events\Dispatchable;

class CourseRatingSubmitted
{
    use Dispatchable;

    public function __construct(
        public readonly Course $course,
        public readonly CourseRating $rating,
    ) {}
}
