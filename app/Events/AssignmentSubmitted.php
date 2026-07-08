<?php

namespace App\Events;

use App\Models\UserCourseAssignment;
use Illuminate\Foundation\Events\Dispatchable;

class AssignmentSubmitted
{
    use Dispatchable;

    public function __construct(
        public readonly UserCourseAssignment $submission,
    ) {}
}
