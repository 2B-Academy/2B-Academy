<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PendingGradeNotification extends Notification
{
    /**
     * @param  string  $assessmentType  'quiz' | 'assignment' — carried in meta for deep-linking only.
     * @param  array<string, mixed>  $meta  Extra ids for deep-linking (course_id, assessment_id, submission_id).
     */
    public function __construct(
        private readonly string $studentName,
        private readonly string $assessmentTitle,
        private readonly string $assessmentType,
        private readonly array $meta = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $params = ['student' => $this->studentName, 'title' => $this->assessmentTitle];

        return [
            'type'     => 'pending_grade',
            'title_en' => __('messages.notifications.pending_grade_title', [], 'en'),
            'title_ar' => __('messages.notifications.pending_grade_title', [], 'ar'),
            'body_en'  => __('messages.notifications.pending_grade_body', $params, 'en'),
            'body_ar'  => __('messages.notifications.pending_grade_body', $params, 'ar'),
            'meta'     => array_merge(['assessment_type' => $this->assessmentType], $this->meta),
        ];
    }
}
