<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-02 scope chip — one of the three fixed filters `All` / `Special`
 * / `General`. The mobile client keys off `key` (stable, locale-free)
 * to drive the `scope` query param, and renders `label` for display.
 */
class AcademyScopeChipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;

        return [
            'key'    => (string) $row['key'],
            'label'  => (string) $row['label'],
            'count'  => (int) $row['count'],
            'is_all' => (bool) $row['is_all'],
        ];
    }
}
