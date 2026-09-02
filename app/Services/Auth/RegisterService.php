<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Events\Auth\UserRegistered;
use App\Events\Tenant\TenantCreated;
use App\Models\Language;
use App\Models\Tenant;
use App\Models\Timezone;
use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * RegisterService — all business logic for self-service firm registration.
 *
 * Responsibilities:
 *  - Create Tenant (accounting firm) with trial plan
 *  - Create the firm owner User inside the same DB transaction
 *  - Fire TenantCreated AFTER commit (listeners: SeedTenantRoles, SeedTenantModules)
 *  - Fire UserRegistered (listeners: SendWelcomeEmail, SendEmailVerification)
 *  - Write audit log
 *  - Log the new user in
 *
 * The controller only decides WHERE to redirect.
 */
final class RegisterService
{
    /**
     * Register a new firm and its owner user.
     *
     * @param  array{
     *     first_name: string,
     *     last_name:  string,
     *     firm_name:  string,
     *     email:      string,
     *     password:   string,
     * } $data  Validated data from RegisterRequest
     *
     * @return User  The newly created and logged-in firm owner
     */
    public function register(array $data): User
    {
        // Resolve default locale references (Canadian defaults)
        $timezoneId = $this->defaultTimezoneId();
        $languageId = $this->defaultLanguageId();

        [$tenant, $owner] = DB::transaction(
            function () use ($data, $timezoneId, $languageId): array {
                $tenant = $this->createTenant($data, $timezoneId, $languageId);
                $owner  = $this->createOwner($data, $tenant->id, $timezoneId, $languageId);
                return [$tenant, $owner];
            },
        );

        // Fire events AFTER commit — never inside transaction
        // TenantCreated → SeedTenantRoles + SeedTenantModules (async)
        DB::afterCommit(fn () => TenantCreated::dispatch($tenant, $owner));

        // UserRegistered → SendWelcomeEmail + SendEmailVerificationOnRegister (async)
        UserRegistered::dispatch($owner, requiresEmailVerification: true);

        Auth::login($owner);

        AuditService::logAuth('register', $owner->email, true);

        return $owner;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function createTenant(array $data, ?int $timezoneId, ?int $languageId): Tenant
    {
        return Tenant::create([
            'uuid'                    => (string) Str::uuid(),
            'name'                    => $data['firm_name'],
            'slug'                    => Str::slug($data['firm_name']) . '-' . Str::lower(Str::random(4)),
            'email'                   => $data['email'],
            'timezone_id'             => $timezoneId,
            'language_id'             => $languageId,
            'fiscal_year_start_month' => 1,
            'fiscal_year_start_day'   => 1,
            'plan'                    => 'trial',
            'status'                  => 'trial',
            'trial_ends_at'           => now()->addDays((int) config('cpa.trial_days', 14)),
            'is_active'               => true,
        ]);
    }

    private function createOwner(array $data, int $tenantId, ?int $timezoneId, ?int $languageId): User
    {
        return User::create([
            'uuid'                 => (string) Str::uuid(),
            'tenant_id'            => $tenantId,
            'first_name'           => $data['first_name'],
            'last_name'            => $data['last_name'],
            'email'                => $data['email'],
            'password'             => Hash::make($data['password']),
            'user_type'            => 'firm_owner',
            'status'               => 'active',
            'must_change_password' => false,
            'email_verified_at'    => null, // Sent via SendEmailVerificationOnRegister
            'timezone_id'          => $timezoneId,
            'language_id'          => $languageId,
        ]);
    }

    private function defaultTimezoneId(): ?int
    {
        // Cache in memory for the duration of this request
        static $id = null;
        return $id ??= Timezone::where('zone_name', 'America/Toronto')->value('id');
    }

    private function defaultLanguageId(): ?int
    {
        static $id = null;
        return $id ??= Language::where('code', 'en')->value('id');
    }
}
