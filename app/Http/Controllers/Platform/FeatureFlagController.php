<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Services\Audit\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform FeatureFlagController
 *
 * Manages feature flags — on/off switches that control feature rollouts
 * at the platform level, per-tenant, or per-plan.
 *
 * Routes: /platform/feature-flags
 */
final class FeatureFlagController extends Controller
{
    /** Inertia page: feature flags listing */
    public function index(): Response
    {
        $flags = FeatureFlag::query()
            ->orderBy('module')
            ->orderBy('key')
            ->get();

        return Inertia::render('Platform/FeatureFlags/Index', [
            'flags' => $flags->map(fn (FeatureFlag $f) => [
                'id'          => $f->id,
                'key'         => $f->key,
                'module'      => $f->module,
                'description' => $f->description,
                'is_enabled'  => $f->is_enabled,
                'scope'       => $f->scope,
                'created_at'  => $f->created_at?->toDateString(),
            ]),
            'modules' => $flags->pluck('module')->unique()->sort()->values(),
        ]);
    }

    /** API: toggle a feature flag on/off */
    public function toggle(int $id): JsonResponse
    {
        /** @var FeatureFlag $flag */
        $flag = FeatureFlag::findOrFail($id);

        $flag->update(['is_enabled' => ! $flag->is_enabled]);

        AuditService::log(
            'feature_flag.toggled',
            $flag,
            ['key' => $flag->key, 'is_enabled' => $flag->is_enabled],
        );

        return ApiResponse::success([
            'id'         => $flag->id,
            'key'        => $flag->key,
            'is_enabled' => $flag->is_enabled,
        ], $flag->is_enabled ? 'Feature enabled.' : 'Feature disabled.');
    }

    /** API: update feature flag description / scope */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:500'],
            'is_enabled'  => ['sometimes', 'boolean'],
        ]);

        /** @var FeatureFlag $flag */
        $flag = FeatureFlag::findOrFail($id);
        $flag->update($validated);

        return ApiResponse::success(null, 'Feature flag updated.');
    }
}
