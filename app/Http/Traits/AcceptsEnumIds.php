<?php

declare(strict_types=1);

namespace App\Http\Traits;

use App\Enums\EnumRegistry;

/**
 * Drop-in helper for `FormRequest::prepareForValidation()` that lets a
 * payload arrive with EITHER:
 *
 *   - the numeric dropdown id surfaced by `/api/v1/enums/{name}` (e.g. 3), or
 *   - the legacy string code stored in the DB (e.g. "hybrid").
 *
 * The trait normalizes the field to the string code BEFORE the validator
 * runs, so existing rules like `'in:online,offline,hybrid,external_link'`
 * keep working without changes.
 *
 * Usage:
 *
 *   class CourseRequest extends FormRequest
 *   {
 *       use AcceptsEnumIds;
 *
 *       protected function enumFieldMap(): array
 *       {
 *           return [
 *               'course_type' => 'course_type',
 *               // 'status'   => 'cohort_status',
 *           ];
 *       }
 *
 *       protected function prepareForValidation(): void
 *       {
 *           $this->normalizeEnumIdsToCodes();
 *           // ...existing prep...
 *       }
 *   }
 */
trait AcceptsEnumIds
{
    /**
     * Walk every `request_field => enum_name` pair declared by the consumer
     * and, if the incoming value is a numeric id from the registry, swap it
     * for the underlying string code in-place via `Request::merge`.
     */
    protected function normalizeEnumIdsToCodes(): void
    {
        $map = method_exists($this, 'enumFieldMap') ? $this->enumFieldMap() : [];
        if (! is_array($map) || $map === []) {
            return;
        }

        $merge = [];
        foreach ($map as $field => $enumName) {
            if (! $this->has($field) || ! EnumRegistry::exists($enumName)) {
                continue;
            }

            $raw = $this->input($field);
            if ($raw === null || $raw === '') {
                continue;
            }

            $code = $this->normalizeOneValue($enumName, $raw);
            if ($code !== null) {
                $merge[$field] = $code;
            }
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * Convert a single dropdown value to its string code.
     *   - numeric (or numeric-string) → looked up by id
     *   - existing string in the registry → returned as-is
     *   - anything else → null (validator will reject it)
     */
    private function normalizeOneValue(string $enumName, mixed $raw): ?string
    {
        $codes = EnumRegistry::values($enumName);

        if (is_int($raw) || (is_string($raw) && ctype_digit($raw))) {
            $id = (int) $raw;
            return $codes[$id - 1] ?? null;
        }

        if (is_string($raw) && in_array($raw, $codes, true)) {
            return $raw;
        }

        return null;
    }
}
