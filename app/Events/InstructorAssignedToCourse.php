<?php

namespace App\Events;

use App\Models\Course;
use Illuminate\Foundation\Events\Dispatchable;

class InstructorAssignedToCourse
{
    use Dispatchable;

    /**
     * @param  array<int, int>  $newInstructorIds  Instructor PKs newly attached to the course
     *                                              (excludes instructors that were already on it).
     */
    public function __construct(
        public readonly Course $course,
        public readonly array $newInstructorIds,
    ) {}
}
