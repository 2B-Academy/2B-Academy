<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Card / management-row shape for a blog (no sections).
 */
class BlogListResource extends JsonResource
{
    /** Organisation label shown when a blog is published anonymously. */
    private const ANONYMOUS_AUTHOR = 'NAS';

    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'            => $this->id,
            'title'         => $this->getTranslation('title', $locale),
            'subtitle'      => $this->subtitle ? $this->getTranslation('subtitle', $locale) : null,
            'slug'          => $this->slug,
            'image'         => $this->image ? $this->getFileUrl($this->image) : null,
            'level'         => $this->level,
            'reading_time'  => $this->reading_time !== null ? (int) $this->reading_time : null,
            'qualification' => $this->qualificationSkill
                ? [
                    'id'   => $this->qualificationSkill->id,
                    'name' => $this->qualificationSkill->getTranslation('name', $locale),
                ]
                : null,
            'author'        => $this->authorPayload(),
            'added_by'      => $this->creator?->name,
            'published_at'  => $this->published_at?->format('Y-m-d'),
            'created_at'    => $this->created_at?->format('Y-m-d'),
            'active'        => (bool) $this->active,
        ];
    }

    /**
     * @return array{name: string, image: string|null, is_anonymous: bool}
     */
    protected function authorPayload(): array
    {
        $isAnonymous = (bool) $this->is_anonymous;

        $name = $isAnonymous
            ? self::ANONYMOUS_AUTHOR
            : ($this->author instanceof User ? $this->author->getLocalizedName() : self::ANONYMOUS_AUTHOR);

        $image = (! $isAnonymous && $this->author && $this->author->image)
            ? $this->getFileUrl($this->author->image)
            : null;

        return [
            'name'         => $name,
            'image'        => $image,
            'is_anonymous' => $isAnonymous,
        ];
    }
}
