<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'questions'                  => 'required|array|min:1',
            'questions.*.question_id'    => 'required|integer|exists:course_exam_questions,id',
            'questions.*.question_title' => 'required|string',
            'questions.*.answer_id'      => 'required|integer',
        ];
    }
}
