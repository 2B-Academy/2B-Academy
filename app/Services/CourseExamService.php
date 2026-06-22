<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseExam;
use App\Repositories\Contracts\CourseExamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseExamService
{
    public function __construct(
        private readonly CourseExamRepositoryInterface $repo
    ) {}

    public function listForCourse(Course $course): Collection
    {
        return $this->repo->allForCourse($course);
    }

    public function find(int $id): CourseExam
    {
        return $this->repo->findWithQuestions($id);
    }

    public function create(Course $course, array $data): CourseExam
    {
        $data['course_id'] = $course->id;
        $data['is_final']  = (bool) ($data['is_final'] ?? false);
        return $this->repo->createWithQuestions($data);
    }

    public function update(CourseExam $exam, array $data): CourseExam
    {
        if (isset($data['is_final'])) {
            $data['is_final'] = (bool) $data['is_final'];
        }
        return $this->repo->updateWithQuestions($exam, $data);
    }

    public function delete(CourseExam $exam): void
    {
        $this->repo->delete($exam);
    }
}
