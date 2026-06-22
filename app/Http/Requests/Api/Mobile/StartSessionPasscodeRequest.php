<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Mobile;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Instructor-dashboard payload for STARTING a session and issuing its
 * passcode in one tap.
 *
 * Unlike the admin per-session endpoint (which takes a `{session}` URL
 * param), the dashboard picks the course + cohort up-front and the
 * backend creates the `course_sessions` row for *today* before issuing
 * the code. The deeper ownership/eligibility checks (instructor teaches
 * the course, cohort belongs to it, cohort not ended) are enforced in
 * the controller where the authenticated instructor context lives.
 *
 *   - `course_id`  (required) the course to start a session for.
 *   - `cohort_id`  (required) the cohort (course_sections row) to attach
 *                  the session to.
 *   - `expires_at` (optional) overrides the default validity window.
 *   - `length`     (optional) overrides the platform passcode length.
 */
final class StartSessionPasscodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'  => ['required', 'integer', 'exists:courses,id'],
            'cohort_id'  => ['required', 'integer', 'exists:course_sections,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'length'     => ['nullable', 'integer', 'min:3', 'max:10'],
        ];
    }
}
