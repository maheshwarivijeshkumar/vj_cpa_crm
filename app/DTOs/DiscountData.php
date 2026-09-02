<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use Illuminate\Support\Carbon;

/**
 * Immutable DTO for creating or updating a Discount.
 */
final readonly class DiscountData
{
    public function __construct(
        public string               $code,
        public string               $name,
        public DiscountType         $type,
        public string               $value,           // DECIMAL string
        public DiscountApplicability $applicability,
        public DiscountTrigger      $trigger,
        public ?string              $description          = null,
        public ?string              $maxDiscountAmount     = null,
        public ?int                 $currencyId            = null,
        public ?string              $applicablePlans       = null, // CSV
        public ?Carbon              $validFrom             = null,
        public ?Carbon              $validUntil            = null,
        public ?int                 $maxUses               = null,
        public int                  $maxUsesPerTenant      = 1,
        public bool                 $autoEmail             = false,
        public array                $tenantIds             = [], // for specific applicability
    ) {}

    /** @param array<string,mixed> $validated */
    public static function fromArray(array $validated): self
    {
        return new self(
            code:              strtoupper(trim($validated['code'])),
            name:              $validated['name'],
            type:              DiscountType::from($validated['type']),
            value:             (string) $validated['value'],
            applicability:     DiscountApplicability::from($validated['applicability'] ?? 'all'),
            trigger:           DiscountTrigger::from($validated['trigger'] ?? 'manual'),
            description:       $validated['description']           ?? null,
            maxDiscountAmount: isset($validated['max_discount_amount'])
                ? (string) $validated['max_discount_amount'] : null,
            currencyId:        $validated['currency_id']           ?? null,
            applicablePlans:   isset($validated['applicable_plans'])
                ? implode(',', (array) $validated['applicable_plans']) : null,
            validFrom:         isset($validated['valid_from'])
                ? Carbon::parse($validated['valid_from']) : null,
            validUntil:        isset($validated['valid_until'])
                ? Carbon::parse($validated['valid_until']) : null,
            maxUses:           $validated['max_uses']              ?? null,
            maxUsesPerTenant:  (int) ($validated['max_uses_per_tenant'] ?? 1),
            autoEmail:         (bool) ($validated['auto_email']    ?? false),
            tenantIds:         $validated['tenant_ids']            ?? [],
        );
    }

    public function toModelArray(): array
    {
        return array_filter([
            'code'                => $this->code,
            'name'                => $this->name,
            'type'                => $this->type->value,
            'value'               => $this->value,
            'applicability'       => $this->applicability->value,
            'trigger'             => $this->trigger->value,
            'description'         => $this->description,
            'max_discount_amount' => $this->maxDiscountAmount,
            'currency_id'         => $this->currencyId,
            'applicable_plans'    => $this->applicablePlans,
            'valid_from'          => $this->validFrom?->toDateTimeString(),
            'valid_until'         => $this->validUntil?->toDateTimeString(),
            'max_uses'            => $this->maxUses,
            'max_uses_per_tenant' => $this->maxUsesPerTenant,
            'auto_email'          => $this->autoEmail,
            'status'              => 'active',
        ], fn ($v) => $v !== null);
    }
}
