<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateProfileRequest;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

/**
 * ProfileController — handles profile update form submissions.
 *
 * Works via Inertia (PATCH /settings/profile).
 * Redirects back to the profile page with a flash success message.
 */
final class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $data['avatar_url'] = $request->file('avatar')->store('avatars', 'public');
            unset($data['avatar']);
        }

        $user->update($data);

        AuditService::log('profile.updated', $user, ['fields' => array_keys($data)]);

        return redirect()
            ->route('settings.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
