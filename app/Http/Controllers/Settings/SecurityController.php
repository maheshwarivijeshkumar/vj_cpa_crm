<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

/**
 * SecurityController — handles password change form submissions.
 *
 * Works via Inertia (PATCH /settings/password).
 * Redirects back to the security page with a flash success message.
 */
final class SecurityController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'password'             => Hash::make($request->validated('password')),
            'must_change_password' => false,
        ]);

        AuditService::logAuth('password.changed', $user->email, true, [
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('settings.security')
            ->with('success', 'Password updated successfully.');
    }
}
