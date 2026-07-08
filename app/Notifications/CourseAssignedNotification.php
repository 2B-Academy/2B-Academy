<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Notifications\Notification;

class CourseAssignedNotification extends Notification
{
    public function __construct(
        private readonly Course $course,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'     => 'course_assigned',
            'title_en' => __('messages.notifications.course_assigned_title', [], 'en'),
            'title_ar' => __('messages.notifications.course_assigned_title', [], 'ar'),
            'body_en'  => __('messages.notifications.course_assigned_body', ['course' => $this->course->getTranslation('title', 'en')], 'en'),
            'body_ar'  => __('messages.notifications.course_assigned_body', ['course' => $this->course->getTranslation('title', 'ar')], 'ar'),
            'meta'     => ['course_id' => $this->course->id],
        ];
    }
}
