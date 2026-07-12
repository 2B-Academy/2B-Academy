<?php

namespace App\Http\Requests\Api;

use App\Models\CourseExamQuestion;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a learner's answer to a single rich quiz question. The shape
 * required depends on the question's `type` (resolved from the bound
 * route-model {question}), matching CourseExamQuestion::TYPES.
 */
class LearnerQuizAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var CourseExamQuestion|null $question */
        $question = $this->route('question');

        return match ($question?->type) {
            'open' => ['value' => ['required', 'string', 'max:500']],
            'reorder' => ['order' => ['required', 'array', 'min:1'], 'order.*' => ['string', 'max:500']],
            default => ['value' => ['required', 'string', 'max:1000']], // mcq | yes_no
        };
    }
}
