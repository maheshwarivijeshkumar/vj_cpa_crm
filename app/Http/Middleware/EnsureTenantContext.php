<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves and stores the tenant context for the current request.
 *
 * Applied to:
 *  - All authenticated API routes (auth:sanctum)
 *  - All authenticated web routes
 *
 * Platform admins (user_type = platform_admin) pass through with null tenant.
 * Firm users without a valid tenant receive 403.
 */
class EnsureTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        try {
            TenantContext::resolveFromUser($user);
        } catch (\RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not associated with a firm. Please contact support.',
                    'code'    => 'NO_TENANT',
                ], 403);
            }

            abort(403, 'Your account is not associated with a firm.');
        }

        // Enforce active tenant (skip for platform admins)
        if (TenantContext::hasTenant()) {
            $tenant = TenantContext::get();

            if (! $tenant->isActive()) {
                $status = $tenant->status;

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => match ($status) {
                            'suspended'  => 'Your firm account has been suspended. Please contact support.',
                            'cancelled'  => 'Your firm account has been cancelled.',
                            default      => 'Your firm account is not active.',
                        },
                        'code' => strtoupper("TENANT_{$status}"),
                    ], 403);
                }

                abort(403, match ($status) {
                    'suspended' => 'Your firm account has been suspended.',
                    'cancelled' => 'Your firm account has been cancelled.',
                    default     => 'Your firm account is not active.',
                });
            }
        }

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        TenantContext::reset();
    }
}
