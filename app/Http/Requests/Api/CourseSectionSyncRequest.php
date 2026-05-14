<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseSectionSyncRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'sections'         => 'required|array|min:1',
            'sections.*.id'    => 'nullable|integer|exists:course_sections,id',
            'sections.*.name'  => 'required|array',
            'sections.*.name.ar' => 'required|string|max:255',
            'sections.*.name.en' => 'nullable|string|max:255',
        ];
    }
}
