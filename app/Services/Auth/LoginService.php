<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * LoginService — all business logic for authentication.
 *
 * Responsibilities:
 *  - Authenticate credentials via LoginRequest (request handles throttle+validate)
 *  - Regenerate session (security: prevents session fixation)
 *  - Record last_login_at and last_login_ip on the User row
 *  - Write audit log entry
 *  - Log user out + invalidate session
 *
 * The controller only decides WHERE to redirect.
 */
final class LoginService
{
    /**
     * Complete the login process for an already-authenticated request.
     * Call this AFTER LoginRequest::authenticate() has passed.
     *
     * @return User  The authenticated user
     */
    public function afterAuthenticate(Request $request): User
    {
        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        // Persist login metadata — raw update to skip event dispatch and timestamps touch
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

        AuditService::logAuth('login', $user->email, true, [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $user->fresh();
    }

    /**
     * Log out the currently authenticated user.
     * Invalidates the session and regenerates the CSRF token.
     */
    public function logout(Request $request): void
    {
        $email = Auth::user()?->email ?? 'unknown';

        AuditService::logAuth('logout', $email, true);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Resolve the intended post-login redirect path.
     */
    public function intendedRedirect(User $user, string $default = '/'): string
    {
        if ($user->isPlatformAdmin()) {
            return route('platform.dashboard');
        }

        return route('dashboard');
    }
}
