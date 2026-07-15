<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Section shape for the dashboard editor — exposes the FULL {en, ar}
 * translations (not the locale-resolved string) so the form can round-trip
 * both languages.
 */
class AdminBlogSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->getTranslations('title'),
            'image'      => $this->image, // raw stored path — round-trips back on edit
            'image_url'  => $this->image ? $this->getFileUrl($this->image) : null,
            'body'       => $this->getTranslations('body'),
            'quote'      => $this->getTranslations('quote'),
            'sort_order' => (int) $this->sort_order,
        ];
    }
}
