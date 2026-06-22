<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Status follows the cohort calendar: a course is "active" the
        // day its first cohort starts and rolls back to "inactive" the
        // day after its last cohort ends. The persisted `active` column
        // remains as a hard manual override for courses with no cohorts
        // yet — see Course::effectiveStatus().
        $effectiveStatus = $this->resource->effectiveStatus();
        $effectiveActive = $effectiveStatus === 'active';

        return [
            'id'                 => $this->id,
            'title'              => $this->getTranslation('title', app()->getLocale()),
            'description'        => $this->getTranslation('description', app()->getLocale()),
            'course_type'        => $this->course_type,
            'category'           => $this->whenLoaded('category', fn () => [
                'id'   => $this->category->id,
                'name' => $this->category->getTranslation('name', app()->getLocale()),
            ]),
            'instructors'        => $this->whenLoaded('instructors',
                fn () => $this->instructors->map(fn ($i) => [
                    'id'   => $i->id,
                    'name' => $i->getTranslation('name', app()->getLocale()),
                ]),
            ),
            'qualification_skills' => $this->whenLoaded('qualificationSkills',
                fn () => $this->qualificationSkills->map(fn ($s) => [
                    'id'   => $s->id,
                    'name' => $s->getTranslation('name', app()->getLocale()),
                ]),
            ),
            'image'              => $this->image ? $this->getFileUrl($this->image) : null,
            'intro_video'        => $this->intro_video,
            'hours'              => $this->hours,
            'language'           => $this->language,
            'level'              => $this->level,
            'price'              => $this->price,
            'currency'           => $this->currency,
            'certificate'        => (bool) $this->certificate,
            'active'             => $effectiveActive,
            'stored_active'      => (bool) $this->active,
            'for_public'         => (bool) $this->for_public,
            'is_evaluate'        => (bool) $this->is_evaluate,
            'outside_materials'  => (bool) $this->outside_materials,
            'allow_attendances'  => (bool) $this->allow_attendances,
            'created_at'         => $this->created_at?->format('Y-m-d'),
            'updated_at'         => $this->updated_at?->format('Y-m-d'),
            'type'               => $this->course_type,
            'status'             => $effectiveStatus,
            'users_count'        => $this->users_count ?? null,
            'cohorts_count'      => $this->cohorts_count ?? null,
            // Aggregated by the list query (withAvg/withCount). We round to
            // one decimal so the table cell can format with `number:'1.1-1'`
            // without re-doing the math client-side.
            'rating'             => $this->rating_avg !== null
                ? round((float) $this->rating_avg, 1)
                : 0,
            'rating_count'       => (int) ($this->rating_count ?? 0),
            'instructor'         => $this->whenLoaded('instructors', function () {
                $first = $this->instructors->first();
                return $first ? [
                    'id'   => $first->id,
                    'name' => $first->getTranslation('name', app()->getLocale()),
                ] : null;
            }),
        ];
    }
}
