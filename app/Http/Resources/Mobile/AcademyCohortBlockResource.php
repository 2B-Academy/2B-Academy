<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Models\Course;
use App\Models\CourseSection;
use App\Services\Mobile\AcademyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-03 cohort/dates block. Shown both above the sticky CTA and inside
 * the cohort selector sheet.
 */
class AcademyCohortBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CourseSection $cohort */
        $cohort    = $this->resource;
        $locale    = app()->getLocale();
        $academy   = app(AcademyService::class);
        $now       = now();
        $enrolled  = (int) ($cohort->enrolled_count
            ?? \DB::table('users_courses')->where('group_id', $cohort->id)->count());
        $capacity  = $cohort->capacity !== null ? (int) $cohort->capacity : null;
        $seatsLeft = $capacity !== null ? max(0, $capacity - $enrolled) : null;
        $deadline  = $academy->effectiveDeadline($cohort);

        return [
            'id'                  => (int) $cohort->id,
            'name'                => $cohort->getTranslation('name', $locale),
            'effective_status'    => Course::deriveCohortStatus(
                $cohort->status,
                $cohort->start_date instanceof \Carbon\Carbon ? $cohort->start_date : null,
                $cohort->end_date instanceof \Carbon\Carbon   ? $cohort->end_date   : null,
            ),
            'start_date'          => $cohort->start_date?->format('Y-m-d'),
            'end_date'            => $cohort->end_date?->format('Y-m-d'),
            'capacity'            => $capacity,
            'enrolled_count'      => $enrolled,
            'seats_left'          => $seatsLeft,
            'is_full'             => $capacity !== null && $enrolled >= $capacity,
            'enrolment_closes_at' => $deadline?->toDateString(),
            'days_until_deadline' => $academy->daysUntilDeadline($cohort, $now),
            'deadline_severity'   => $academy->deadlineSeverity($cohort, $now),
            'sessions'            => $cohort->relationLoaded('sessions')
                ? $cohort->sessions->map(fn ($s) => [
                    'id'           => (int) $s->id,
                    'title'        => $s->title,
                    'session_date' => $s->session_date instanceof \Carbon\Carbon
                        ? $s->session_date->format('Y-m-d')
                        : $s->session_date,
                    'time_from'    => $s->time_from,
                    'time_to'      => $s->time_to,
                    'location'     => $s->location,
                ])->values()
                : [],
        ];
    }
}
