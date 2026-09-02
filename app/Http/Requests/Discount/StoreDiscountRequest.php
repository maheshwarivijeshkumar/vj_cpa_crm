<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates creating a new discount code.
 * Only platform admins are authorised.
 */
class StoreDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'code'                 => ['required', 'string', 'min:3', 'max:60', 'regex:/^[A-Z0-9\-]+$/'],
            'name'                 => ['required', 'string', 'min:2', 'max:150'],
            'description'          => ['nullable', 'string', 'max:500'],
            'type'                 => ['required', Rule::enum(DiscountType::class)],
            'value'                => ['required', 'numeric', 'min:0.01'],
            'max_discount_amount'  => ['nullable', 'numeric', 'min:0.01'],
            'currency_id'          => ['nullable', 'integer', 'exists:currencies,id'],
            'applicability'        => ['required', Rule::enum(DiscountApplicability::class)],
            'applicable_plans'     => ['nullable', 'array'],
            'applicable_plans.*'   => ['string', Rule::in(['trial', 'starter', 'professional', 'enterprise'])],
            'trigger'              => ['required', Rule::enum(DiscountTrigger::class)],
            'valid_from'           => ['nullable', 'date'],
            'valid_until'          => ['nullable', 'date', 'after_or_equal:valid_from'],
            'max_uses'             => ['nullable', 'integer', 'min:1'],
            'max_uses_per_tenant'  => ['nullable', 'integer', 'min:1'],
            'auto_email'           => ['boolean'],
            'tenant_ids'           => ['nullable', 'array'],
            'tenant_ids.*'         => ['integer', 'exists:tenants,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex'          => 'Code may only contain uppercase letters, numbers, and hyphens.',
            'valid_until.after_or_equal' => 'Expiry date must be on or after the start date.',
            'value.min'           => 'Discount value must be greater than zero.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->code))]);
        }
        $this->mergeIfMissing(['auto_email' => false, 'max_uses_per_tenant' => 1]);
    }
}
