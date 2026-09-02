<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\Enums\TenantPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates creating a new subscription (platform admin or payment webhook).
 */
class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Platform admins create subscriptions for tenants, or payment webhook
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'tenant_id'         => ['required', 'integer', 'exists:tenants,id'],
            'plan'              => ['required', Rule::enum(TenantPlan::class)],
            'starts_at'         => ['required', 'date'],
            'ends_at'           => ['required', 'date', 'after:starts_at'],
            'amount_paid'       => ['required', 'numeric', 'min:0'],
            'billing_cycle'     => ['required', 'in:monthly,annual'],
            'currency_id'       => ['nullable', 'integer', 'exists:currencies,id'],
            'discount_code'     => ['nullable', 'string', 'max:60'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_method'    => ['nullable', 'string', 'max:40'],
            'trial_ends_at'     => ['nullable', 'date'],
            'metadata'          => ['nullable', 'array'],
        ];
    }
}
