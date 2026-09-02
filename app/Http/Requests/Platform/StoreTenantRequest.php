<?php

declare(strict_types=1);

namespace App\Http\Requests\Platform;

use App\Enums\TenantPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validates creating a new tenant (accounting firm) by a platform admin.
 * All business rules live here — controller stays clean.
 */
class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            // Firm details
            'name'  => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'plan'  => ['required', Rule::enum(TenantPlan::class)],

            // Firm owner
            'owner_first_name' => ['required', 'string', 'min:1', 'max:80'],
            'owner_last_name'  => ['required', 'string', 'min:1', 'max:80'],
            'owner_email'      => ['required', 'string', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'owner_password'   => ['required', 'string', Password::defaults()],

            // Locale (all FK IDs — validated against reference tables)
            'country_id'              => ['nullable', 'integer', 'exists:countries,id'],
            'timezone_id'             => ['nullable', 'integer', 'exists:timezones,id'],
            'currency_id'             => ['nullable', 'integer', 'exists:currencies,id'],
            'language_id'             => ['nullable', 'integer', 'exists:languages,id'],
            'fiscal_year_start_month' => ['nullable', 'integer', 'between:1,12'],
            'fiscal_year_start_day'   => ['nullable', 'integer', 'between:1,31'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_email.unique'  => 'An account with this owner email already exists.',
            'plan.enum'           => 'Plan must be one of: ' . implode(', ', array_column(TenantPlan::cases(), 'value')),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize email fields
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
        if ($this->has('owner_email')) {
            $this->merge(['owner_email' => strtolower(trim($this->owner_email))]);
        }
    }
}
