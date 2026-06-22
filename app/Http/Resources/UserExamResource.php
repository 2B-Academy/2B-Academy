<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'user_degree' => $this->user_degree,
            'status'      => $this->status,
            'course'      => $this->whenLoaded('course', fn () => [
                'id'          => $this->course->id,
                'title'       => $this->course->getTranslation('title', app()->getLocale()),
                'certificate' => (bool) $this->course->certificate,
            ]),
            'exam'        => $this->whenLoaded('exam', fn () => [
                'id'       => $this->exam->id,
                'title'    => $this->exam->getTranslation('title', app()->getLocale()),
                'degree'   => $this->exam->degree,
                'is_final' => (bool) $this->exam->is_final,
            ]),
            'answers'     => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($a) => [
                'question'   => $a->question,
                'answer'     => $a->answer,
                'is_correct' => (bool) $a->is_correct,
            ])),
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}
