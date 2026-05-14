<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->getTranslations('name'),
            'lectures' => $this->whenLoaded('lectures', fn () => CourseLectureResource::collection($this->lectures)),
            'exams'    => $this->whenLoaded('exams', fn () => CourseExamResource::collection($this->exams)),
        ];
    }
}
