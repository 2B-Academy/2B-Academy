<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'         => $this->id,
            'title'      => $this->getTranslation('title', $locale),
            'image'      => $this->image ? $this->getFileUrl($this->image) : null,
            'body'       => $this->getTranslation('body', $locale),
            'quote'      => $this->quote ? $this->getTranslation('quote', $locale) : null,
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
