<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a subscription cancellation request.
 * Requires a cancellation reason for audit purposes.
 */
class CancelSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Firm owners cancel their own, platform admins cancel any
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'reason'      => ['required', 'string', 'min:10', 'max:500'],
            'immediately' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Please provide a reason for cancellation.',
            'reason.min'      => 'Please provide a more descriptive cancellation reason.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->mergeIfMissing(['immediately' => false]);
    }
}
