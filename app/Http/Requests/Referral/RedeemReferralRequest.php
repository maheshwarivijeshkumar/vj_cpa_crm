<?php

declare(strict_types=1);

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RedeemReferralRequest
 *
 * Validates a tenant request to redeem accumulated referral rewards
 * (points or credit) against their next subscription renewal.
 */
final class RedeemReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'reward_type'   => ['required', 'string', 'in:points,credit'],
            'amount'        => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'currency'      => ['sometimes', 'string', 'size:3'],
            'subscription_id' => ['sometimes', 'nullable', 'string', 'max:26'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'reward_type.required' => 'Please select a reward type to redeem.',
            'reward_type.in'       => 'Reward type must be either points or credit.',
            'amount.required'      => 'Please enter an amount to redeem.',
            'amount.min'           => 'Redemption amount must be at least 0.01.',
            'amount.max'           => 'Redemption amount exceeds the allowed maximum.',
        ];
    }
}
