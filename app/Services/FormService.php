<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormQuestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FormService
{
    public function paginate(int $perPage = 20, ?string $search = null): LengthAwarePaginator
    {
        return Form::when($search, fn ($q) => $q->where('title->ar', 'like', "%$search%")
                ->orWhere('title->en', 'like', "%$search%")
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function find(int $id): Form
    {
        return Form::with('questions.answers')->findOrFail($id);
    }

    public function create(array $data): Form
    {
        $data['active'] = (bool) ($data['active'] ?? true);
        return Form::create($data);
    }

    public function update(Form $form, array $data): Form
    {
        if (isset($data['active'])) {
            $data['active'] = (bool) $data['active'];
        }
        $form->update($data);
        return $form->fresh();
    }

    public function addQuestion(Form $form, array $data): FormQuestion
    {
        return DB::transaction(function () use ($form, $data) {
            $question = $form->questions()->create([
                'question' => $data['question'],
                'type'     => $data['type'],
            ]);

            if (in_array($question->type, ['radio', 'yes_no']) && !empty($data['answers'])) {
                foreach ($data['answers'] as $index => $answerData) {
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

    public function delete(Form $form): void
    {
        $form->delete();
    }
}
