<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Mobile;

use App\Enums\EnumRegistry;
use App\Enums\Mobile\CourseDurationBucket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * S-02 Catalogue list query params (`GET .../academy/courses`).
 *
 * `category_id` / `search` / `per_page` / `scope` predate this request
 * class — this action used to take a plain `Request`. They're kept
 * deliberately LOOSE (no type/enum rules) so the already-shipped mobile
 * app contract (`routes/apis/mobile.php` routes the exact same
 * `AcademyController::courses` action) never starts 422-ing on a value
 * it has always been able to send; `AcademyService` already whitelists
 * `scope` itself (`normaliseScope()`), collapsing anything unrecognised
 * to `all` exactly like before this change.
 *
 * The NEW learner-web Catalogue filters (`level` / `type` / `duration` /
 * `job_role_id` / `sort`) are brand new surface, so they ARE strictly
 * whitelisted here — every value is re-validated defensively again in
 * `AcademyService` (`normaliseLevels()` etc.) before it reaches SQL.
 */
final class AcademyCoursesListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Accept every multi-value filter as either a real query array
     * (`level[]=beginner&level[]=intermediate`) or a comma-separated
     * string (`level=beginner,intermediate`), since the Angular client
     * may reasonably send either depending on how its HttpParams are
     * built.
     */
    protected function prepareForValidation(): void
    {
        foreach (['level', 'type', 'duration', 'job_role_id'] as $key) {
            $value = $this->query($key);

            if (is_string($value) && $value !== '') {
                $this->query->set($key, array_values(array_filter(
                    array_map('trim', explode(',', $value)),
                    static fn (string $v) => $v !== '',
                )));
            }
        }
    }

    public function rules(): array
    {
        return [
            // Pre-existing, intentionally loose (see class docblock).
            'category_id' => ['nullable'],
            'search'      => ['nullable'],
            'per_page'    => ['nullable'],
            'scope'       => ['nullable'],

            // New filters.
            'level'         => ['nullable', 'array'],
            'level.*'       => ['string', Rule::in(EnumRegistry::values('course_level'))],

            'type'          => ['nullable', 'array'],
            'type.*'        => ['string', Rule::in(EnumRegistry::values('course_type'))],

            'duration'      => ['nullable', 'array'],
            'duration.*'    => ['string', Rule::in(CourseDurationBucket::values())],

            'job_role_id'   => ['nullable', 'array'],
            'job_role_id.*' => ['integer', 'min:1', 'exists:job_titles,id'],

            'sort' => ['nullable', 'string', Rule::in(['most_relevant', 'highest_rated', 'soonest_start', 'newest'])],
        ];
    }
}
