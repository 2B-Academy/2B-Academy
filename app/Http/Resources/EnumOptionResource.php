<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * One dropdown option for an enum exposed under `/api/v1/enums`.
 *
 * `id`    — numeric 1-indexed identifier; what the frontend POSTs/PUTs back.
 * `value` — localized display label, resolved against the active
 *           `Accept-Language` (en | ar).
 * `code`  — underlying string machine code (e.g. "online"). Surfaced so
 *           clients that still want the legacy string can access it without
 *           a second round-trip.
 * `description` — optional localized helper text (only set for enums that
 *           opt in via `EnumRegistry::hasDescriptions()`, e.g. certificate basis).
 */
class EnumOptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var array{id: int, value: string, code: string, description: ?string} $row */
        $row = (array) $this->resource;

        $data = [
            'id'    => (int) $row['id'],
            'value' => $row['value'],
            'code'  => $row['code'],
        ];

        if (! empty($row['description'])) {
            $data['description'] = $row['description'];
        }

        return $data;
    }
}
