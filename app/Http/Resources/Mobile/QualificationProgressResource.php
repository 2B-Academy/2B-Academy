<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-05 qualification progress row — one per skill the user's job
 * title requires (or per skill the user is enrolled in, when no role
 * is attached).
 */
class QualificationProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;
        return [
            'id'                => (int) $row['id'],
            'name'              => (string) $row['name'],
            'total_courses'     => (int) $row['total_courses'],
            'completed_courses' => (int) $row['completed_courses'],
            'percent'           => (int) $row['percent'],
        ];
    }
}
