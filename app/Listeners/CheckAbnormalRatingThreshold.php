<?php

namespace App\Listeners;

use App\Events\CourseRatingSubmitted;
use App\Models\Admin;
use App\Notifications\CourseRatingDroppedNotification;
use App\Services\SettingService;

/**
 * Notifies the course's instructors and every Admin whenever a course's
 * average rating is at or below the platform-configured
 * `abnormal_rating_threshold` (Settings > Grading & Certificates).
 */
class CheckAbnormalRatingThreshold
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function handle(CourseRatingSubmitted $event): void
    {
        $course = $event->course;

        $average = (float) ($course->ratings()->avg('rating') ?? 0);
        $threshold = (float) ($this->settings->getMap()['abnormal_rating_threshold'] ?? 2);

        if ($average > $threshold) {
            return;
        }

        foreach ($course->instructors as $instructor) {
            $instructor->notify(new CourseRatingDroppedNotification($course, $average));
        }

        foreach (Admin::all() as $admin) {
            $admin->notify(new CourseRatingDroppedNotification($course, $average));
        }
    }
}
