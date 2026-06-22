<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->getTranslation('name', app()->getLocale()),
            'description' => $this->getTranslation('description', app()->getLocale()),
            'image'       => $this->image ? $this->getFileUrl($this->image) : null,
            'active'      => (bool) $this->active,
            'created_at'  => $this->created_at?->format('Y-m-d'),
        ];
    }
}
