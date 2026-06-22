<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the instructor-dashboard passcode widget state produced
 * by DashboardPasscodeService. The underlying resource is a plain
 * pre-composed array, so this resource is a thin, explicit contract
 * (keeps the shape documented + stable for the Angular client).
 *
 * @property array<string, mixed> $resource
 */
final class SessionPasscodeStateResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var array<string, mixed> $state */
        $state = $this->resource;

        return [
            'available'       => $state['available'] ?? false,
            'state'           => $state['state'] ?? 'idle',
            'passcode_length' => $state['passcode_length'] ?? null,
            // Rotating-passcode contract: when `rotates` is true the code
            // resets every `reset_seconds`, so the dashboard runs a live
            // countdown + auto re-issues a fresh code.
            'rotates'         => $state['rotates'] ?? false,
            'reset_seconds'   => $state['reset_seconds'] ?? null,
            'session'         => $state['session'] ?? null,
            'passcode'        => $state['passcode'] ?? null,
        ];
    }
}
