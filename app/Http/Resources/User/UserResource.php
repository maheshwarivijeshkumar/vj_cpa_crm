<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'uuid'       => $this->uuid,
            'name'       => $this->full_name,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'username'   => $this->username,
            'phone'      => $this->phone,
            'avatar_path'=> $this->avatar_path,
            'user_type'  => [
                'value' => $this->user_type,
                'label' => UserType::tryFrom($this->user_type)?->label() ?? $this->user_type,
            ],
            'status' => [
                'value'       => $this->status,
                'label'       => UserStatus::tryFrom($this->status)?->label() ?? $this->status,
                'badge_class' => UserStatus::tryFrom($this->status)?->badgeClass() ?? 'badge-gray',
            ],
            'must_change_password' => $this->must_change_password,
            'two_factor_enabled'   => $this->two_factor_enabled,
            'last_login_at'        => $this->last_login_at?->toIso8601String(),
            'email_verified_at'    => $this->email_verified_at?->toIso8601String(),
            'created_at'           => $this->created_at?->toIso8601String(),
            'updated_at'           => $this->updated_at?->toIso8601String(),

            // Conditional — only when loaded
            'tenant' => $this->whenLoaded('tenant', fn () => [
                'id'   => $this->tenant->id,
                'name' => $this->tenant->name,
                'slug' => $this->tenant->slug,
            ]),
            'office' => $this->whenLoaded('office', fn () => [
                'id'   => $this->office->id,
                'name' => $this->office->name,
            ]),
            'roles' => $this->whenLoaded('roles', fn () =>
                $this->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'slug' => $r->slug])
            ),

            // Security-sensitive — only for the user themselves or platform admins
            'last_login_ip' => $this->when(
                $request->user()?->isPlatformAdmin() || $request->user()?->id === $this->id,
                $this->last_login_ip,
            ),
        ];
    }
}
