<?php

declare(strict_types=1);

namespace App\Http\Resources\Platform;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use App\Support\PaginationHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Tenant
 */
final class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'uuid'  => $this->uuid,
            'name'  => $this->name,
            'slug'  => $this->slug,
            'email' => $this->email,
            'phone' => $this->phone,

            'plan' => [
                'value'         => $this->plan,
                'label'         => TenantPlan::tryFrom($this->plan)?->label() ?? $this->plan,
                'monthly_price' => TenantPlan::tryFrom($this->plan)?->monthlyPrice(),
                'max_clients'   => TenantPlan::tryFrom($this->plan)?->maxClients(),
            ],

            'status' => [
                'value'       => $this->status,
                'label'       => TenantStatus::tryFrom($this->status)?->label() ?? $this->status,
                'badge_class' => TenantStatus::tryFrom($this->status)?->badgeClass() ?? 'badge-gray',
            ],

            'address' => [
                'line1'       => $this->address_line1,
                'line2'       => $this->address_line2,
                'city'        => $this->city,
                'state'       => $this->state,
                'postal_code' => $this->postal_code,
            ],

            'fiscal_year' => [
                'start_month' => $this->fiscal_year_start_month,
                'start_day'   => $this->fiscal_year_start_day,
            ],

            'trial_ends_at'  => $this->trial_ends_at?->toIso8601String(),
            'suspended_at'   => $this->suspended_at?->toIso8601String(),
            'is_active'      => $this->is_active,
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),

            // Conditionally loaded
            'country'  => $this->whenLoaded('country',  fn () => ['id' => $this->country->id,  'name' => $this->country->name,  'iso2' => $this->country->iso2]),
            'timezone' => $this->whenLoaded('timezone', fn () => ['id' => $this->timezone->id, 'zone_name' => $this->timezone->zone_name, 'gmt_offset_name' => $this->timezone->gmt_offset_name]),
            'currency' => $this->whenLoaded('currency', fn () => ['id' => $this->currency->id, 'code' => $this->currency->currency, 'symbol' => $this->currency->currency_symbol]),

            'user_count'  => $this->whenCounted('users'),
            'office_count'=> $this->whenCounted('offices'),
        ];
    }
}
