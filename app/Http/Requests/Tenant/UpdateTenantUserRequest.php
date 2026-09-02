<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateTenantUserRequest
 *
 * Validates role / office reassignment for a tenant user.
 * Cannot change email via this form (security boundary).
 */
final class UpdateTenantUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isFirmUser() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'role'      => ['required', 'string', 'exists:roles,slug'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'status'    => ['required', 'string', Rule::in(['active', 'inactive', 'suspended'])],
        ];
    }
}
