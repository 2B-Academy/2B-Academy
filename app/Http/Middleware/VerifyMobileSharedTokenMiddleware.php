<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Mobile\MobileSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 📱 Mobile shared-token gate.
 *
 * The mobile API surface is consumed S2S by the HR system (and any
 * future integration). All callers share a single static token
 * persisted in the `settings` table under key
 * `mobile_shared_bearer_token`. This middleware:
 *
 *   1. Reads the token from one of (in order):
 *        - `X-Api-Token` header     (preferred, Swagger-UI friendly)
 *        - `X-Mobile-Token` header  (alias)
 *        - `Authorization: Bearer <token>` (also accepted, for HR
 *          systems that prefer the classic convention)
 *   2. Timing-safely compares it to the value held by
 *      App\Services\Mobile\MobileSettings::sharedBearerToken().
 *   3. Rejects the request with HTTP 401 + the standard JSON envelope
 *      if the header is missing or the value does not match.
 *
 * Rotation
 *   `UPDATE settings SET value='<new>' WHERE key='mobile_shared_bearer_token'`
 *   (or change it from the admin Settings UI), then call
 *   `MobileSettings::flush()` so the 10-minute cache is invalidated.
 *
 * This middleware does NOT identify a user — that responsibility
 * belongs to `ResolveMobileEmployeeMiddleware`, which runs next and
 * resolves the acting learner from the `Employee-Code` header.
 *
 * Route alias: `mobile.token` (registered in bootstrap/app.php).
 */
class VerifyMobileSharedTokenMiddleware
{
    public function __construct(
        private readonly MobileSettings $settings,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $presented = $this->extractToken($request);

        if ($presented === null || $presented === '') {
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.unauthenticated'),
                'errors'  => [],
            ], 401);
        }

        $expected = $this->settings->sharedBearerToken();

        if (!hash_equals($expected, $presented)) {
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.unauthenticated'),
                'errors'  => [],
            ], 401);
        }

        return $next($request);
    }

    /**
     * Pull the shared token out of whichever header the caller used.
     * Returns the raw token only — any accidental `Bearer ` prefix
     * (case-insensitive) is stripped so a developer pasting the value
     * with or without the prefix gets the same result.
     */
    private function extractToken(Request $request): ?string
    {
        $custom = $request->header('X-Api-Token')
            ?? $request->header('X-Mobile-Token');

        if (is_string($custom) && trim($custom) !== '') {
            return $this->stripBearer($custom);
        }

        $bearer = $request->bearerToken();
        if (is_string($bearer) && trim($bearer) !== '') {
            return $this->stripBearer($bearer);
        }

        return null;
    }

    private function stripBearer(string $value): string
    {
        $value = trim($value);
        if (stripos($value, 'bearer ') === 0) {
            return trim(substr($value, 7));
        }
        return $value;
    }
}
