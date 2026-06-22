<?php

namespace App\Services;

use App\Models\CourseLectureQuestion;
use App\Repositories\Contracts\CourseLectureQuestionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseLectureQuestionService
{
    public function __construct(
        private readonly CourseLectureQuestionRepositoryInterface $questionRepository,
    ) {}

    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->questionRepository->paginateFiltered($perPage, $filters);
    }

    public function submit(array $data): CourseLectureQuestion
    {
        return $this->questionRepository->create($data);
    }

    public function answer(CourseLectureQuestion $question, int $adminId, string $answer): CourseLectureQuestion
    {
        return $this->questionRepository->update($question, [
            'answer'      => $answer,
            'answered_by' => $adminId,
        ]);
    }

    public function delete(CourseLectureQuestion $question): bool
    {
        return $this->questionRepository->delete($question);
    }
}
