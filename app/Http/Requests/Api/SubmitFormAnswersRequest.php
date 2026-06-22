<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFormAnswersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'questions'                  => 'required|array|min:1',
            'questions.*.question_id'    => 'required|integer',
            'questions.*.question_title' => 'required|string',
            'questions.*.answer_id'      => 'required',
            'minutes_remaining'          => 'sometimes|integer|min:0',
        ];
    }
}
