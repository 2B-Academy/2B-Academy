<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_anonymous' => filter_var($this->input('is_anonymous', false), FILTER_VALIDATE_BOOLEAN),
            'active'       => $this->has('active')
                ? filter_var($this->input('active'), FILTER_VALIDATE_BOOLEAN)
                : true,
        ]);
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $imageRule = $isUpdate
            ? 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:4000'
            : 'required|image|mimes:png,jpg,jpeg,webp,svg,gif|max:4000';

        return [
            'title'                   => 'required|array',
            'title.en'                => 'required|string|max:255',
            'title.ar'                => 'required|string|max:255',

            'subtitle'                => 'nullable|array',
            'subtitle.en'             => 'nullable|string|max:1000',
            'subtitle.ar'             => 'nullable|string|max:1000',

            'image'                   => $imageRule,
            'level'                   => ['required', Rule::in(['beginner', 'intermediate', 'professional'])],

            'is_anonymous'            => 'boolean',
            'author_user_id'          => 'required_if:is_anonymous,false|nullable|exists:users,id',

            'reading_time'            => 'required|integer|min:1|max:1000',
            'qualification_skill_id'  => 'nullable|exists:qualification_skills,id',

            'active'                  => 'boolean',
            'published_at'            => 'nullable|date',

            'sections'                => 'required|array|min:1',
            'sections.*.id'           => 'nullable|integer',
            'sections.*.title'        => 'required|array',
            'sections.*.title.en'     => 'required|string|max:255',
            'sections.*.title.ar'     => 'required|string|max:255',
            'sections.*.body'         => 'required|array',
            'sections.*.body.en'      => 'required|string',
            'sections.*.body.ar'      => 'required|string',
            'sections.*.quote'        => 'nullable|array',
            'sections.*.quote.en'     => 'nullable|string|max:1000',
            'sections.*.quote.ar'     => 'nullable|string|max:1000',
            // Either a freshly uploaded file, or the existing stored path (edit).
            'sections.*.image'        => 'nullable',
            'sections.*.sort_order'   => 'nullable|integer|min:0',
        ];
    }
}
