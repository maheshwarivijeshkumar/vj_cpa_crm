<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a subscription renewal request from a firm owner.
 */
class RenewSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isFirmUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'discount_code' => ['nullable', 'string', 'max:60'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('discount_code') && $this->discount_code) {
            $this->merge(['discount_code' => strtoupper(trim((string) $this->discount_code))]);
        }
    }
}
