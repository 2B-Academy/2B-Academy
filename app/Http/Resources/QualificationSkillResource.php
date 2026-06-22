<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualificationSkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->getTranslation('name', app()->getLocale()),
            'name_en'        => $this->getTranslation('name', 'en'),
            'name_ar'        => $this->getTranslation('name', 'ar'),
            'courses_count'  => $this->whenCounted('courses'),
            'enrolled_count' => (int) ($this->enrolled_count ?? 0),
            'created_at'     => $this->created_at?->format('Y-m-d'),
        ];
    }
}
