<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id'   => 'required|integer|exists:course_sections,id',
            'title'        => 'required|string|max:255',
            'session_date' => 'nullable|date',
            'time_from'    => 'nullable|date_format:H:i',
            'time_to'      => 'nullable|date_format:H:i',
            'location'     => 'nullable|string|max:255',
        ];
    }
}
