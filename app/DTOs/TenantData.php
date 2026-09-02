<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;

/**
 * Immutable DTO for creating or updating a Tenant (accounting firm).
 */
final readonly class TenantData
{
    public function __construct(
        public string       $name,
        public string       $email,
        public TenantPlan   $plan,
        public TenantStatus $status,
        public ?string      $phone                  = null,
        public ?string      $website                = null,
        public ?string      $addressLine1           = null,
        public ?string      $addressLine2           = null,
        public ?string      $city                   = null,
        public ?string      $state                  = null,
        public ?string      $postalCode             = null,
        public ?int         $countryId              = null,
        public ?int         $timezoneId             = null,
        public ?int         $languageId             = null,
        public ?int         $currencyId             = null,
        public int          $fiscalYearStartMonth   = 1,
        public int          $fiscalYearStartDay     = 1,
    ) {}

    /**
     * @param array<string,mixed> $validated
     */
    public static function fromArray(array $validated): self
    {
        return new self(
            name:                 $validated['name'],
            email:                $validated['email'],
            plan:                 TenantPlan::from($validated['plan'] ?? 'trial'),
            status:               TenantStatus::from($validated['status'] ?? 'trial'),
            phone:                $validated['phone']           ?? null,
            website:              $validated['website']         ?? null,
            addressLine1:         $validated['address_line1']   ?? null,
            addressLine2:         $validated['address_line2']   ?? null,
            city:                 $validated['city']            ?? null,
            state:                $validated['state']           ?? null,
            postalCode:           $validated['postal_code']     ?? null,
            countryId:            $validated['country_id']      ?? null,
            timezoneId:           $validated['timezone_id']     ?? null,
            languageId:           $validated['language_id']     ?? null,
            currencyId:           $validated['currency_id']     ?? null,
            fiscalYearStartMonth: (int) ($validated['fiscal_year_start_month'] ?? 1),
            fiscalYearStartDay:   (int) ($validated['fiscal_year_start_day']   ?? 1),
        );
    }

    public function toModelArray(): array
    {
        return array_filter([
            'name'                    => $this->name,
            'email'                   => $this->email,
            'plan'                    => $this->plan->value,
            'status'                  => $this->status->value,
            'phone'                   => $this->phone,
            'website'                 => $this->website,
            'address_line1'           => $this->addressLine1,
            'address_line2'           => $this->addressLine2,
            'city'                    => $this->city,
            'state'                   => $this->state,
            'postal_code'             => $this->postalCode,
            'country_id'              => $this->countryId,
            'timezone_id'             => $this->timezoneId,
            'language_id'             => $this->languageId,
            'currency_id'             => $this->currencyId,
            'fiscal_year_start_month' => $this->fiscalYearStartMonth,
            'fiscal_year_start_day'   => $this->fiscalYearStartDay,
        ], fn ($v) => $v !== null);
    }
}
