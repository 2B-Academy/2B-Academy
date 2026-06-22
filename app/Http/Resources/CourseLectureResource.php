<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseLectureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'course_id'          => $this->course_id,
            'section_id'         => $this->section_id,

            // Localized strings — frontend already handles bilingual objects,
            // so we always emit the *full* JSON (en + ar) for both editor and
            // display contexts. This way the edit dialog can prefill every
            // language without a second round-trip.
            'title'              => $this->getTranslations('title'),
            'instructions'       => $this->getTranslations('instructions'),

            'content_type'       => $this->content_type ?? 'video',
            'learner_scope'      => $this->learner_scope ?? 'all',
            'session_id'         => $this->session_id,
            'duration_minutes'   => $this->duration_minutes,

            'type'               => $this->type,
            'video'              => $this->video,
            'file_name'          => $this->file_name,
            // Public URL when the lecture stores a real file; null otherwise.
            'file_url'           => $this->type === 'file' && $this->video
                ? $this->getFileUrl($this->video)
                : null,

            'require_completion' => (bool) ($this->require_completion ?? false),

            'created_at'         => $this->created_at?->format('Y-m-d'),
            'updated_at'         => $this->updated_at?->format('Y-m-d'),
        ];
    }
}
