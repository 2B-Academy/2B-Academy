<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
        $required_image = request()->isMethod('put') ?
            'nullable|mimes:png,jpg,jpeg,webp,svg,gif|max:2000' :
            'required|mimes:png,jpg,jpeg,webp,svg,gif|max:2000';
        return [
            'course_type' => 'sometimes|in:online,offline',
            'title' => 'required|max:255',
            'title_for_certificate' => 'nullable|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'intro_video' => 'nullable',
            'price' => 'nullable',
            'currency' => 'nullable',
            'hours' => 'required|integer',
            'language' => 'nullable',
            'level' => 'nullable',
            'certificate' => 'required',
            'image' => $required_image,
            'active' => 'nullable|boolean',
            'instructors.*' => 'required',
            'outside_materials' => 'nullable|boolean',
            'is_evaluate' => 'nullable|boolean',
            'qualification_skill_ids'   => 'nullable|array',
            'qualification_skill_ids.*' => 'integer|distinct|exists:qualification_skills,id',
        ];
    }

     public function attributes()
    {
           return [
               'title.required',
               'title.max',
               'description.required',
               'category_id.required',
               'category_id.exists',
               'certificate.required',
               'certificate.boolean',
               'image.required',
               'image.mimes',
               'image.max',
               'active.boolean',
               'instructors.*.required',
           ];
    }

}
