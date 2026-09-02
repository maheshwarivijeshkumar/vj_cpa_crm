<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Referral;

use App\Http\Controllers\Controller;
use App\Http\Resources\Referral\ReferralLinkResource;
use App\Http\Resources\Referral\ReferralResource;
use App\Repositories\Contracts\ReferralRepositoryInterface;
use App\Services\Referral\ReferralService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ReferralController — HTTP layer only.
 *
 * Business logic: ReferralService
 *
 * Endpoints:
 *   GET  /api/v1/referral/link          — get or create the tenant's referral link
 *   GET  /api/v1/referral/balance       — current points + credit balance
 *   GET  /api/v1/referral               — list of all referrals for this tenant
 *   POST /api/v1/referral/redeem        — redeem points/credit against current subscription
 *   GET  /r/{code}                      — public click handler (in routes/web.php)
 */
final class ReferralController extends Controller
{
    public function __construct(
        private readonly ReferralService             $referralService,
        private readonly ReferralRepositoryInterface $referrals,
    ) {}

    /**
     * Get the referral link for the current tenant (creates one if it doesn't exist).
     */
    public function link(Request $request): JsonResponse
    {
        $link = $this->referralService->getLinkForTenant($request->user()->tenant_id);

        return ApiResponse::success(new ReferralLinkResource($link));
    }

    /**
     * Get current reward balance (points + credit) for the current tenant.
     */
    public function balance(Request $request): JsonResponse
    {
        $balance = $this->referralService->getBalance($request->user()->tenant_id);
        $count   = $this->referrals->countRewarded($request->user()->tenant_id);

        return ApiResponse::success([
            'points_balance'    => $balance['points'],
            'credit_balance'    => $balance['credit'],
            'total_referrals'   => $count,
        ]);
    }

    /**
     * List all referrals for the current tenant (as referrer).
     */
    public function index(Request $request): JsonResponse
    {
        $referrals = $this->referrals->forReferrer($request->user()->tenant_id);

        return ApiResponse::success(
            ReferralResource::collection($referrals),
        );
    }

    /**
     * Redeem reward balance against the current subscription.
     *
     * @bodyParam reward_type string required  points|credit
     * @bodyParam amount      string required  Amount to redeem (DECIMAL)
     */
    public function redeem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reward_type'    => ['required', 'in:points,credit'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'subscription_id'=> ['required', 'string'],
        ]);

        try {
            $redeemed = $this->referralService->redeemBalance(
                tenantId:       $request->user()->tenant_id,
                rewardType:     $validated['reward_type'],
                requestedAmount:(string) $validated['amount'],
                subscriptionId: $validated['subscription_id'],
                currencyId:     $request->user()->currency_id,
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 'INSUFFICIENT_BALANCE', 422);
        }

        return ApiResponse::success([
            'amount_redeemed' => $redeemed,
            'balance'         => $this->referralService->getBalance($request->user()->tenant_id),
        ], "Successfully redeemed {$redeemed} {$validated['reward_type']}.");
    }
}
