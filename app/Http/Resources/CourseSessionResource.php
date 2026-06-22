<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'course_id'    => $this->course_id,
            'section'      => $this->whenLoaded('section', fn () => [
                'id'   => $this->section->id,
                'name' => $this->section->getTranslation('name', app()->getLocale()),
            ]),
            'title'        => $this->title,
            'session_date' => $this->session_date,
            'time_from'    => $this->time_from,
            'time_to'      => $this->time_to,
            'location'     => $this->location,
            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
