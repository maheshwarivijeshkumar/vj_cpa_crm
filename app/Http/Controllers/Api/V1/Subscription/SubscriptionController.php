<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Subscription;

use App\DTOs\PaginationDTO;
use App\DTOs\SubscriptionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;
use App\Http\Requests\Subscription\RenewSubscriptionRequest;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Resources\Subscription\SubscriptionCollection;
use App\Http\Resources\Subscription\SubscriptionResource;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Subscription\SubscriptionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SubscriptionController — HTTP layer only.
 *
 * Validation: StoreSubscriptionRequest / RenewSubscriptionRequest / CancelSubscriptionRequest
 * Business logic: SubscriptionService
 */
final class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService             $subscriptionService,
        private readonly SubscriptionRepositoryInterface $subscriptions,
    ) {}

    /**
     * Platform admin: list subscriptions with filters.
     * Tenant user: list their own subscription history.
     */
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'subscriptions',
            ['id', 'plan', 'status', 'starts_at', 'ends_at', 'amount_paid', 'created_at'],
        );

        $user = $request->user();

        // Firm users only see their own tenant's subscriptions
        $filters = $user->isPlatformAdmin()
            ? array_filter(['search' => $pagination->search])
            : ['tenant_id' => $user->tenant_id];

        $subscriptions = $this->subscriptions->paginate(
            perPage: $pagination->perPage,
            sortBy:  $pagination->sortBy,
            sortDir: $pagination->sortDir,
            filters: $filters,
            with:    ['currency:id,currency,currency_symbol', 'discount:id,code,name'],
        );

        return ApiResponse::success(new SubscriptionCollection($subscriptions));
    }

    /**
     * View the current active subscription for the authenticated tenant.
     */
    public function current(Request $request): JsonResponse
    {
        $sub = $this->subscriptions->currentForTenant($request->user()->tenant_id);

        if ($sub === null) {
            return ApiResponse::notFound('No active subscription found.');
        }

        $sub->load(['currency:id,currency,currency_symbol', 'discount:id,code,name,type,value']);

        return ApiResponse::success(new SubscriptionResource($sub));
    }

    /**
     * View a specific subscription.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $sub = $this->subscriptions->findOrFail($id);

        // Firm users can only view their own
        if (! $request->user()->isPlatformAdmin() && $sub->tenant_id !== $request->user()->tenant_id) {
            return ApiResponse::forbidden();
        }

        $sub->load(['currency:id,currency,currency_symbol', 'discount:id,code,name']);

        return ApiResponse::success(new SubscriptionResource($sub));
    }

    /**
     * Platform admin: create a subscription for a tenant.
     */
    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $data     = $request->validated();
        $subData  = SubscriptionData::fromArray($data);
        $discount = $data['discount_code'] ?? null;

        try {
            $subscription = $this->subscriptionService->create($subData, false, $discount);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 'SUBSCRIPTION_ERROR', 422);
        }

        return ApiResponse::created(new SubscriptionResource($subscription), 'Subscription created.');
    }

    /**
     * Tenant firm owner: renew their subscription.
     */
    public function renew(RenewSubscriptionRequest $request): JsonResponse
    {
        $user = $request->user();

        try {
            $subscription = $this->subscriptionService->renew(
                $user->tenant_id,
                $request->validated('discount_code'),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 'RENEWAL_ERROR', 422);
        }

        return ApiResponse::created(new SubscriptionResource($subscription), 'Subscription renewed.');
    }

    /**
     * Cancel a subscription.
     * Firm owners cancel their own; platform admins cancel any.
     */
    public function cancel(CancelSubscriptionRequest $request, string $id): JsonResponse
    {
        $sub  = $this->subscriptions->findOrFail($id);
        $user = $request->user();

        // Authorise
        if (! $user->isPlatformAdmin() && $sub->tenant_id !== $user->tenant_id) {
            return ApiResponse::forbidden();
        }

        $data         = $request->validated();
        $subscription = $this->subscriptionService->cancel(
            $id,
            $data['reason'],
            (bool) ($data['immediately'] ?? false),
        );

        return ApiResponse::success(new SubscriptionResource($subscription), 'Subscription cancelled.');
    }

    /**
     * Platform admin: subscription statistics.
     */
    public function stats(): JsonResponse
    {
        return ApiResponse::success(
            $this->subscriptionService->platformStats(),
        );
    }
}
