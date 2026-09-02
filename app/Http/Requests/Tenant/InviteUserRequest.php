<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * InviteUserRequest
 *
 * Validates a firm owner / firm admin invitation of a new team member.
 * Email must be unique globally (users cannot belong to two tenants at once).
 */
final class InviteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isFirmUser() ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')],
            'role'       => ['required', 'string', 'exists:roles,slug'],
            'office_id'  => ['nullable', 'integer', 'exists:offices,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.unique'     => 'This email address is already registered.',
            'role.exists'      => 'The selected role does not exist.',
            'office_id.exists' => 'The selected office does not exist.',
        ];
    }
}
