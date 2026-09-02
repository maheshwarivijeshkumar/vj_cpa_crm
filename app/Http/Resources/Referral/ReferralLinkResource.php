<?php

declare(strict_types=1);

namespace App\Http\Resources\Referral;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ReferralLink
 */
final class ReferralLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'code'         => $this->code,
            'url'          => $this->url(),
            'click_count'  => $this->click_count,
            'signup_count' => $this->signup_count,
            'is_active'    => $this->is_active,
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
