<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->getTranslation('title', app()->getLocale()),
            'body'       => $this->getTranslation('body', app()->getLocale()),
            'for_public' => (bool) $this->for_public,
            'users'      => $this->whenLoaded('users', fn () => $this->users->pluck('user_code')),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
