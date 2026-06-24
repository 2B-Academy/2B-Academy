<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\AcceptsEnumIds;
use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    use AcceptsEnumIds;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Enum fields on this request. Frontends may submit either the numeric
     * dropdown id (preferred, matches `/api/v1/enums/course_type`) OR the
     * legacy string code — the trait normalizes both to the string before
     * validation runs.
     */
    protected function enumFieldMap(): array
    {
        return [
            'course_type' => 'course_type',
            'type'        => 'course_type',
            'level'       => 'course_level',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeEnumIdsToCodes();

        $merge = [];

        if ($this->has('instructor_id') && ! $this->has('instructors')) {
            $merge['instructors'] = [(int) $this->input('instructor_id')];
        }

        if ($this->has('type') && ! $this->has('course_type')) {
            // Normalize legacy `type` field to the full course_type enum code.
            $allowed = ['online', 'offline', 'hybrid', 'external_link'];
            $type    = $this->input('type');
            $merge['course_type'] = in_array($type, $allowed, true) ? $type : 'offline';
        }

        if ($this->has('qualification_ids') && ! $this->has('qualification_skill_ids')) {
            $merge['qualification_skill_ids'] = $this->input('qualification_ids');
        }

        if (! $this->has('hours')) {
            $merge['hours'] = 1;
        }

        if (is_string($this->input('title'))) {
            $merge['title'] = ['en' => $this->input('title'), 'ar' => $this->input('title')];
        }

        if (is_string($this->input('description'))) {
            $merge['description'] = ['en' => $this->input('description'), 'ar' => $this->input('description')];
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $imageRule = $this->isMethod('post')
            ? 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:2048'
            : 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:2048';

        return [
            'course_type'             => 'sometimes|in:online,offline,hybrid,external_link',
            'title'                   => 'required|array',
            'title.en'                => 'required|string|max:255',
            'title.ar'                => 'nullable|string|max:255',
            'title_for_certificate'   => 'nullable|string|max:255',
            'description'             => 'required|array',
            'description.en'          => 'required|string',
            'description.ar'          => 'nullable|string',
            'category_id'             => 'required|exists:categories,id',
            'intro_video'             => 'nullable|string',
            'price'                   => 'nullable|numeric|min:0',
            'currency'                => 'nullable|string|max:10',
            'hours'                   => 'required|integer|min:1',
            'max_learners'            => 'nullable|integer|min:1|max:10000',
            // Planned session count (Figma 321:7349). Mandatory when the
            // course is first created, then read-only — the Edit Course
            // dialog never sends it, so it's optional on update.
            'number_of_sessions'      => $this->isMethod('post')
                ? 'required|integer|min:1|max:1000'
                : 'nullable|integer|min:1|max:1000',
            'language'                => 'nullable|string|max:50',
            'level'                   => 'nullable|in:beginner,intermediate,professional',
            'certificate'             => 'required|boolean',
            'image'                   => $imageRule,
            'active'                  => 'nullable|boolean',
            'outside_materials'       => 'nullable|boolean',
            'is_evaluate'             => 'nullable|boolean',
            'allow_attendances'       => 'nullable|boolean',
            'instructors'             => 'required|array|min:1',
            'instructors.*'           => 'required|exists:instructors,id',
            'qualification_skill_ids' => 'nullable|array',
            'qualification_skill_ids.*' => 'integer|distinct|exists:qualification_skills,id',
            // Cohort window. Mandatory on course creation (Figma 321:7349
            // marks both dates required); optional on update so editing an
            // older course without a cohort window doesn't 422.
            'cohort_start'            => $this->isMethod('post')
                ? 'required|date_format:Y-m-d'
                : 'nullable|date_format:Y-m-d',
            'cohort_end'              => $this->isMethod('post')
                ? 'required|date_format:Y-m-d|after_or_equal:cohort_start'
                : 'nullable|date_format:Y-m-d|after_or_equal:cohort_start',
        ];
    }
}
