<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LmsResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'type'          => $this->type,
            'content'       => $this->content,
            'url'           => $this->url,
            'file_path'     => $this->file_path
                ? Storage::disk('public')->url($this->file_path)
                : null,
            'file_name'     => $this->file_name,
            'file_size'     => $this->file_size,
            'qualification' => $this->whenLoaded('qualificationSkill', fn () => [
                'id'   => $this->qualificationSkill->id,
                'name' => $this->qualificationSkill->getTranslation('name', app()->getLocale()),
            ]),
            'created_by_name' => $this->whenLoaded('createdByAdmin', fn () => $this->createdByAdmin?->name),
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
