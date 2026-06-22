<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseExamQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'question' => $this->getTranslation('question', app()->getLocale()),
            'answers'  => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($a) => [
                'id'         => $a->id,
                'answer'     => $a->getTranslation('answer', app()->getLocale()),
                'is_correct' => (bool) $a->is_correct,
            ])),
        ];
    }
}
