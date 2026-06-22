<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseLectureQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'question' => $this->question,
            'answer'   => $this->answer,
            'user'     => $this->whenLoaded('user', fn () => [
                'id'           => $this->user->id,
                'name'         => $this->user->name,
                'machine_code' => $this->user->machine_code,
            ]),
            'course'   => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->getTranslation('title', app()->getLocale()),
            ]),
            'lecture'  => $this->whenLoaded('lecture', fn () => [
                'id'    => $this->lecture->id,
                'title' => $this->lecture->getTranslation('title', app()->getLocale()),
            ]),
            'answered_by' => $this->whenLoaded('answeredBy', fn () => $this->answeredBy?->name),
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}
