<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id'    => 'required|integer|exists:course_sections,id',
            'user_ids'    => 'required|array|min:1',
            'user_ids.*'  => 'integer|exists:users,id',
        ];
    }
}
