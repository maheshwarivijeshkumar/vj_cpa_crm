<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\UpdatePlatformSettingsRequest;
use App\Models\SystemSetting;
use App\Services\Settings\SettingsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform SettingsController — manages platform-level system settings.
 *
 * Clean controller: validation fully delegated to UpdatePlatformSettingsRequest.
 */
final class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    /** Inertia page: grouped settings listing */
    public function index(): Response
    {
        $groups = SystemSetting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->map(fn ($s) => [
                'id'          => $s->id,
                'group'       => $s->group,
                'key'         => $s->key,
                'value'       => $s->value,
                'type'        => $s->type,
                'description' => $s->description,
                'is_public'   => $s->is_public,
            ]));

        return Inertia::render('Platform/Settings/Index', [
            'settingGroups' => $groups,
        ]);
    }

    /** API: batch update settings — all validation in UpdatePlatformSettingsRequest */
    public function update(UpdatePlatformSettingsRequest $request): JsonResponse
    {
        foreach ($request->validated()['settings'] as $setting) {
            SettingsService::setPlatform(
                "{$setting['group']}.{$setting['key']}",
                $setting['value'],
                $setting['type'],
            );
        }

        return ApiResponse::success(null, 'Settings updated successfully.');
    }

    /** API: clear settings cache */
    public function clearCache(): JsonResponse
    {
        SettingsService::clearPlatformCache();

        return ApiResponse::success(null, 'Settings cache cleared.');
    }
}
