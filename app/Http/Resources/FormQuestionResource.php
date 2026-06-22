<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'type'     => $this->type,
            'question' => $this->getTranslation('question', app()->getLocale()),
            'answers'  => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($a) => [
                'id'      => $a->id,
                'answer'  => $a->getTranslation('answer', app()->getLocale()),
                'is_true' => (bool) $a->is_true,
            ])),
        ];
    }
}
