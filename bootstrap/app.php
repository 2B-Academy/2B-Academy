<?php

use App\Http\Middleware\AdminLogMiddleware;
use App\Http\Middleware\ApiProtectMiddleware;
use App\Http\Middleware\AuthenticationMiddleware;
use App\Http\Middleware\ResolveMobileEmployeeMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SwitchLanguageMiddleware;
use App\Http\Middleware\TrustApiMiddleware;
use App\Http\Middleware\VerifyMobileSharedTokenMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            // Web routes (frontend public)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // User auth routes
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            // Admin panel routes
            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            // API routes — versioned at /api/v1
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Append custom middleware to the web group
        $middleware->web(append: [
            SwitchLanguageMiddleware::class,
        ]);

        // Append custom middleware to the api group
        $middleware->api(append: [
            SetLocale::class,
        ]);

        // Guests hitting protected routes are redirected to the appropriate
        // login page. There is no `login` named route in this project; admin
        // routes use `admin.login_page` and the user area uses `front.auth.login`.
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return $request->is('admin/*')
                ? route('admin.login_page')
                : route('front.auth.login');
        });

        // Named middleware aliases
        $middleware->alias([
            // API authentication — validates Sanctum bearer token
            'auth.user'          => AuthenticationMiddleware::class,
            // API role check — role:Admin | role:User | role:Admin,User
            'role'               => RoleMiddleware::class,
            // Spatie permission package middlewares (Laravel 11 no longer
            // auto-registers these; the admin panel relies on `permission:*`)
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            // Legacy / Blade middleware
            'admin.logs'         => AdminLogMiddleware::class,
            'api-protect'        => ApiProtectMiddleware::class,
            'switch-language'    => SwitchLanguageMiddleware::class,
            'language'           => SetLocale::class,
            'trust'              => TrustApiMiddleware::class,
            // 📱 Mobile API (S2S, HR integration) — shared bearer token
            // gate + employee resolver. Used in pairs:
            // ->middleware(['mobile.token', 'mobile.employee'])
            'mobile.token'       => VerifyMobileSharedTokenMiddleware::class,
            'mobile.employee'    => ResolveMobileEmployeeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // JSON error responses for all API routes — format matches ApiResponse trait
        $exceptions->render(function (AuthenticationException $_e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('messages.unauthenticated'),
                    'errors'  => [],
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('messages.validation_failed'),
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (NotFoundHttpException $_e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('messages.not_found'),
                    'errors'  => [],
                ], 404);
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage() ?: __('messages.server_error'),
                    'errors'  => [],
                ], $status);
            }
        });
    })
    ->create();
