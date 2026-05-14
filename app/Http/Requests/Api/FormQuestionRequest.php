<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FormQuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'                   => 'required|in:radio,yes_no,text',
            'question'               => 'required|array',
            'question.ar'            => 'required|string',
            'question.en'            => 'nullable|string',
            'answers'                => 'required_unless:type,text|array|min:2',
            'answers.*.answer'       => 'required|array',
            'answers.*.answer.ar'    => 'required|string',
            'answers.*.answer.en'    => 'nullable|string',
            'answers.*.is_true'      => 'required|boolean',
        ];
    }
}
