<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Repositories\Contracts\CourseAssignmentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseAssignmentService
{
    use HasFile;

    public function __construct(
        private readonly CourseAssignmentRepositoryInterface $repo
    ) {}

    public function listForCourse(Course $course): Collection
    {
        return $this->repo->listForCourse($course);
    }

    public function create(Course $course, string $title, UploadedFile $file, ?string $dueDate = null): CourseAssignment
    {
        $path = $this->uploadRequestFile('CourseAssignment', request(), null, $file);

        /** @var CourseAssignment */
        return $this->repo->createForCourse($course, array_filter([
            'title'    => $title,
            'file'     => $path,
            'due_date' => $dueDate,
        ], fn ($v) => $v !== null));
    }

    public function update(CourseAssignment $assignment, string $title, ?UploadedFile $file, ?string $dueDate = null): CourseAssignment
    {
        $data = ['title' => $title, 'due_date' => $dueDate];
        if ($file) {
            $data['file'] = $this->uploadRequestFile('CourseAssignment', request(), null, $file);
        }

        /** @var CourseAssignment */
        return $this->repo->update($assignment, $data);
    }

    public function paginateAll(?int $courseId, ?string $search, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->repo->paginateAll($courseId, $search, $perPage);
    }

    public function delete(CourseAssignment $assignment): void
    {
        $this->repo->delete($assignment);
    }

    public function listSubmissions(CourseAssignment $assignment, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repo->listSubmissions($assignment, $perPage);
    }

    public function submitFile(CourseAssignment $assignment, User $user, UploadedFile $file): UserCourseAssignment
    {
        $path = $this->uploadRequestFile('UserAssignment', request(), null, $file);
        $submission = $this->repo->upsertSubmission($assignment, $user, ['user_file' => $path]);
        return $this->repo->findSubmissionWithRelations($submission->id);
    }

    public function reviewSubmission(UserCourseAssignment $submission, ?string $feedback, ?string $score): UserCourseAssignment
    {
        $this->repo->update($submission, ['feedback' => $feedback, 'score' => $score]);
        return $this->repo->findSubmissionWithRelations($submission->id);
    }

    public function findSubmission(int $assignmentId, int $userId): ?UserCourseAssignment
    {
        return $this->repo->findSubmission($assignmentId, $userId);
    }

    public function findSubmissionById(int $id): UserCourseAssignment
    {
        return $this->repo->findSubmissionWithRelations($id);
    }

    public function paginateAllSubmissions(?int $userId, ?int $courseId, ?string $status = null, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->repo->paginateAllSubmissions($userId, $courseId, $status, $perPage);
    }

    public function deleteSubmissionById(int $id): void
    {
        $this->repo->deleteSubmissionById($id);
    }
}
