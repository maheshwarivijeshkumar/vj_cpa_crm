<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateNotificationPreferencesRequest;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Http\Requests\Settings\UpdateProfileRequest;
use App\Http\Resources\Auth\MeResource;
use App\Services\Audit\AuditService;
use App\Services\Settings\SettingsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * API Settings Controller — tenant user self-service settings.
 *
 * Covers:
 *  - GET  /api/v1/settings/profile          — own profile
 *  - PATCH /api/v1/settings/profile          — update profile
 *  - PATCH /api/v1/settings/password         — change password
 *  - GET  /api/v1/settings/notifications     — notification preferences
 *  - PATCH /api/v1/settings/notifications    — update preferences
 *
 * Clean controller — validation in Form Requests, storage in Services.
 */
final class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly AuditService    $audit,
    ) {}

    /** GET /settings/profile — authenticated user's own profile */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['tenant:id,name,slug,plan', 'office:id,name', 'roles:id,name,slug']);

        return ApiResponse::success(new MeResource($user));
    }

    /** PATCH /settings/profile — update own profile */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Remove old avatar
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $data['avatar_url'] = $request->file('avatar')->store('avatars', 'public');
            unset($data['avatar']);
        }

        $user->update($data);

        AuditService::log('profile.updated', $user, ['fields' => array_keys($data)]);

        return ApiResponse::success(new MeResource($user->fresh(['tenant', 'office', 'roles'])), 'Profile updated.');
    }

    /** PATCH /settings/password — change own password */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'password'              => Hash::make($request->validated('password')),
            'must_change_password'  => false,
        ]);

        AuditService::logAuth('password.changed', $user->email, true, [
            'user_id' => $user->id,
        ]);

        return ApiResponse::success(null, 'Password changed successfully.');
    }

    /** GET /settings/notifications — user's notification preferences */
    public function notifications(Request $request): JsonResponse
    {
        $user = $request->user();

        $prefs = SettingsService::get(
            key:    'notifications.preferences',
            userId: $user->id,
        );

        // Default structure if not yet saved
        $defaults = [
            'filing_deadlines'    => ['email' => true,  'in_app' => true,  'sms' => false],
            'invoice_sent'        => ['email' => true,  'in_app' => true,  'sms' => false],
            'payment_received'    => ['email' => true,  'in_app' => true,  'sms' => false],
            'task_assigned'       => ['email' => false, 'in_app' => true,  'sms' => false],
            'subscription_events' => ['email' => true,  'in_app' => true,  'sms' => false],
            'referral_updates'    => ['email' => true,  'in_app' => true,  'sms' => false],
        ];

        return ApiResponse::success(
            array_merge($defaults, is_array($prefs) ? $prefs : [])
        );
    }

    /** PATCH /settings/notifications — save notification preferences */
    public function updateNotifications(UpdateNotificationPreferencesRequest $request): JsonResponse
    {
        $user = $request->user();

        SettingsService::setForUser(
            userId: $user->id,
            key:    'notifications.preferences',
            value:  $request->validated('preferences'),
            type:   'json',
        );

        return ApiResponse::success(null, 'Notification preferences saved.');
    }
}
