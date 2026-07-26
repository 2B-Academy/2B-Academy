<?php

namespace App\Http\Middleware;

use App\Http\Traits\HelperTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiProtectMiddleware
{
    use HelperTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Read via config (not env()) so the key still resolves after
        // `php artisan config:cache` — under a cached config env() returns
        // null, which previously let a request with no API_KEY header pass
        // (null == null) and reach the user create/update/delete webhooks.
        $expected = (string) config('services.api_protect_key');
        $provided = (string) $request->header('API_KEY');

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return $this->errorResponse('عفواً - ليس لديك صلاحية الأستخدام', Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
