<?php

namespace App\Repositories\Contracts;

use App\Models\Form;
use App\Models\FormQuestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FormRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator;
    public function findWithQuestions(int $id): Form;
    public function addQuestion(Form $form, array $data): FormQuestion;
    public function deleteQuestion(FormQuestion $question): void;
}
