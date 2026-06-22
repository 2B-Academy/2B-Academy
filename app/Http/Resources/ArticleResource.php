<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'title'        => $this->getTranslation('title', app()->getLocale()),
            'description'  => $this->getTranslation('description', app()->getLocale()),
            'slug'         => $this->slug,
            'date_publish' => $this->date_publish,
            'image'        => $this->image ? $this->getFileUrl($this->image) : null,
            'is_home'      => (bool) $this->is_home,
            'active'       => (bool) $this->active,
            'created_at'   => $this->created_at?->format('Y-m-d'),
        ];
    }
}
