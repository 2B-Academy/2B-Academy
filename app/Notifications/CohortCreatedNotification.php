<?php

namespace App\Notifications;

use App\Models\CourseSection;
use Illuminate\Notifications\Notification;

class CohortCreatedNotification extends Notification
{
    public function __construct(
        private readonly CourseSection $section,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $course = $this->section->course;

        $paramsEn = [
            'cohort' => $this->section->getTranslation('name', 'en'),
            'course' => $course?->getTranslation('title', 'en'),
        ];
        $paramsAr = [
            'cohort' => $this->section->getTranslation('name', 'ar') ?: $this->section->getTranslation('name', 'en'),
            'course' => $course?->getTranslation('title', 'ar') ?: $course?->getTranslation('title', 'en'),
        ];

        return [
            'type'     => 'cohort_created',
            'title_en' => __('messages.notifications.cohort_created_title', [], 'en'),
            'title_ar' => __('messages.notifications.cohort_created_title', [], 'ar'),
            'body_en'  => __('messages.notifications.cohort_created_body', $paramsEn, 'en'),
            'body_ar'  => __('messages.notifications.cohort_created_body', $paramsAr, 'ar'),
            'meta'     => ['course_section_id' => $this->section->id, 'course_id' => $this->section->course_id],
        ];
    }
}
