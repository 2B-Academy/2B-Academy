<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'about'   => $this->getTranslation('about', app()->getLocale()),
            'mission' => $this->getTranslation('mission', app()->getLocale()),
            'vision'  => $this->getTranslation('vision', app()->getLocale()),
            'goals'   => $this->getTranslation('goals', app()->getLocale()),
            'image'   => $this->image ? $this->getFileUrl($this->image) : null,
        ];
    }
}
