<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
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

        $request->setUserResolver(fn () => $tokenable);

        return $next($request);
    }
}
