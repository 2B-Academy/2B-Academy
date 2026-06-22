<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Identity block for the authenticated learner.
 *
 * 📱 MOBILE · Identity card — used as a sub-block on every mobile
 * response where the audit/business identity is contextually relevant
 * (S-04 enrolment confirmation, S-05 My Learning header, S-06 mark
 * present echo, S-07 certificate cards).
 *
 * `machine_code` is the canonical business identity for an employee
 * (HR-sourced, denormalized on every attendance / form / assignment
 * audit row). The mobile client uses it as the cross-reference key
 * back into the HR system and renders it on:
 *
 *   - the My Learning header card,
 *   - every certificate visual,
 *   - the Mark Present success snackbar (so the learner can verify
 *     the attendance was logged under THEIR HR id),
 *   - the enrolment confirmation receipt.
 *
 * `id` is also exposed for internal debugging but the client should
 * NEVER use it as a business key — only `machine_code` is stable
 * across HR resyncs.
 */
class LearnerIdentityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user   = $this->resource;
        $locale = app()->getLocale();

        $jobTitle = $user->relationLoaded('jobTitle') || method_exists($user, 'jobTitle')
            ? $user->jobTitle
            : null;

        return [
            'id'              => (int) $user->id,
            'machine_code'    => $user->machine_code,
            'name'            => $user->name,
            'email'           => $user->email,
            'image'           => $this->absoluteUrl($user->image ?? null),
            'department_name' => $user->department_name,
            'job_title'       => $jobTitle ? [
                'id'   => (int) $jobTitle->id,
                'name' => $this->safeTranslate($jobTitle->name, $locale),
            ] : null,
            'learner_type'    => $user->learner_type ?? null,
        ];
    }

    private function absoluteUrl(?string $path): ?string
    {
        if ($path === null || $path === '') return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    private function safeTranslate(mixed $value, string $locale): string
    {
        if (is_array($value)) {
            return (string) ($value[$locale] ?? ($value['en'] ?? ($value['ar'] ?? '')));
        }
        $decoded = is_string($value) ? json_decode($value, true) : null;
        if (is_array($decoded)) {
            return (string) ($decoded[$locale] ?? ($decoded['en'] ?? ($decoded['ar'] ?? '')));
        }
        return (string) $value;
    }
}
