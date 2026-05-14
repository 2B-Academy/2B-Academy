<?php

namespace App\Http\Middleware;

use App\Models\Mainlog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request method is POST, PUT, or DELETE
        if ($request->method() != 'GET') {
            Mainlog::create([
                'admin_id' => auth()->guard('admin')->user()->id,
                'admin_email' => auth()->guard('admin')->user()->email,
                'url' => $request->url(),
                'method' => $request->method(),
                'payload' => json_encode($request->all()), // Log the request body data
            ]);
        }
        return $next($request);
    }
}
