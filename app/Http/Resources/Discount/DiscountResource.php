<?php

declare(strict_types=1);

namespace App\Http\Resources\Discount;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountStatus;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Discount
 */
final class DiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()?->isPlatformAdmin() ?? false;

        return [
            'id'   => $this->id,
            'code' => $this->code,
            'name' => $this->name,

            'type' => [
                'value'  => $this->type instanceof DiscountType ? $this->type->value : $this->type,
                'label'  => $this->type instanceof DiscountType ? $this->type->label() : $this->type,
                'symbol' => $this->type instanceof DiscountType ? $this->type->symbol() : '',
            ],

            'value'               => $this->value,
            'max_discount_amount' => $this->max_discount_amount,

            'applicability' => [
                'value' => $this->applicability instanceof DiscountApplicability
                    ? $this->applicability->value : $this->applicability,
                'label' => $this->applicability instanceof DiscountApplicability
                    ? $this->applicability->label() : $this->applicability,
            ],

            'trigger' => [
                'value' => $this->trigger instanceof DiscountTrigger
                    ? $this->trigger->value : $this->trigger,
                'label' => $this->trigger instanceof DiscountTrigger
                    ? $this->trigger->label() : $this->trigger,
            ],

            'status' => [
                'value'       => $this->status instanceof DiscountStatus
                    ? $this->status->value : $this->status,
                'label'       => $this->status instanceof DiscountStatus
                    ? $this->status->label() : $this->status,
                'badge_class' => $this->status instanceof DiscountStatus
                    ? $this->status->badgeClass() : 'badge-gray',
            ],

            'valid_from'          => $this->valid_from?->toDateString(),
            'valid_until'         => $this->valid_until?->toDateString(),
            'max_uses'            => $this->max_uses,
            'max_uses_per_tenant' => $this->max_uses_per_tenant,
            'uses_count'          => $this->uses_count,
            'auto_email'          => $this->auto_email,
            'is_expired'          => $this->isExpired(),

            'currency' => $this->whenLoaded('currency', fn () => [
                'id'     => $this->currency->id,
                'code'   => $this->currency->currency,
                'symbol' => $this->currency->currency_symbol,
            ]),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Admin-only fields
            'description'      => $this->when($isAdmin, $this->description),
            'applicable_plans' => $this->when($isAdmin, $this->applicablePlanList()),
            'total_saved'      => $this->when($isAdmin, fn () =>
                $this->usages()->sum('discount_amount'),
            ),
        ];
    }
}
