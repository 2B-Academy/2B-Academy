<?php

namespace App\Listeners;

use App\Events\InstructorAssignedToCourse;
use App\Models\Instructor;
use App\Notifications\CourseAssignedNotification;

class NotifyInstructorCourseAssigned
{
    public function handle(InstructorAssignedToCourse $event): void
    {
        if (empty($event->newInstructorIds)) {
            return;
        }

        $instructors = Instructor::whereIn('id', $event->newInstructorIds)->get();

        foreach ($instructors as $instructor) {
            $instructor->notify(new CourseAssignedNotification($event->course));
        }
    }
}
