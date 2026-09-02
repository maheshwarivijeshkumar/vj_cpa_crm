<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Enums\TenantPlan;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SubscriptionSeeder
 *
 * Creates demo subscriptions for development / testing.
 * Assigns one active subscription per demo tenant.
 *
 * Runs after: TenantSeeder
 */
class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /** @var \Illuminate\Support\Collection<int, Tenant> $tenants */
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->command->warn('SubscriptionSeeder: no tenants found — run TenantSeeder first.');
            return;
        }

        $plans = [
            TenantPlan::Starter->value,
            TenantPlan::Professional->value,
        ];

        $count = 0;

        foreach ($tenants as $index => $tenant) {
            // Skip if tenant already has a subscription
            $exists = DB::table('subscriptions')
                ->where('tenant_id', $tenant->id)
                ->where('status', SubscriptionStatus::Active->value)
                ->exists();

            if ($exists) {
                continue;
            }

            $plan = $plans[$index % count($plans)];

            DB::table('subscriptions')->insertOrIgnore([
                'tenant_id'          => $tenant->id,
                'plan'               => $plan,
                'status'             => SubscriptionStatus::Active->value,
                'billing_cycle'      => 'monthly',
                'starts_at'          => $now->copy()->subDays(15),
                'ends_at'            => $now->copy()->addDays(15),
                'trial_ends_at'      => null,
                'amount_paid'        => $plan === TenantPlan::Starter->value ? '49.000000' : '99.000000',
                'discount_amount'    => '0.000000',
                'discount_id'        => null,
                'currency_id'        => DB::table('currencies')->where('currency', 'USD')->value('id'),
                'payment_reference'  => 'DEMO-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'payment_method'     => 'credit_card',
                'cancellation_reason'=> null,
                'cancelled_at'       => null,
                'lapsed_at'          => null,
                'metadata'           => null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);

            $count++;
        }

        $this->command->info("SubscriptionSeeder: {$count} subscriptions seeded.");
    }
}
