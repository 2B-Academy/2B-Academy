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
            'recipients_text'            => $this->total_recipients
                ? $this->total_recipients . ' recipients'
                : null,
            'has_learner_recipients'     => (int) ($this->learner_recipients_count    ?? 0) > 0,
            'has_instructor_recipients'  => (int) ($this->instructor_recipients_count ?? 0) > 0,
            'created_at'       => $this->created_at?->toDateTimeString(),
            'recipients'       => $this->whenLoaded('recipients', fn () =>
                $this->recipients->map(function ($recipient) {
                    $isInstructor = $recipient->instructor_id !== null;
                    $person       = $isInstructor ? $recipient->instructor : $recipient->user;
                    $name         = $person
                        ? (method_exists($person, 'getLocalizedName')
                            ? $person->getLocalizedName()
                            : $person->getTranslation('name', app()->getLocale()))
                        : null;
                    return [
                        'id'           => $person?->id,
                        'name'         => $name,
                        'is_instructor' => $isInstructor,
                        'read_at'      => $recipient->read_at?->toDateTimeString(),
                    ];
                })
            ),
        ];
    }
}
