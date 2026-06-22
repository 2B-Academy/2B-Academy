<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * List-view resource for the admin Ratings overview, matching the Figma
 * table columns: Learner · Course · Instructor · Rating · Comment · Date.
 */
class AdminRatingListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        // The Figma layout shows a single instructor per row. Pick the first
        // instructor linked to the course (most courses only have one in this
        // dataset; for multi-instructor courses we expose the full list too).
        $instructors = $this->course && $this->course->relationLoaded('instructors')
            ? $this->course->instructors
            : collect();

        $primaryInstructor = $instructors->first();

        return [
            'id'          => $this->id,
            'rating'      => (int) $this->rating,
            'comment'     => $this->comment,
            'created_at'  => $this->created_at?->toIso8601String(),

            'user' => $this->whenLoaded('user', fn () => [
                'id'           => $this->user->id,
                'name'         => $this->user->name,
                'machine_code' => $this->user->machine_code ?? null,
            ]),

            'course' => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => method_exists($this->course, 'getTranslation')
                    ? $this->course->getTranslation('title', $locale)
                    : $this->course->title,
            ]),

            'instructor' => $primaryInstructor ? [
                'id'   => $primaryInstructor->id,
                'name' => method_exists($primaryInstructor, 'getTranslation')
                    ? $primaryInstructor->getTranslation('name', $locale)
                    : $primaryInstructor->name,
            ] : null,

            'instructors' => $instructors->map(fn ($i) => [
                'id'   => $i->id,
                'name' => method_exists($i, 'getTranslation')
                    ? $i->getTranslation('name', $locale)
                    : $i->name,
            ])->values(),
        ];
    }
}
