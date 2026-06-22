<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'uuid'      => $this->uuid,
            'title'     => $this->getTranslation('title', app()->getLocale()),
            'duration'  => $this->duration,
            'full_mark' => $this->full_mark,
            'active'    => (bool) $this->active,
            'questions' => $this->whenLoaded('questions',
                fn () => FormQuestionResource::collection($this->questions)
            ),
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
