<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountStatus;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates updating an existing discount code.
 * Only platform admins are authorised.
 */
class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'code'                 => ['sometimes', 'string', 'min:3', 'max:60', 'regex:/^[A-Z0-9\-]+$/'],
            'name'                 => ['sometimes', 'string', 'min:2', 'max:150'],
            'description'          => ['nullable', 'string', 'max:500'],
            'type'                 => ['sometimes', Rule::enum(DiscountType::class)],
            'value'                => ['sometimes', 'numeric', 'min:0.01'],
            'max_discount_amount'  => ['nullable', 'numeric', 'min:0.01'],
            'currency_id'          => ['nullable', 'integer', 'exists:currencies,id'],
            'applicability'        => ['sometimes', Rule::enum(DiscountApplicability::class)],
            'applicable_plans'     => ['nullable', 'array'],
            'applicable_plans.*'   => ['string', Rule::in(['trial', 'starter', 'professional', 'enterprise'])],
            'trigger'              => ['sometimes', Rule::enum(DiscountTrigger::class)],
            'valid_from'           => ['nullable', 'date'],
            'valid_until'          => ['nullable', 'date', 'after_or_equal:valid_from'],
            'max_uses'             => ['nullable', 'integer', 'min:1'],
            'max_uses_per_tenant'  => ['sometimes', 'integer', 'min:1'],
            'auto_email'           => ['sometimes', 'boolean'],
            'status'               => ['sometimes', Rule::enum(DiscountStatus::class)],
            'tenant_ids'           => ['nullable', 'array'],
            'tenant_ids.*'         => ['integer', 'exists:tenants,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->code))]);
        }
    }
}
