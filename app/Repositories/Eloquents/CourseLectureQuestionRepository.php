<?php

namespace App\Repositories\Eloquents;

use App\Models\CourseLectureQuestion;
use App\Repositories\Contracts\CourseLectureQuestionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseLectureQuestionRepository extends BaseRepository implements CourseLectureQuestionRepositoryInterface
{
    public function __construct(CourseLectureQuestion $model)
    {
        parent::__construct($model);
    }

    public function paginateFiltered(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user:id,name,machine_code', 'course:id,title', 'lecture:id,title', 'answeredBy:id,name'])
            ->when($filters['course_id'] ?? null, fn ($q, $v) => $q->where('course_id', $v))
            ->when($filters['lecture_id'] ?? null, fn ($q, $v) => $q->where('lecture_id', $v))
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when(isset($filters['answered']), function ($q) use ($filters) {
                $filters['answered']
                    ? $q->whereNotNull('answer')
                    : $q->whereNull('answer');
            })
            ->latest()
            ->paginate($perPage);
    }
}
