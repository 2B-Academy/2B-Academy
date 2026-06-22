<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSession;
use App\Repositories\Contracts\CourseSessionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseSessionService
{
    public function __construct(
        private readonly CourseSessionRepositoryInterface $repo
    ) {}

    public function paginate(Course $course, int $perPage, ?int $sectionId): LengthAwarePaginator
    {
        return $this->repo->paginateForCourse($course, $perPage, $sectionId);
    }

    public function create(Course $course, array $data): CourseSession
    {
        return $this->repo->createForCourse($course, $data);
    }

    public function update(CourseSession $session, array $data): CourseSession
    {
        $this->repo->update($session, $data);
        return $this->repo->findWithSection($session->id);
    }

    public function delete(CourseSession $session): void
    {
        $this->repo->delete($session);
    }
}
