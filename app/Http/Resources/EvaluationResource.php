<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->getTranslation('title', app()->getLocale()),
            'type'        => $this->type,
            'is_required' => (bool) $this->is_required,
            'category'    => $this->whenLoaded('category', fn () => new EvaluationCategoryResource($this->category)),
            'created_at'  => $this->created_at?->format('Y-m-d'),
        ];
    }
}
