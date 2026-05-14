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
        if($request->header('API_KEY') != env('API_PROTECT_KEY'))
        {
            return $this->errorResponse('عفواً - ليس لديك صلاحية الأستخدام', Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
