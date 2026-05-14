<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use App\Models\CourseExam;
use Illuminate\Database\Eloquent\Collection;

interface CourseExamRepositoryInterface extends BaseRepositoryInterface
{
    public function allForCourse(Course $course): Collection;
    public function findWithQuestions(int $id): CourseExam;
    public function createWithQuestions(array $data): CourseExam;
    public function updateWithQuestions(CourseExam $exam, array $data): CourseExam;
}
