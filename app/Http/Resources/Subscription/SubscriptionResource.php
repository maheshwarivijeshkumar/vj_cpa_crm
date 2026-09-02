<?php

declare(strict_types=1);

namespace App\Http\Resources\Subscription;

use App\Enums\SubscriptionStatus;
use App\Enums\TenantPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Subscription
 */
final class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'tenant_id'     => $this->tenant_id,
            'billing_cycle' => $this->billing_cycle,

            'plan' => [
                'value'         => $this->plan instanceof TenantPlan
                    ? $this->plan->value : $this->plan,
                'label'         => $this->plan instanceof TenantPlan
                    ? $this->plan->label() : $this->plan,
                'monthly_price' => $this->plan instanceof TenantPlan
                    ? $this->plan->monthlyPrice() : null,
            ],

            'status' => [
                'value'       => $this->status instanceof SubscriptionStatus
                    ? $this->status->value : $this->status,
                'label'       => $this->status instanceof SubscriptionStatus
                    ? $this->status->label() : $this->status,
                'badge_class' => $this->status instanceof SubscriptionStatus
                    ? $this->status->badgeClass() : 'badge-gray',
            ],

            'starts_at'      => $this->starts_at?->toDateString(),
            'ends_at'        => $this->ends_at?->toDateString(),
            'trial_ends_at'  => $this->trial_ends_at?->toDateString(),
            'days_remaining' => $this->daysRemaining(),
            'is_active'      => $this->isActive(),

            'amount_paid'     => $this->amount_paid,
            'discount_amount' => $this->discount_amount,

            'currency' => $this->whenLoaded('currency', fn () => [
                'id'     => $this->currency->id,
                'code'   => $this->currency->currency,
                'symbol' => $this->currency->currency_symbol,
            ]),

            'discount' => $this->whenLoaded('discount', fn () => $this->discount ? [
                'id'   => $this->discount->id,
                'code' => $this->discount->code,
                'name' => $this->discount->name,
            ] : null),

            'cancelled_at'        => $this->cancelled_at?->toIso8601String(),
            'cancellation_reason' => $this->cancellation_reason,
            'lapsed_at'           => $this->lapsed_at?->toIso8601String(),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
