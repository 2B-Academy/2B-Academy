<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\CourseAssignmentQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminAssignmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'                 => ['required', 'integer', 'exists:courses,id'],
            'title'                     => ['required', 'string', 'max:255'],
            'title_ar'                  => ['nullable', 'string', 'max:255'],
            'instructions_en'           => ['nullable', 'string', 'max:5000'],
            'instructions_ar'           => ['nullable', 'string', 'max:5000'],
            'due_date'                  => ['nullable', 'date'],
            'cohort_scope'              => ['required', Rule::in(['all', 'specific'])],
            'cohort_ids'                => ['nullable', 'array'],
            'cohort_ids.*'              => ['integer', 'exists:course_sessions,id'],
            'pass_score'                => ['nullable', 'integer', 'min:0'],
            'status'                    => ['nullable', Rule::in(['draft', 'active'])],

            'questions'                 => ['required', 'array', 'min:1'],
            'questions.*.type'          => ['required', Rule::in(CourseAssignmentQuestion::TYPES)],
            'questions.*.score'         => ['required', 'integer', 'min:0'],
            'questions.*.question_en'   => ['required', 'string', 'max:2000'],
            'questions.*.question_ar'   => ['nullable', 'string', 'max:2000'],
            'questions.*.options_en'    => ['nullable', 'array'],
            'questions.*.options_en.*'  => ['string', 'max:500'],
            'questions.*.options_ar'    => ['nullable', 'array'],
            'questions.*.options_ar.*'  => ['string', 'max:500'],
            'questions.*.correct_answer_en' => ['nullable', 'string', 'max:5000'],
            'questions.*.correct_answer_ar' => ['nullable', 'string', 'max:5000'],
            'questions.*.explanation_en'    => ['nullable', 'string', 'max:5000'],
            'questions.*.explanation_ar'    => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $scope    = $this->input('cohort_scope');
            $cohorts  = $this->input('cohort_ids', []);

            if ($scope === 'specific' && (empty($cohorts) || !is_array($cohorts))) {
                $v->errors()->add('cohort_ids', __('Select at least one cohort when scope is specific.'));
            }

            foreach ((array) $this->input('questions', []) as $i => $q) {
                $type = $q['type'] ?? null;
                $opts = $q['options_en'] ?? [];

                if (in_array($type, ['mcq', 'reorder'], true) && (empty($opts) || count($opts) < 2)) {
                    $v->errors()->add("questions.$i.options_en", __('Provide at least two options.'));
                }

                if ($type !== 'open' && empty($q['correct_answer_en'])) {
                    $v->errors()->add("questions.$i.correct_answer_en", __('Correct answer is required for this question type.'));
                }
            }
        });
    }
}
