<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-05 attendance/session row.
 *
 * `attended` is computed by the repository against the attendances
 * table — not read from a snapshot. `time_from`/`time_to` are passed
 * through as-is for the client to format with its own RTL/timezone
 * rules.
 */
class MyLearningSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;

        return [
            'id'           => (int) $row->id,
            'title'        => $row->title,
            'session_date' => $row->session_date instanceof \Carbon\Carbon
                ? $row->session_date->format('Y-m-d')
                : $row->session_date,
            'time_from'    => $row->time_from,
            'time_to'      => $row->time_to,
            'attended'     => (bool) $row->attended,
        ];
    }
}
