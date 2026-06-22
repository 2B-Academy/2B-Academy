<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Mobile;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Optional payload for S-03 enrolment.
 *
 * The service resolves the cohort automatically (first joinable
 * cohort: open seats + deadline in the future) — but the client can
 * *optionally* pin a specific cohort id when the course exposes more
 * than one upcoming round in the future and the screen lets the
 * learner choose.
 */
final class EnrolmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cohort_id' => ['nullable', 'integer', 'min:1', 'exists:course_sections,id'],
        ];
    }
}
