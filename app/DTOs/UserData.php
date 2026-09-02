<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\UserStatus;
use App\Enums\UserType;

/**
 * Immutable data transfer object for creating or updating a User.
 * Constructed from validated Form Request data — never directly from raw input.
 */
final readonly class UserData
{
    public function __construct(
        public string      $firstName,
        public string      $lastName,
        public string      $email,
        public UserType    $userType,
        public UserStatus  $status,
        public ?string     $username        = null,
        public ?string     $phone           = null,
        public ?string     $password        = null,   // null = don't change
        public ?int        $tenantId        = null,
        public ?int        $officeId        = null,
        public ?int        $timezoneId      = null,
        public ?int        $languageId      = null,
        public ?int        $currencyId      = null,
        public ?string     $dateFormat      = null,
        public bool        $mustChangePassword = false,
    ) {}

    /**
     * Create from a validated array (Form Request output).
     *
     * @param array<string,mixed> $validated
     */
    public static function fromArray(array $validated): self
    {
        return new self(
            firstName:          $validated['first_name'],
            lastName:           $validated['last_name'],
            email:              $validated['email'],
            userType:           UserType::from($validated['user_type'] ?? 'firm_user'),
            status:             UserStatus::from($validated['status'] ?? 'active'),
            username:           $validated['username']     ?? null,
            phone:              $validated['phone']        ?? null,
            password:           $validated['password']    ?? null,
            tenantId:           $validated['tenant_id']   ?? null,
            officeId:           $validated['office_id']   ?? null,
            timezoneId:         $validated['timezone_id'] ?? null,
            languageId:         $validated['language_id'] ?? null,
            currencyId:         $validated['currency_id'] ?? null,
            dateFormat:         $validated['date_format'] ?? null,
            mustChangePassword: (bool) ($validated['must_change_password'] ?? false),
        );
    }

    /** Return an array suitable for Model::create() / Model::update(). */
    public function toModelArray(): array
    {
        $data = [
            'first_name'           => $this->firstName,
            'last_name'            => $this->lastName,
            'email'                => $this->email,
            'user_type'            => $this->userType->value,
            'status'               => $this->status->value,
            'username'             => $this->username,
            'phone'                => $this->phone,
            'tenant_id'            => $this->tenantId,
            'office_id'            => $this->officeId,
            'timezone_id'          => $this->timezoneId,
            'language_id'          => $this->languageId,
            'currency_id'          => $this->currencyId,
            'date_format'          => $this->dateFormat,
            'must_change_password' => $this->mustChangePassword,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password; // must be hashed by caller
        }

        return array_filter($data, fn ($v) => $v !== null);
    }
}
