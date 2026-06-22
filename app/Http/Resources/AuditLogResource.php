<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'user_type'   => $this->user_type,
            'user_name'   => $this->user_name,
            'action'      => $this->action,
            'model_type'  => $this->model_type,
            'model_id'    => $this->model_id,
            'description' => $this->description,
            'ip_address'  => $this->ip_address,
            'created_at'  => $this->created_at,
        ];
    }
}
