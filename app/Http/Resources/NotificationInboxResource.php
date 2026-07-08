<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationInboxResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $data   = (array) $this->data;

        return [
            'id'         => $this->id,
            'type'       => $data['type'] ?? null,
            'title'      => $data["title_{$locale}"] ?? $data['title_en'] ?? null,
            'body'       => $data["body_{$locale}"]  ?? $data['body_en']  ?? null,
            'meta'       => $data['meta'] ?? [],
            'read_at'    => $this->read_at?->format('Y-m-d H:i'),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
