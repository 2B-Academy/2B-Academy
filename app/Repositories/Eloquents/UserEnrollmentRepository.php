<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Models\UsersCourse;
use App\Repositories\Contracts\UserEnrollmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserEnrollmentRepository implements UserEnrollmentRepositoryInterface
{
    public function paginateForCourse(Course $course, int $perPage, ?int $groupId): LengthAwarePaginator
    {
        // The Learners tab in the course detail screen needs a real progress
        // bar (0–100%) per row. We compute it inline as a correlated
        // sub-select so a single query returns everything Eloquent needs to
        // hydrate — no N+1 round-trips, even for hundreds of enrollments.
        $lectureIds = $course->lectures()->pluck('id')->all();
        $totalLectures = count($lectureIds);

        $query = UsersCourse::query()
            ->select('users_courses.*')
            ->with(['user:id,name,machine_code,department_name', 'group'])
            ->where('users_courses.course_id', $course->id)
            ->when($groupId, fn ($q) => $q->where('users_courses.group_id', $groupId))
            ->orderByDesc('users_courses.id');

        if ($totalLectures > 0) {
            // Bind lecture IDs through the query builder so we don't
            // interpolate raw integers into SQL.
            $placeholders = implode(',', array_fill(0, $totalLectures, '?'));
            $query->selectRaw(
                "FLOOR((
                    SELECT COUNT(*) FROM user_lecture_progress
                    WHERE user_lecture_progress.user_id = users_courses.user_id
                      AND user_lecture_progress.completed = 1
                      AND user_lecture_progress.lecture_id IN ({$placeholders})
                ) * 100 / ?) AS progress_percent",
                array_merge($lectureIds, [$totalLectures])
            );
        } else {
            $query->selectRaw('0 AS progress_percent');
        }

        return $query->paginate($perPage);
    }

    public function syncUsers(Course $course, array $userIds, ?int $groupId): void
    {
        $data = [];
        foreach ($userIds as $userId) {
            $data[$userId] = ['group_id' => $groupId];
        }
        $course->users()->syncWithoutDetaching($data);
    }

    public function delete(UsersCourse $enrollment): void
    {
        $enrollment->delete();
    }
}
