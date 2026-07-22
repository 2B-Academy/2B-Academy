<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Blog shape for the dashboard create/edit form — exposes the FULL {en, ar}
 * translations for every translatable field plus the raw foreign keys, so the
 * form can populate both language inputs and every control.
 */
class AdminBlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'title'                  => $this->getTranslations('title'),
            'subtitle'               => $this->getTranslations('subtitle'),
            'slug'                   => $this->slug,
            'image'                  => $this->image, // raw stored path
            'image_url'              => $this->image ? $this->getFileUrl($this->image) : null,
            'level'                  => $this->level,
            'author_user_id'         => $this->author_user_id,
            'author_name'            => $this->author instanceof User ? $this->author->getLocalizedName() : null,
            'is_anonymous'           => (bool) $this->is_anonymous,
            'reading_time'           => $this->reading_time !== null ? (int) $this->reading_time : null,
            // Multiple qualifications: ids for the multi-select, plus a labelled
            // list for display.
            'qualification_skill_ids' => $this->qualificationSkills->pluck('id')->values(),
            'qualifications'          => $this->qualificationSkills->map(fn ($skill) => [
                'id'   => $skill->id,
                'name' => $skill->getTranslation('name', app()->getLocale()),
            ])->values(),
            'active'                 => (bool) $this->active,
            'published_at'           => $this->published_at?->format('Y-m-d'),
            'sections'               => AdminBlogSectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
