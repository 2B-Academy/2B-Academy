<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * @param  string  $guard  'user' | 'admin' | 'any'
     */
    public function handle(Request $request, Closure $next, string $guard = 'any'): Response
    {
        $rawToken = $request->bearerToken();

        if (!$rawToken) {
            return $this->unauthenticated();
        }

        $tokenHash    = hash('sha256', $rawToken);
        $accessToken  = PersonalAccessToken::where('token', $tokenHash)->first();

        if (!$accessToken) {
            return $this->unauthenticated();
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            $accessToken->delete();
            return $this->unauthenticated(__('messages.token_expired'));
        }

        $tokenableType = $accessToken->tokenable_type;

        if ($guard === 'user' && $tokenableType !== User::class) {
            return $this->forbidden();
        }

        if ($guard === 'admin' && $tokenableType !== Admin::class) {
            return $this->forbidden();
        }

        $tokenable = $accessToken->tokenable;

        if (!$tokenable) {
            return $this->unauthenticated();
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        $request->setUserResolver(fn () => $tokenable);

        return $next($request);
    }

    private function unauthenticated(string $message = ''): Response
    {
        return response()->json([
            'status' => 'error',
            'error'  => $message ?: __('messages.unauthenticated'),
        ], 401);
    }

    private function forbidden(): Response
    {
        return response()->json([
            'status' => 'error',
            'error'  => __('messages.forbidden'),
        ], 403);
    }
}
