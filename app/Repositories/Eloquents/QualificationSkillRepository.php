<?php

namespace App\Repositories\Eloquents;

use App\Models\QualificationSkill;
use App\Repositories\Contracts\QualificationSkillRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QualificationSkillRepository extends BaseRepository implements QualificationSkillRepositoryInterface
{
    public function __construct(QualificationSkill $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(int $perPage, ?string $search): LengthAwarePaginator
    {
        $enrolledSubQuery = DB::table('users_courses')
            ->selectRaw('COUNT(DISTINCT users_courses.user_id)')
            ->join('course_qualification_skills', 'users_courses.course_id', '=', 'course_qualification_skills.course_id')
            ->whereColumn('course_qualification_skills.qualification_skill_id', 'qualification_skills.id');

        return $this->model->newQuery()
            ->when($search, fn ($q) => $q->where('name', 'LIKE', "%{$search}%"))
            ->withCount('courses')
            ->addSelect(['qualification_skills.*', DB::raw("({$enrolledSubQuery->toSql()}) as enrolled_count")])
            ->mergeBindings($enrolledSubQuery)
            ->latest()
            ->paginate($perPage);
    }

    public function allForSelect(): Collection
    {
        return $this->model->newQuery()
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get();
    }
}
