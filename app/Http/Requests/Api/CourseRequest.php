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
            $type = $this->input('type');
            $merge['course_type'] = $type === 'online' ? 'online' : 'offline';
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
            'course_type'             => 'sometimes|in:online,offline',
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
            // Optional cohort window: when present, the controller
            // upserts the course's *first* cohort (course_sections row)
            // so the calendar can drive status. Both can be sent
            // independently — the service treats them as a window
            // that defaults to "open-ended" on the missing side.
            'cohort_start'            => 'nullable|date_format:Y-m-d',
            'cohort_end'              => 'nullable|date_format:Y-m-d|after_or_equal:cohort_start',
        ];
    }
}
