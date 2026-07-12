<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLecture;
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

    /**
     * Batch variant of getCourseProgress — resolves module completion % for
     * MANY courses in 2 queries total instead of 1 per course, so the
     * `my/learnings` dashboard composite (#4) doesn't N+1 across a
     * learner's course list. Same rule as getCourseProgress: completed
     * lecture rows ÷ total lectures for that course.
     *
     * @param  array<int, int>  $courseIds
     * @return array<int, int>  course_id => percent (0-100)
     */
    public function getCourseProgressBatch(int $userId, array $courseIds): array
    {
        if (empty($courseIds)) {
            return [];
        }

        $totalsByCourse = CourseLecture::whereIn('course_id', $courseIds)
            ->selectRaw('course_id, COUNT(*) as total')
            ->groupBy('course_id')
            ->pluck('total', 'course_id');

        $completedByCourse = CourseLecture::whereIn('course_lectures.course_id', $courseIds)
            ->join('user_lecture_progress', function ($join) use ($userId) {
                $join->on('user_lecture_progress.lecture_id', '=', 'course_lectures.id')
                    ->where('user_lecture_progress.user_id', '=', $userId)
                    ->where('user_lecture_progress.completed', '=', true);
            })
            ->selectRaw('course_lectures.course_id as course_id, COUNT(*) as completed')
            ->groupBy('course_lectures.course_id')
            ->pluck('completed', 'course_id');

        $result = [];
        foreach ($courseIds as $courseId) {
            $total = (int) ($totalsByCourse->get($courseId) ?? 0);
            $completed = (int) ($completedByCourse->get($courseId) ?? 0);
            $result[$courseId] = $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        }

        return $result;
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
