<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->getTranslation('name', app()->getLocale()),
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
