<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'rating'     => $this->rating,
            'review'     => $this->review,
            'user'       => $this->whenLoaded('user', fn () => [
                'id'           => $this->user->id,
                'name'         => $this->user->name,
                'machine_code' => $this->user->machine_code ?? null,
            ]),
            'course'     => $this->whenLoaded('course', fn () => [
                'id'          => $this->course->id,
                'title'       => $this->course->getTranslation('title', app()->getLocale()),
                'instructors' => $this->course->relationLoaded('instructors')
                    ? $this->course->instructors->map(fn ($i) => [
                        'id'   => $i->id,
                        'name' => $i->getTranslation('name', app()->getLocale()),
                    ])
                    : [],
            ]),
            'course_id'  => $this->course_id,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
