<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'subject'          => $this->subject,
            'body'             => $this->body,
            'admin'            => $this->whenLoaded('admin', fn () => [
                'id'   => $this->admin->id,
                'name' => $this->admin->name,
            ]),
            'read_count'         => $this->read_count ?? null,
            'recipients_count'   => $this->total_recipients ?? $this->recipients_count ?? null,
            'total_recipients'   => $this->total_recipients ?? null,
            'preview'            => $this->body ? mb_substr(strip_tags($this->body), 0, 100) : '',
            'recipients_text'    => $this->total_recipients
                ? $this->total_recipients . ' recipients'
                : null,
            'created_at'       => $this->created_at?->toDateTimeString(),
            'recipients'       => $this->whenLoaded('recipients', fn () =>
                $this->recipients->map(fn ($recipient) => [
                    'user'    => [
                        'id'   => $recipient->user?->id,
                        'name' => $recipient->user?->name,
                    ],
                    'read_at' => $recipient->read_at?->toDateTimeString(),
                ])
            ),
        ];
    }
}
