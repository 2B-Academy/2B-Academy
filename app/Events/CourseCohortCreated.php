<?php

namespace App\Events;

use App\Models\CourseSection;
use Illuminate\Foundation\Events\Dispatchable;

class CourseCohortCreated
{
    use Dispatchable;

    public function __construct(
        public readonly CourseSection $section,
    ) {}
}
