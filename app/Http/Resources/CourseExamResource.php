<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'section_id' => $this->section_id,
            'title'      => $this->getTranslation('title', app()->getLocale()),
            'degree'     => $this->degree,
            'is_final'   => (bool) $this->is_final,
            'questions'  => $this->whenLoaded('questions',
                fn () => CourseExamQuestionResource::collection($this->questions)
            ),
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
