<?php

namespace App\Services;

use App\Models\Course;
use App\Models\UserLectureProgress;

class LectureProgressService
{
    /**
     * Record or update lecture watch progress for a user.
     * Marks as completed when progress >= 90.
     */
    public function track(int $userId, int $lectureId, int $progress): UserLectureProgress
    {
        return UserLectureProgress::updateOrCreate(
            ['user_id' => $userId, 'lecture_id' => $lectureId],
            ['progress' => $progress, 'completed' => $progress >= 90],
        );
    }

    /**
     * Calculate overall course completion % for a user.
     * Mirrors HelperTrait::userCourseProgress().
     */
    public function getCourseProgress(int $userId, int $courseId): int
    {
        $course = Course::with('lectures:id,course_id')->findOrFail($courseId);

        $totalLectures = $course->lectures->count();
        if ($totalLectures === 0) {
            return 0;
        }

        $completed = UserLectureProgress::where('user_id', $userId)
            ->whereIn('lecture_id', $course->lectures->pluck('id'))
            ->where('completed', true)
            ->count();

        return (int) round(($completed / $totalLectures) * 100);
    }

    /** Get per-lecture progress for a user within a course. */
    public function getLectureProgress(int $userId, int $courseId): array
    {
        $course = Course::with('lectures:id,course_id,title')->findOrFail($courseId);

        $progressMap = UserLectureProgress::where('user_id', $userId)
            ->whereIn('lecture_id', $course->lectures->pluck('id'))
            ->get()
            ->keyBy('lecture_id');

        return $course->lectures->map(fn ($lecture) => [
            'lecture_id' => $lecture->id,
            'title'      => $lecture->title,
            'progress'   => $progressMap[$lecture->id]->progress ?? 0,
            'completed'  => (bool) ($progressMap[$lecture->id]->completed ?? false),
        ])->all();
    }
}
