<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Discount;

use App\DTOs\DiscountData;
use App\DTOs\PaginationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\StoreDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Http\Requests\Discount\ValidateDiscountRequest;
use App\Http\Resources\Discount\DiscountCollection;
use App\Http\Resources\Discount\DiscountResource;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use App\Services\Discount\DiscountService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DiscountController — HTTP layer only.
 *
 * Validation: StoreDiscountRequest / UpdateDiscountRequest / ValidateDiscountRequest
 * Business logic: DiscountService
 */
final class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService             $discountService,
        private readonly DiscountRepositoryInterface $discounts,
    ) {}

    /**
     * Platform admin: list all discounts with filters + pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'discounts',
            ['id', 'code', 'name', 'type', 'value', 'status', 'uses_count', 'valid_until', 'created_at'],
        );

        $discounts = $this->discounts->paginate(
            perPage: $pagination->perPage,
            sortBy:  $pagination->sortBy,
            sortDir: $pagination->sortDir,
            filters: array_filter(['search' => $pagination->search]),
            with:    ['currency:id,currency,currency_symbol', 'creator:id,first_name,last_name'],
        );

        return ApiResponse::success(new DiscountCollection($discounts));
    }

    /**
     * Platform admin: create a new discount.
     */
    public function store(StoreDiscountRequest $request): JsonResponse
    {
        $discount = $this->discountService->create(
            DiscountData::fromArray($request->validated()),
            $request->user()->id,
        );

        return ApiResponse::created(new DiscountResource($discount), 'Discount created.');
    }

    /**
     * Platform admin / tenant: view a single discount.
     */
    public function show(string $id): JsonResponse
    {
        $discount = $this->discounts->findOrFail($id);
        $discount->load(['currency:id,currency,currency_symbol', 'assignedTenants:id,name,slug']);

        return ApiResponse::success(new DiscountResource($discount));
    }

    /**
     * Platform admin: update a discount.
     */
    public function update(UpdateDiscountRequest $request, string $id): JsonResponse
    {
        $discount = $this->discountService->update(
            $id,
            DiscountData::fromArray(array_merge(['code' => $this->discounts->findOrFail($id)->code], $request->validated())),
        );

        return ApiResponse::success(new DiscountResource($discount), 'Discount updated.');
    }

    /**
     * Platform admin: deactivate a discount (soft-disable, not delete).
     */
    public function deactivate(Request $request, string $id): JsonResponse
    {
        $discount = $this->discountService->deactivate($id, $request->user()->id);

        return ApiResponse::success(new DiscountResource($discount), 'Discount deactivated.');
    }

    /**
     * Platform admin: soft-delete a discount.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->discounts->delete($id);

        return ApiResponse::noContent('Discount deleted.');
    }

    /**
     * Tenant user: validate a discount code at checkout.
     * Returns the discount preview (savings amount) without applying it.
     */
    public function validate(ValidateDiscountRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user      = $request->user();

        try {
            $discount = $this->discountService->validate(
                $validated['code'],
                $user->tenant_id,
                $validated['plan'],
                (string) $validated['amount'],
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 'DISCOUNT_INVALID', 422);
        }

        $discountAmount = $discount->calculateAmount((string) $validated['amount']);
        $finalAmount    = bcsub((string) $validated['amount'], $discountAmount, 6);

        return ApiResponse::success([
            'discount'        => new DiscountResource($discount),
            'original_amount' => (string) $validated['amount'],
            'discount_amount' => $discountAmount,
            'final_amount'    => $finalAmount,
        ], 'Discount code is valid.');
    }

    /**
     * Platform admin: stats for the discount module.
     */
    public function stats(): JsonResponse
    {
        return ApiResponse::success([
            'total'    => $this->discounts->all(['id'])->count(),
            'active'   => $this->discounts->paginate(1, 'created_at', 'desc', ['status' => 'active'])->total(),
            'expired'  => $this->discounts->paginate(1, 'created_at', 'desc', ['status' => 'expired'])->total(),
            'depleted' => $this->discounts->paginate(1, 'created_at', 'desc', ['status' => 'depleted'])->total(),
        ]);
    }
}
