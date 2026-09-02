<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MeResource
 *
 * Returns the authenticated user's own profile data.
 * Never exposes: password, remember_token, internal notes.
 * Used by: /api/v1/auth/me endpoint.
 */
final class MeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'full_name'         => trim("{$this->first_name} {$this->last_name}"),
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'user_type'         => $this->user_type,
            'status'            => $this->status,
            'avatar_url'        => $this->avatar_url,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'two_factor_enabled'=> ! empty($this->two_factor_secret),
            'timezone'          => $this->timezone,
            'locale'            => $this->locale,
            'tenant'            => $this->whenLoaded('tenant', fn () => [
                'id'   => $this->tenant?->id,
                'name' => $this->tenant?->name,
                'slug' => $this->tenant?->slug,
                'plan' => $this->tenant?->plan,
            ]),
            'office'            => $this->whenLoaded('office', fn () => [
                'id'   => $this->office?->id,
                'name' => $this->office?->name,
            ]),
            'roles'             => $this->whenLoaded('roles', fn () =>
                $this->roles->pluck('slug')
            ),
            'permissions'       => $this->whenLoaded('permissions', fn () =>
                $this->permissions->pluck('slug')
            ),
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
