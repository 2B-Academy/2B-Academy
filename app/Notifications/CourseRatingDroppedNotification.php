<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Notifications\Notification;

class CourseRatingDroppedNotification extends Notification
{
    public function __construct(
        private readonly Course $course,
        private readonly float $averageRating,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $audience = $notifiable instanceof Instructor ? 'instructor' : 'admin';
        $rating   = round($this->averageRating, 1);

        $paramsEn = ['course' => $this->course->getTranslation('title', 'en'), 'rating' => $rating];
        $paramsAr = ['course' => $this->course->getTranslation('title', 'ar') ?: $this->course->getTranslation('title', 'en'), 'rating' => $rating];

        return [
            'type'     => 'rating_dropped',
            'title_en' => __("messages.notifications.rating_dropped_{$audience}_title", [], 'en'),
            'title_ar' => __("messages.notifications.rating_dropped_{$audience}_title", [], 'ar'),
            'body_en'  => __("messages.notifications.rating_dropped_{$audience}_body", $paramsEn, 'en'),
            'body_ar'  => __("messages.notifications.rating_dropped_{$audience}_body", $paramsAr, 'ar'),
            'meta'     => ['course_id' => $this->course->id, 'rating' => $rating],
        ];
    }
}
