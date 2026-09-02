<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\TenantPlan;
use Illuminate\Support\Carbon;

/**
 * Immutable DTO for creating or renewing a Subscription.
 */
final readonly class SubscriptionData
{
    public function __construct(
        public int         $tenantId,
        public TenantPlan  $plan,
        public Carbon      $startsAt,
        public Carbon      $endsAt,
        public string      $amountPaid,      // DECIMAL string
        public string      $billingCycle,    // monthly|annual
        public ?int        $currencyId       = null,
        public ?string     $discountId       = null, // ULID
        public string      $discountAmount   = '0.000000',
        public ?string     $paymentReference = null,
        public ?string     $paymentMethod    = null,
        public ?Carbon     $trialEndsAt      = null,
        public array       $metadata         = [],
    ) {}

    /** @param array<string,mixed> $validated */
    public static function fromArray(array $validated): self
    {
        return new self(
            tenantId:         (int) $validated['tenant_id'],
            plan:             TenantPlan::from($validated['plan']),
            startsAt:         Carbon::parse($validated['starts_at']),
            endsAt:           Carbon::parse($validated['ends_at']),
            amountPaid:       (string) $validated['amount_paid'],
            billingCycle:     $validated['billing_cycle'] ?? 'monthly',
            currencyId:       $validated['currency_id']       ?? null,
            discountId:       $validated['discount_id']       ?? null,
            discountAmount:   (string) ($validated['discount_amount'] ?? 0),
            paymentReference: $validated['payment_reference'] ?? null,
            paymentMethod:    $validated['payment_method']    ?? null,
            trialEndsAt:      isset($validated['trial_ends_at'])
                ? Carbon::parse($validated['trial_ends_at']) : null,
            metadata:         $validated['metadata'] ?? [],
        );
    }

    public function toModelArray(): array
    {
        return array_filter([
            'tenant_id'         => $this->tenantId,
            'plan'              => $this->plan->value,
            'status'            => 'active',
            'starts_at'         => $this->startsAt->toDateString(),
            'ends_at'           => $this->endsAt->toDateString(),
            'amount_paid'       => $this->amountPaid,
            'billing_cycle'     => $this->billingCycle,
            'currency_id'       => $this->currencyId,
            'discount_id'       => $this->discountId,
            'discount_amount'   => $this->discountAmount,
            'payment_reference' => $this->paymentReference,
            'payment_method'    => $this->paymentMethod,
            'trial_ends_at'     => $this->trialEndsAt?->toDateString(),
            'metadata'          => empty($this->metadata) ? null : $this->metadata,
        ], fn ($v) => $v !== null);
    }
}
