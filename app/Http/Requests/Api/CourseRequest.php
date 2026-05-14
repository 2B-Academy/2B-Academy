<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = $this->isMethod('post')
            ? 'required|image|mimes:png,jpg,jpeg,webp,svg,gif|max:2048'
            : 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:2048';

        return [
            'course_type'        => 'sometimes|in:online,offline',
            'title'              => 'required|string|max:255',
            'title_for_certificate' => 'nullable|string|max:255',
            'description'        => 'required|string',
            'category_id'        => 'required|exists:categories,id',
            'intro_video'        => 'nullable|string',
            'price'              => 'nullable|numeric|min:0',
            'currency'           => 'nullable|string|max:10',
            'hours'              => 'required|integer|min:1',
            'language'           => 'nullable|string|max:50',
            'level'              => 'nullable|string|max:50',
            'certificate'        => 'required|boolean',
            'image'              => $imageRule,
            'active'             => 'nullable|boolean',
            'outside_materials'  => 'nullable|boolean',
            'is_evaluate'        => 'nullable|boolean',
            'allow_attendances'  => 'nullable|boolean',
            'instructors'        => 'required|array|min:1',
            'instructors.*'      => 'required|exists:instructors,id',
        ];
    }
}
