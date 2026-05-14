<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseExamRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'section_id'                          => 'required|integer|exists:course_sections,id',
            'title'                               => 'required|array',
            'title.ar'                            => 'required|string|max:255',
            'title.en'                            => 'nullable|string|max:255',
            'degree'                              => 'required|integer|min:1',
            'is_final'                            => 'nullable|boolean',
            'questions'                           => 'required|array|min:1',
            'questions.*.question'                => 'required|array',
            'questions.*.question.ar'             => 'required|string',
            'questions.*.question.en'             => 'nullable|string',
            'questions.*.answers'                 => 'required|array|min:2',
            'questions.*.answers.*.answer'        => 'required|array',
            'questions.*.answers.*.answer.ar'     => 'required|string',
            'questions.*.answers.*.answer.en'     => 'nullable|string',
            'questions.*.answers.*.is_correct'    => 'required|boolean',
        ];
    }
}
