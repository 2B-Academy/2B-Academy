<?php

namespace App\Repositories\Eloquents;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Repositories\Contracts\FormRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FormRepository extends BaseRepository implements FormRepositoryInterface
{
    public function __construct(Form $model)
    {
        parent::__construct($model);
    }

    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->when($search, fn ($q) => $q->where('title->ar', 'like', "%$search%")
                ->orWhere('title->en', 'like', "%$search%"))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findWithQuestions(int $id): Form
    {
        return $this->model->newQuery()
            ->with('questions.answers')
            ->findOrFail($id);
    }

    public function addQuestion(Form $form, array $data): FormQuestion
    {
        return DB::transaction(function () use ($form, $data) {
            $question = $form->questions()->create([
                'question' => $data['question'],
                'type'     => $data['type'],
            ]);

            if (in_array($question->type, ['radio', 'yes_no']) && !empty($data['answers'])) {
                foreach ($data['answers'] as $answerData) {
                    $question->answers()->create([
                        'answer'  => $answerData['answer'],
                        'is_true' => (bool) ($answerData['is_true'] ?? false),
                    ]);
                }
            }

            return $question->load('answers');
        });
    }

    public function deleteQuestion(FormQuestion $question): void
    {
        $question->delete();
    }
}
