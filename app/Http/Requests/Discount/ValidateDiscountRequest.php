<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a discount code check request from a tenant user.
 * Used at checkout to preview savings before confirming a subscription.
 */
class ValidateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'code'   => ['required', 'string', 'max:60'],
            'plan'   => ['required', 'string', 'in:trial,starter,professional,enterprise'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'   => 'Please enter a discount code.',
            'plan.required'   => 'Plan is required to validate the discount.',
            'amount.required' => 'Amount is required to calculate the discount.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->code))]);
        }
    }
}
