<?php

namespace App\Http\Requests\Api;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseLectureRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // The route binds `{course}` to a Course model (implicit binding);
        // fall back to the raw id when it's not yet resolved.
        $routeCourse = $this->route('course');
        $courseId = $routeCourse instanceof Course ? $routeCourse->id : $routeCourse;

        return [
            // `section_id` is optional: the service will fall back to a
            // course-default section when omitted, since the admin "Module"
            // form intentionally hides section selection.
            'section_id'         => 'nullable|integer|exists:course_sections,id',

            'title'              => 'required|array',
            'title.ar'           => 'required|string|max:255',
            'title.en'           => 'nullable|string|max:255',

            'content_type'       => 'required|in:video,document,article,link',
            'learner_scope'      => 'required|in:all,cohort',
            // For the "Specific Cohort" scope `session_id` carries the chosen
            // COHORT id (= course_sections.id), not a course_sessions id — the
            // column is unconstrained and the mobile cohort-scoping compares it
            // against the learner's group_id (also a section id). Validate it
            // against this course's own cohorts so cross-course ids are rejected.
            'session_id'         => [
                'nullable',
                'required_if:learner_scope,cohort',
                'integer',
                Rule::exists('course_sections', 'id')->where(
                    fn ($q) => $q->where('course_id', $courseId),
                ),
            ],
            'duration_minutes'   => 'nullable|integer|min:0|max:10000',

            // `type` reflects how the `video` value is stored (url | file).
            // Defaults derived from `content_type` in the service if absent.
            'type'               => 'nullable|in:url,file',
            'video'              => 'required|string|max:2048',

            // Optional original filename for uploaded documents — preserved so
            // the Edit dialog can render "File Title.pdf · 313 KB" instead of a
            // storage hash. Ignored for URL-based content types.
            'file_name'          => 'nullable|string|max:255',

            'instructions'       => 'nullable|array',
            'instructions.en'    => 'nullable|string|max:1000',
            'instructions.ar'    => 'nullable|string|max:1000',

            'require_completion' => 'nullable|boolean',
        ];
    }
}
