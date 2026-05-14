<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseLectureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'section_id' => $this->section_id,
            'title'      => $this->getTranslations('title'),
            'type'       => $this->type,
            'video'      => $this->video,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
