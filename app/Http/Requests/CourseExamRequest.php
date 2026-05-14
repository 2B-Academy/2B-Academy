<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => 'required|exists:course_sections,id',
            'title' => 'required|max:255',
            'degree' => 'required|integer',
            'is_final' => 'nullable|boolean',
            'questions' => 'required|array',
            'questions.*.title' => 'required|string',
            'questions.*.is_correct' => 'required|integer|min:0',
            'questions.*.answers' => 'required|array|min:1',
           // 'questions.*.answers.*' => 'required|string',
        ];
    }

     public function attributes()
    {
           return [
               'section_id.required',
               'section_id.exists',
               'title.required',
               'title.max',
               'degree.required',
               'degree.integer',
               'questions.required',
               'questions.array',
               'questions.*.title',
               'questions.*.is_correct',
               'questions.*.answers',
               'questions.*.answers.*',
           ];
    }

}
