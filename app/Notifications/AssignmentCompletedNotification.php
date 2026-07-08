<?php

namespace App\Notifications;

use App\Models\CourseAssignment;
use Illuminate\Notifications\Notification;

class AssignmentCompletedNotification extends Notification
{
    public function __construct(
        private readonly CourseAssignment $assignment,
        private readonly string $latestStudentName,
        private readonly int $totalCompleted,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isSingle  = $this->totalCompleted <= 1;
        $bodyKey   = $isSingle ? 'assignment_completed_single_body' : 'assignment_completed_multiple_body';
        $titleEn   = $this->assignment->title;
        $titleAr   = $this->assignment->title_ar ?: $this->assignment->title;

        $params = [
            'student' => $this->latestStudentName,
            'count'   => $this->totalCompleted - 1,
        ];

        return [
            'type'     => 'assignment_completed',
            'title_en' => __('messages.notifications.assignment_completed_title', [], 'en'),
            'title_ar' => __('messages.notifications.assignment_completed_title', [], 'ar'),
            'body_en'  => __("messages.notifications.{$bodyKey}", $params + ['title' => $titleEn], 'en'),
            'body_ar'  => __("messages.notifications.{$bodyKey}", $params + ['title' => $titleAr], 'ar'),
            'meta'     => ['course_assignment_id' => $this->assignment->id, 'total_completed' => $this->totalCompleted],
        ];
    }
}
