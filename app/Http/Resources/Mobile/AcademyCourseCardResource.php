<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\RatingSentiment;
use App\Models\Course;
use App\Services\Mobile\AcademyService;
use App\Services\Mobile\MobileSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-02 course card. Renders the "Available courses" feed.
 *
 * Resolves the "next cohort" inline so the card can show the start
 * date / seats-left badge without a second request, and surfaces the
 * deadline severity flag so the UI can colour the seats chip directly.
 */
class AcademyCourseCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Course $course */
        $course   = $this->resource;
        $locale   = app()->getLocale();
        $academy  = app(AcademyService::class);
        $settings = app(MobileSettings::class);
        $now      = now();

        // Pick the next joinable cohort *from the already-loaded
        // sections* if the repository pre-loaded them, otherwise fall
        // back to a query.
        $sections = $course->relationLoaded('sections')
            ? $course->sections
            : collect();

        $nextCohort = $sections
            ->filter(function ($section) use ($now) {
                if ($section->status === 'inactive') return false;
                if ($section->start_date === null)   return false;
                return $section->start_date->gte($now->copy()->startOfDay());
            })
            ->sortBy('start_date')
            ->first();

        $deadlineSeverity = $nextCohort ? $academy->deadlineSeverity($nextCohort, $now) : 'none';
        $daysUntilStart   = $nextCohort && $nextCohort->start_date
            ? (int) $now->copy()->startOfDay()->diffInDays($nextCohort->start_date->startOfDay(), false)
            : null;

        $ratingAvg   = (float) ($course->rating_avg ?? 0);
        $ratingCount = (int)   ($course->rating_count ?? 0);

        return [
            'id'               => (int) $course->id,
            'title'            => (string) $course->getTranslation('title', $locale),
            'description'      => $course->getTranslation('description', $locale),
            'course_type'      => $course->course_type,
            'image'            => $this->absoluteUrl($course->image),
            'hours'            => (int) ($course->hours ?? 0),
            'has_certificate'  => (bool) $course->certificate,
            'category'         => $course->relationLoaded('category') && $course->category
                ? [
                    'id'   => (int) $course->category->id,
                    'name' => $course->category->getTranslation('name', $locale),
                ]
                : null,
            'instructors'      => $course->relationLoaded('instructors')
                ? $course->instructors->map(fn ($i) => [
                    'id'    => (int) $i->id,
                    'name'  => (string) $i->name,
                    'image' => $this->absoluteUrl($i->image),
                ])->values()
                : [],
            'qualifications'   => $course->relationLoaded('qualificationSkills')
                ? $course->qualificationSkills->map(fn ($q) => [
                    'id'   => (int) $q->id,
                    'name' => $this->safeTranslate($q->name, $locale),
                ])->values()
                : [],
            'rating'           => [
                'avg'      => round($ratingAvg, 2),
                'count'    => $ratingCount,
                'sentiment'=> RatingSentiment::fromRating(
                    (int) round($ratingAvg),
                    $settings->ratingMinValue(),
                    $settings->ratingMaxValue(),
                )->value,
            ],
            'next_cohort'      => $nextCohort ? [
                'id'             => (int) $nextCohort->id,
                'name'           => $nextCohort->getTranslation('name', $locale),
                'start_date'     => $nextCohort->start_date?->format('Y-m-d'),
                'end_date'       => $nextCohort->end_date?->format('Y-m-d'),
                'capacity'       => $nextCohort->capacity !== null ? (int) $nextCohort->capacity : null,
                'enrolled_count' => (int) ($nextCohort->enrolled_count
                    ?? \DB::table('users_courses')->where('group_id', $nextCohort->id)->count()),
                'seats_left'     => $nextCohort->capacity !== null
                    ? max(0, (int) $nextCohort->capacity - (int) ($nextCohort->enrolled_count
                        ?? \DB::table('users_courses')->where('group_id', $nextCohort->id)->count()))
                    : null,
                'enrolment_closes_at' => $academy->effectiveDeadline($nextCohort)?->toDateString(),
                'days_until_deadline' => $academy->daysUntilDeadline($nextCohort, $now),
                'days_until_start'    => $daysUntilStart,
                'deadline_severity'   => $deadlineSeverity,
            ] : null,
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
