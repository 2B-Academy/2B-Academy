<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitCourseEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'instructor_id' => 'required|integer|exists:instructors,id',
            'questions'     => 'required|array|min:1',
            'questions.*'   => 'required',
        ];
    }
}
