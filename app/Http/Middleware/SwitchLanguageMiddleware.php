<?php

namespace App\Http\Middleware;

use Closure;

class SwitchLanguageMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('locale')) {
            session()->put('locale', 'ar');
        }

        app()->setLocale(session()->get('locale'));

        return $next($request);
    }
}