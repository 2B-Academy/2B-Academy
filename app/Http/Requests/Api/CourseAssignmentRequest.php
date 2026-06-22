<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'file'     => ($this->isMethod('POST') ? 'required' : 'nullable') . '|file|max:20480',
        ];
    }
}
