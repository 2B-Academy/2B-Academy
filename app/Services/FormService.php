<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Repositories\Contracts\FormRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FormService
{
    public function __construct(
        private readonly FormRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage = 20, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginateWithSearch($perPage, $search);
    }

    public function find(int $id): Form
    {
        return $this->repo->findWithQuestions($id);
    }

    public function create(array $data): Form
    {
        $data['active'] = (bool) ($data['active'] ?? true);

        /** @var Form */
        return $this->repo->create($data);
    }

    public function update(Form $form, array $data): Form
    {
        if (isset($data['active'])) {
            $data['active'] = (bool) $data['active'];
        }

        /** @var Form */
        return $this->repo->update($form, $data);
    }

    public function addQuestion(Form $form, array $data): FormQuestion
    {
        return $this->repo->addQuestion($form, $data);
    }

    public function deleteQuestion(FormQuestion $question): void
    {
        $this->repo->deleteQuestion($question);
    }

    public function delete(Form $form): void
    {
        $this->repo->delete($form);
    }
}
