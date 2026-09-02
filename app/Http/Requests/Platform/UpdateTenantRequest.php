<?php

declare(strict_types=1);

namespace App\Http\Requests\Platform;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'   => ['sometimes', 'string', 'min:2', 'max:150'],
            'email'  => ['sometimes', 'string', 'email:rfc', 'max:255'],
            'plan'   => ['sometimes', Rule::enum(TenantPlan::class)],
            'status' => ['sometimes', Rule::enum(TenantStatus::class)],

            'country_id'              => ['nullable', 'integer', 'exists:countries,id'],
            'timezone_id'             => ['nullable', 'integer', 'exists:timezones,id'],
            'currency_id'             => ['nullable', 'integer', 'exists:currencies,id'],
            'language_id'             => ['nullable', 'integer', 'exists:languages,id'],
            'fiscal_year_start_month' => ['nullable', 'integer', 'between:1,12'],
            'fiscal_year_start_day'   => ['nullable', 'integer', 'between:1,31'],
            'phone'                   => ['nullable', 'string', 'max:30'],
            'website'                 => ['nullable', 'url', 'max:255'],
            'address_line1'           => ['nullable', 'string', 'max:255'],
            'address_line2'           => ['nullable', 'string', 'max:255'],
            'city'                    => ['nullable', 'string', 'max:100'],
            'state'                   => ['nullable', 'string', 'max:100'],
            'postal_code'             => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
    }
}
