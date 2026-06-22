<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 📱 Mobile employee resolver (token-less auth).
 *
 * Replaces Sanctum bearer authentication for the mobile-employee flow.
 * The mobile client identifies the acting learner by sending their HR
 * `machine_code` in the `Employee-Code` request header. This middleware:
 *
 *   1. Pulls the code from the header (falls back to the
 *      `employee_code` query / body param so simple GET testing in
 *      Swagger UI also works without re-typing the header).
 *   2. Looks up the active `User` row by `machine_code`.
 *   3. Wires the user into the request via `setUserResolver` so every
 *      mobile service / resource that reads `$request->user()` keeps
 *      working with NO code change (same contract Sanctum uses).
 *
 * Validation errors are surfaced as the standard JSON envelope:
 *   - 422  -> Employee-Code header missing or empty
 *   - 404  -> No active user found with that machine_code
 *
 * ⚠️  Security note
 *   This middleware deliberately performs NO secret verification — the
 *   header is treated as a *trusted identity assertion*. It must only
 *   run behind a controlled network boundary (intranet, VPN, mTLS
 *   gateway, or an HR system signing the requests upstream). On the
 *   open internet this would let any client impersonate any employee.
 *
 * Route alias: `mobile.employee` (registered in bootstrap/app.php).
 */
class ResolveMobileEmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $code = $this->extractEmployeeCode($request);

        if ($code === null || $code === '') {
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.mobile_employee_code_required'),
                'errors'  => [
                    'Employee-Code' => [__('messages.mobile_employee_code_required')],
                ],
            ], 422);
        }

        /** @var User|null $user */
        $user = User::query()
            ->where('machine_code', $code)
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => __('messages.mobile_employee_not_found'),
                'errors'  => [
                    'Employee-Code' => [__('messages.mobile_employee_not_found')],
                ],
            ], 404);
        }

        // Wire the resolved learner into the request so every mobile
        // service / resource that already does `$request->user()` keeps
        // working unchanged — mirrors the Sanctum middleware contract.
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    private function extractEmployeeCode(Request $request): ?string
    {
        $candidates = [
            $request->header('Employee-Code'),
            $request->header('X-Employee-Code'),
            $request->query('employee_code'),
            $request->input('employee_code'),
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }
}
