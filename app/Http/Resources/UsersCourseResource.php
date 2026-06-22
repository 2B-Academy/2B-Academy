<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'user'    => $this->whenLoaded('user', fn () => [
                'id'              => $this->user->id,
                'name'            => $this->user->name,
                'machine_code'    => $this->user->machine_code,
                'department_name' => $this->user->department_name,
            ]),
            'course'  => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->getTranslation('title', app()->getLocale()),
            ]),
            'group'   => $this->whenLoaded('group', fn () => $this->group
                ? ['id' => $this->group->id, 'name' => $this->group->getTranslation('name', app()->getLocale())]
                : null
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
