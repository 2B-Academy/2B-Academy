<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'system_id'       => $this->system_id,
            'machine_code'    => $this->machine_code,
            'department_name' => $this->department_name,
            'learner_type'    => $this->learner_type,
            'roles'           => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
