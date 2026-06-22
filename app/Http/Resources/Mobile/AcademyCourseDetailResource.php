<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\CourseCtaState;
use App\Enums\Mobile\RatingSentiment;
use App\Models\Course;
use App\Services\Mobile\AcademyService;
use App\Services\Mobile\MobileSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-03 detail screen payload. Combines course meta, anchor cohort,
 * full cohort list, ordered units, instructors, qualifications, and
 * the typed CTA state so the client doesn't have to derive any of it.
 *
 * The CTA state + anchor cohort are passed in via the `additional()`
 * call from the controller — that's the cleanest way to inject
 * per-user view-model state into a resource without polluting the
 * domain model.
 */
class AcademyCourseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Course $course */
        $course   = $this->resource;
        $locale   = app()->getLocale();
        $academy  = app(AcademyService::class);
        $settings = app(MobileSettings::class);

        $anchorCohort = $this->additional['anchor_cohort'] ?? null;
        $ctaState     = $this->additional['cta_state'] ?? CourseCtaState::Unavailable;

        $ratingAvg   = (float) ($course->rating_avg ?? 0);
        $ratingCount = (int)   ($course->rating_count ?? 0);

        return [
            'id'              => (int) $course->id,
            'title'           => $course->getTranslation('title', $locale),
            'description'     => $course->getTranslation('description', $locale),
            'course_type'     => $course->course_type,
            'image'           => $this->absoluteUrl($course->image),
            'hours'           => (int) ($course->hours ?? 0),
            'has_certificate' => (bool) $course->certificate,
            'allow_attendance'=> (bool) ($course->allow_attendances ?? false),

            'category'        => $course->category ? [
                'id'   => (int) $course->category->id,
                'name' => $course->category->getTranslation('name', $locale),
            ] : null,

            'instructors'     => $course->instructors->map(fn ($i) => [
                'id'    => (int) $i->id,
                'name'  => (string) $i->name,
                'image' => $this->absoluteUrl($i->image),
            ])->values(),

            'qualifications'  => $course->qualificationSkills->map(fn ($q) => [
                'id'   => (int) $q->id,
                'name' => $this->safeTranslate($q->name, $locale),
            ])->values(),

            'rating' => [
                'avg'   => round($ratingAvg, 2),
                'count' => $ratingCount,
                'sentiment' => RatingSentiment::fromRating(
                    (int) round($ratingAvg),
                    $settings->ratingMinValue(),
                    $settings->ratingMaxValue(),
                )->value,
            ],

            'enrolled_users_count' => (int) ($course->users_count ?? 0),

            'units' => AcademyCourseUnitResource::collection($course->lectures ?? collect()),

            'cohorts' => AcademyCohortBlockResource::collection($course->sections ?? collect()),

            'anchor_cohort' => $anchorCohort
                ? (new AcademyCohortBlockResource($anchorCohort))->toArray($request)
                : null,

            'cta' => [
                'state'    => $ctaState instanceof CourseCtaState ? $ctaState->value : (string) $ctaState,
                'label_key'=> $ctaState instanceof CourseCtaState
                    ? "enums.course_cta_state.{$ctaState->value}"
                    : '',
                'enabled'  => $ctaState instanceof CourseCtaState
                    ? ($ctaState->isInteractive() || $ctaState === CourseCtaState::GetNotified)
                    : false,
            ],
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
