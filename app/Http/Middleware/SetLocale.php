<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    const ALLOWED_LOCALIZATIONS = ['en', 'ar'];
    const DEFAULT_LOCALE        = 'ar';

    public function handle(Request $request, Closure $next)
    {
        $raw = $request->header('Accept-Language')
            ?? $request->header('culture')
            ?? $request->header('LANGUAGE')
            ?? '';

        // Strip region suffix and quality values: "ar-EG,ar;q=0.9" → "ar"
        $locale = strtolower(explode('-', explode(',', (string) $raw)[0])[0]);

        $locale = in_array($locale, self::ALLOWED_LOCALIZATIONS, true)
            ? $locale
            : self::DEFAULT_LOCALE;

        app()->setLocale($locale);

        return $next($request);
    }
}
