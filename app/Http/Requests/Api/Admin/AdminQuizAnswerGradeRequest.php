<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Grader request for a single open-question quiz answer.
 */
class AdminQuizAnswerGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'awarded_score' => ['required', 'integer', 'min:0'],
            'feedback'      => ['nullable', 'string', 'max:5000'],
        ];
    }
}
