<?php

namespace App\Listeners;

use App\Events\AssignmentSubmitted;
use App\Notifications\AssignmentCompletedNotification;

/**
 * Every submission recomputes the total-completed count for the assignment
 * so the notification text always reflects the current state ("John Doe has
 * completed..." vs "John Doe and 4 others have completed...").
 */
class NotifyInstructorsAssignmentCompleted
{
    public function handle(AssignmentSubmitted $event): void
    {
        $submission = $event->submission;
        $assignment = $submission->assignment;

        if (! $assignment) {
            return;
        }

        $course = $assignment->course;
        if (! $course) {
            return;
        }

        $studentName = $submission->user?->name ?? __('messages.notifications.assignment_completed_title');
        $total       = $assignment->submissions()->count();

        foreach ($course->instructors as $instructor) {
            $instructor->notify(new AssignmentCompletedNotification($assignment, $studentName, $total));
        }
    }
}
