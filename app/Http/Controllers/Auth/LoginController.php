<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\LoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * LoginController — HTTP layer only.
 *
 * Responsibilities:
 *   - Render the login page
 *   - Delegate authentication to LoginRequest (throttle + credentials check)
 *   - Delegate post-auth side-effects to LoginService
 *   - Decide redirect destination
 *   - Delegate logout to LoginService
 *
 * Contains zero business logic.
 */
final class LoginController extends Controller
{
    public function __construct(
        private readonly LoginService $loginService,
    ) {}

    /** Show the login page. */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => true,
            'status'           => session('status'),
        ]);
    }

    /**
     * Authenticate the request.
     * LoginRequest handles rate-limiting and credential verification.
     * LoginService handles session, metadata, and audit logging.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Throws ValidationException on bad credentials / too many attempts
        $request->authenticate();

        $user = $this->loginService->afterAuthenticate($request);

        if ($user->must_change_password) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(
            $this->loginService->intendedRedirect($user),
        );
    }

    /** Log out the current user. */
    public function destroy(Request $request): RedirectResponse
    {
        $this->loginService->logout($request);

        return redirect('/');
    }
}
