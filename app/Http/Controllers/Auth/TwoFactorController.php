<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ConfirmTwoFactorRequest;
use App\Http\Requests\Auth\DisableTwoFactorRequest;
use App\Http\Requests\Auth\EnableTwoFactorRequest;
use App\Http\Requests\Auth\RegenerateRecoveryCodesRequest;
use App\Services\Auth\TwoFactorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * TwoFactorController — HTTP layer only.
 *
 * Responsibilities:
 *   - Render the 2FA challenge page (Inertia)
 *   - Delegate all TOTP operations to TwoFactorService
 *   - Shape JSON responses from service results or exceptions
 *
 * Contains zero business logic.
 * All TOTP math, secret generation, and state mutations live in TwoFactorService.
 */
final class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService,
    ) {}

    /** Show the 2FA challenge page (login TOTP code entry). */
    public function challenge(): Response
    {
        return Inertia::render('Auth/TwoFactor');
    }

    /**
     * Begin 2FA setup — returns QR code URL and base-32 secret.
     * EnableTwoFactorRequest ensures user is authenticated.
     */
    public function enable(EnableTwoFactorRequest $request): JsonResponse
    {
        try {
            $result = $this->twoFactorService->enable($request->user());
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 'TWO_FACTOR_ALREADY_ENABLED', 422);
        }

        return ApiResponse::success($result, '2FA setup initiated. Scan the QR code with your authenticator app.');
    }

    /**
     * Confirm 2FA — verifies the first TOTP code, activates 2FA, returns recovery codes.
     * ConfirmTwoFactorRequest validates that code is exactly 6 digits.
     */
    public function confirm(ConfirmTwoFactorRequest $request): JsonResponse
    {
        try {
            $result = $this->twoFactorService->confirm(
                $request->user(),
                $request->validated('code'),
            );
        } catch (\DomainException $e) {
            return ApiResponse::validationError(
                ['code' => [$e->getMessage()]],
                'Invalid authenticator code.',
            );
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 'TWO_FACTOR_NOT_INITIATED', 422);
        }

        return ApiResponse::success($result, '2FA enabled. Store your recovery codes safely.');
    }

    /**
     * Disable 2FA entirely.
     * DisableTwoFactorRequest validates current_password.
     */
    public function disable(DisableTwoFactorRequest $request): JsonResponse
    {
        $this->twoFactorService->disable($request->user());

        return ApiResponse::success(null, '2FA has been disabled.');
    }

    /**
     * Regenerate recovery codes (previous codes become invalid).
     * RegenerateRecoveryCodesRequest validates current_password.
     */
    public function regenerateCodes(RegenerateRecoveryCodesRequest $request): JsonResponse
    {
        $result = $this->twoFactorService->regenerateCodes($request->user());

        return ApiResponse::success($result, 'Recovery codes regenerated. Store them safely.');
    }
}
