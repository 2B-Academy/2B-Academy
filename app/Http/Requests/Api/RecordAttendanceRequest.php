<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RecordAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'   => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'status'    => 'required|boolean',
        ];
    }
}
