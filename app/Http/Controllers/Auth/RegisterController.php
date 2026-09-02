<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * RegisterController — HTTP layer only.
 *
 * Responsibilities:
 *   - Render the registration page
 *   - Pass validated data to RegisterService
 *   - Redirect to dashboard on success
 *
 * Contains zero business logic.
 * Tenant creation, user creation, event dispatch, and login are in RegisterService.
 */
final class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterService $registerService,
    ) {}

    /** Show the registration page. */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle registration.
     * RegisterRequest validates all fields.
     * RegisterService does all business work.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->registerService->register($request->validated());

        return redirect()->route('dashboard');
    }
}
