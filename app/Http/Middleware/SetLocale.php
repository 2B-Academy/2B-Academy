<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    const ALLOWED_LOCALIZATIONS = ['en', 'ar'];
    public function handle(Request $request, Closure $next)
    {
        $localization = $request->header('LANGUAGE');
        $localization = in_array($localization, self::ALLOWED_LOCALIZATIONS, true) ? $localization : 'ar';
        app()->setLocale($localization);
        return $next($request);
    }
}
