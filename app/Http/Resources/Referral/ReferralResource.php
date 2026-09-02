<?php

declare(strict_types=1);

namespace App\Http\Resources\Referral;

use App\Enums\ReferralStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Referral
 */
final class ReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'status' => [
                'value'       => $this->status instanceof ReferralStatus
                    ? $this->status->value : $this->status,
                'label'       => $this->status instanceof ReferralStatus
                    ? $this->status->label() : $this->status,
                'badge_class' => $this->status instanceof ReferralStatus
                    ? $this->status->badgeClass() : 'badge-gray',
            ],

            'referee_email' => $this->referee_email,

            'referee_tenant' => $this->whenLoaded('refereeTenant', fn () =>
                $this->refereeTenant ? [
                    'id'   => $this->refereeTenant->id,
                    'name' => $this->refereeTenant->name,
                ] : null,
            ),

            'clicked_at'   => $this->clicked_at?->toIso8601String(),
            'signed_up_at' => $this->signed_up_at?->toIso8601String(),
            'verified_at'  => $this->verified_at?->toIso8601String(),
            'rewarded_at'  => $this->rewarded_at?->toIso8601String(),
            'expires_at'   => $this->expires_at?->toIso8601String(),

            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
