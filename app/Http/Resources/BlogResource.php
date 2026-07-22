<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Full blog shape: the card fields plus the ordered content sections and the
 * author bio needed by the reading page + the dashboard edit form.
 */
class BlogResource extends BlogListResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'author_user_id'          => $this->author_user_id,
            'is_anonymous'            => (bool) $this->is_anonymous,
            // Ids for the (multi-select) edit form to prefill.
            'qualification_skill_ids' => $this->qualificationSkills->pluck('id')->values(),
            'author_bio'              => $this->authorBio(),
            'sections'               => BlogSectionResource::collection(
                $this->whenLoaded('sections')
            ),
        ]);
    }

    /**
     * @return array{name: string, image: string|null, title: string|null}|null
     */
    protected function authorBio(): ?array
    {
        if ($this->is_anonymous || ! $this->author instanceof User) {
            return null;
        }

        return [
            'name'  => $this->author->getLocalizedName(),
            'image' => $this->author->image ? $this->getFileUrl($this->author->image) : null,
            'title' => $this->author->department_name ?? null,
        ];
    }
}
