<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->getTranslations('title'),
            'description'        => $this->getTranslations('description'),
            'course_type'        => $this->course_type,
            'category'           => $this->whenLoaded('category', fn () => [
                'id'   => $this->category->id,
                'name' => $this->category->getTranslations('name'),
            ]),
            'instructors'        => $this->whenLoaded('instructors',
                fn () => $this->instructors->map(fn ($i) => [
                    'id'   => $i->id,
                    'name' => $i->getTranslations('name'),
                ]),
            ),
            'image'              => $this->image ? $this->getFileUrl($this->image) : null,
            'intro_video'        => $this->intro_video,
            'hours'              => $this->hours,
            'language'           => $this->language,
            'level'              => $this->level,
            'price'              => $this->price,
            'currency'           => $this->currency,
            'certificate'        => (bool) $this->certificate,
            'active'             => (bool) $this->active,
            'for_public'         => (bool) $this->for_public,
            'is_evaluate'        => (bool) $this->is_evaluate,
            'outside_materials'  => (bool) $this->outside_materials,
            'allow_attendances'  => (bool) $this->allow_attendances,
            'created_at'         => $this->created_at?->format('Y-m-d'),
        ];
    }
}
