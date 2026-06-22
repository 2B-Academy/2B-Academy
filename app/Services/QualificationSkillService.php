<?php

namespace App\Services;

use App\Models\QualificationSkill;
use App\Repositories\Contracts\QualificationSkillRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QualificationSkillService
{
    public function __construct(
        private readonly QualificationSkillRepositoryInterface $repository,
    ) {}

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->paginateWithFilters($perPage, $search);
    }

    public function allForSelect(): Collection
    {
        return $this->repository->allForSelect();
    }

    public function findOrFail(int $id): QualificationSkill
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): QualificationSkill
    {
        return $this->repository->create($this->normaliseName($data));
    }

    public function update(QualificationSkill $skill, array $data): QualificationSkill
    {
        return $this->repository->update($skill, $this->normaliseName($data));
    }

    public function delete(QualificationSkill $skill): bool
    {
        return $this->repository->delete($skill);
    }

    /**
     * The model stores `name` as a Spatie translatable JSON column.
     * Requests may arrive with either:
     *   - name => ['en' => '...', 'ar' => '...']   (canonical, multi-locale)
     *   - name => 'value'                          (single locale, current request)
     *
     * Normalise to an array so HasTranslations writes every locale we received.
     */
    private function normaliseName(array $data): array
    {
        if (! array_key_exists('name', $data)) {
            return $data;
        }

        if (is_string($data['name'])) {
            $data['name'] = [app()->getLocale() => $data['name']];
        }

        return $data;
    }
}
