<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\User;
use App\Models\UserCourseAssignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseAssignmentRepositoryInterface extends BaseRepositoryInterface
{
    public function listForCourse(Course $course): Collection;
    public function createForCourse(Course $course, array $data): CourseAssignment;
    public function paginateAll(?int $courseId, ?string $search, int $perPage): LengthAwarePaginator;
    public function listSubmissions(CourseAssignment $assignment, int $perPage): LengthAwarePaginator;
    public function upsertSubmission(CourseAssignment $assignment, User $user, array $data): UserCourseAssignment;
    public function findSubmission(int $assignmentId, int $userId): ?UserCourseAssignment;
    public function findSubmissionWithRelations(int $id): UserCourseAssignment;
    public function paginateAllSubmissions(?int $userId, ?int $courseId, ?string $status, int $perPage): LengthAwarePaginator;
    public function deleteSubmissionById(int $id): void;
}
