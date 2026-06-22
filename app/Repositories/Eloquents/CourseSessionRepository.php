<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Models\CourseSession;
use App\Repositories\Contracts\CourseSessionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseSessionRepository extends BaseRepository implements CourseSessionRepositoryInterface
{
    public function __construct(CourseSession $model)
    {
        parent::__construct($model);
    }

    public function paginateForCourse(Course $course, int $perPage, ?int $sectionId): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('section')
            ->where('course_id', $course->id)
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->orderBy('session_date')
            ->orderBy('time_from')
            ->paginate($perPage);
    }

    public function createForCourse(Course $course, array $data): CourseSession
    {
        $session = $this->model->newQuery()->create(
            array_merge($data, ['course_id' => $course->id])
        );
        return $this->findWithSection($session->id);
    }

    public function findWithSection(int $id): CourseSession
    {
        return $this->model->newQuery()->with('section')->findOrFail($id);
    }
}
