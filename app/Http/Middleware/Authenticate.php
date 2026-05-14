<?php

namespace App\Http\Middleware;

use App\Http\Traits\HelperTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    use HelperTrait;
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if( $request->is('api/*')){
            return $request->expectsJson() ? null : $this->errorResponse('غير مسموح');
        }else{
            return $request->expectsJson() ? null : (($request->is('admin/*')) ? route('admin.login_page') : route('front.auth.login'));
        }

    }
}
