<?php

namespace App\Http\Middleware;

use Closure;

class SwitchLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('locale') == null) {
            $lang = session()->put('locale','ar');
            app()->setLocale($lang);
        }
        else
        {
            app()->setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}
