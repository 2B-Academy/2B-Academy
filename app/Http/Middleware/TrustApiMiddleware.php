<?php

namespace App\Http\Middleware;

use App\Http\Traits\HelperTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustApiMiddleware
{
    use HelperTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('app.API_SECRET_KEY');
        if (!empty($apiKey) && $request->header('API_SECRET_KEY') == $apiKey)
        {
            return $next($request);
        }
       return $this->errorResponse('غير مسموح');
    }
}
