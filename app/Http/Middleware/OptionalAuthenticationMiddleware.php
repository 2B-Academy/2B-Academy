<?php

namespace App\Http\Middleware;

use App\Http\Traits\TracksLastActive;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional Sanctum authentication.
 *
 * Mirrors {@see AuthenticationMiddleware} when a valid bearer token is
 * present — it resolves the tokenable onto the request so downstream code
 * sees `$request->user()` — but, unlike it, never rejects the request when
 * the token is missing, invalid, or expired. In those cases the request
 * simply proceeds as a guest with a null user.
 *
 * Used by public browse endpoints (e.g. the learner Academy catalogue) that
 * must serve not-logged-in visitors general/public content while still
 * personalising the response for an authenticated learner.
 */
class OptionalAuthenticationMiddleware
{
    use TracksLastActive;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $next($request);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        // Unknown or expired token → treat as a guest rather than 401, so a
        // stale token can never block public browsing.
        if (! $accessToken
            || ($accessToken->expires_at && $accessToken->expires_at->isPast())
        ) {
            return $next($request);
        }

        $tokenable = $accessToken->tokenable;

        if (! $tokenable) {
            return $next($request);
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        if ($this->isLastActiveStale($tokenable)) {
            $this->stampLastActive($tokenable, force: true);
            $this->stampLastActiveByEmail($tokenable->email ?? null);
        }

        $request->setUserResolver(fn () => $tokenable);

        return $next($request);
    }
}
