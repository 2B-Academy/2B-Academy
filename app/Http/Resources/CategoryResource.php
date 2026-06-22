<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->getTranslation('name', app()->getLocale()),
            'logo'         => $this->logo ? $this->getFileUrl($this->logo) : null,
            'active'       => (bool) $this->active,
            'courses_count'=> $this->whenCounted('courses'),
            'created_at'   => $this->created_at?->format('Y-m-d'),
        ];
    }
}
