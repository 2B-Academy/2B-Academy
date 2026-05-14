<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseLectureRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'section_id' => 'required|integer|exists:course_sections,id',
            'title'      => 'required|array',
            'title.ar'   => 'required|string|max:255',
            'title.en'   => 'nullable|string|max:255',
            'type'       => 'required|in:url,file',
            'video'      => 'required|string',
        ];
    }
}
