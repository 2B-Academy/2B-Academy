<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->getTranslations('name'),
            'bio'           => $this->getTranslations('bio'),
            'job_title'     => $this->getTranslations('job_title'),
            'image'         => $this->image ? $this->getFileUrl($this->image) : null,
            'courses_count' => $this->whenCounted('courses'),
            'created_at'    => $this->created_at?->format('Y-m-d'),
        ];
    }
}
