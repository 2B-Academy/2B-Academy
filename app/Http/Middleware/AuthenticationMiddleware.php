<?php

namespace App\Http\Middleware;

use App\Http\Traits\TracksLastActive;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
    use TracksLastActive;

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            $accessToken->delete();
            return response()->json([
                'status' => 'error',
                'message' => __('messages.token_expired'),
            ], 401);
        }

        $tokenable = $accessToken->tokenable;

        if (!$tokenable) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        // Stamp activity for ANY authenticated model (learner / instructor /
        // admin). Throttle on the model itself, and when it's actually due
        // for a refresh, also stamp any sibling rows sharing the same email
        // (e.g. the instructors row for a person who signs in as a user).
        if ($this->isLastActiveStale($tokenable)) {
            $this->stampLastActive($tokenable, force: true);
            $this->stampLastActiveByEmail($tokenable->email ?? null);
        }

        $request->setUserResolver(fn () => $tokenable);

        return $next($request);
    }
}
