<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountStatus;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * DiscountSeeder
 *
 * Seeds default platform-level discount codes for development / demo.
 *
 * Included codes:
 *  - WELCOME10  : 10% off, all new tenants, welcome trigger, no expiry
 *  - LAUNCH25   : 25% off, all tenants, manual, valid 90 days from seed date
 *  - TRIAL2PRO  : $50 off first Professional subscription
 */
class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $discounts = [
            // Welcome discount — auto-applied at first subscription
            [
                'code'                  => 'WELCOME10',
                'name'                  => 'Welcome Discount — 10% Off',
                'description'           => 'Automatic 10% discount for all new tenant sign-ups on their first subscription.',
                'type'                  => DiscountType::Percentage->value,
                'value'                 => '10.000000',
                'max_discount_amount'   => null,
                'applicability'         => DiscountApplicability::All->value,
                'applicable_plans'      => null,
                'applicable_tenant_ids' => null,
                'trigger'               => DiscountTrigger::Welcome->value,
                'status'                => DiscountStatus::Active->value,
                'valid_from'            => $now,
                'valid_until'           => null,
                'max_uses'              => null,
                'max_uses_per_tenant'   => 1,
                'uses_count'            => 0,
                'auto_email'            => false,
                'created_by'            => null,
            ],

            // Launch promotional discount — time-limited
            [
                'code'                  => 'LAUNCH25',
                'name'                  => 'Launch Promotion — 25% Off',
                'description'           => '25% off any plan for the first 90 days post-launch.',
                'type'                  => DiscountType::Percentage->value,
                'value'                 => '25.000000',
                'max_discount_amount'   => '100.000000',
                'applicability'         => DiscountApplicability::All->value,
                'applicable_plans'      => null,
                'applicable_tenant_ids' => null,
                'trigger'               => DiscountTrigger::Manual->value,
                'status'                => DiscountStatus::Active->value,
                'valid_from'            => $now,
                'valid_until'           => $now->copy()->addDays(90),
                'max_uses'              => 200,
                'max_uses_per_tenant'   => 1,
                'uses_count'            => 0,
                'auto_email'            => false,
                'created_by'            => null,
            ],

            // Upgrade incentive — fixed amount off Professional
            [
                'code'                  => 'TRIAL2PRO',
                'name'                  => 'Trial to Professional — $50 Off',
                'description'           => '$50 off the first month of Professional when upgrading from Trial.',
                'type'                  => DiscountType::Fixed->value,
                'value'                 => '50.000000',
                'max_discount_amount'   => null,
                'applicability'         => DiscountApplicability::All->value,
                'applicable_plans'      => json_encode(['professional']),
                'applicable_tenant_ids' => null,
                'trigger'               => DiscountTrigger::Manual->value,
                'status'                => DiscountStatus::Active->value,
                'valid_from'            => $now,
                'valid_until'           => $now->copy()->addYear(),
                'max_uses'              => null,
                'max_uses_per_tenant'   => 1,
                'uses_count'            => 0,
                'auto_email'            => false,
                'created_by'            => null,
            ],
        ];

        foreach ($discounts as $discount) {
            // Check if the code already exists to avoid duplicates
            $exists = DB::table('discounts')->where('code', $discount['code'])->exists();
            if ($exists) {
                DB::table('discounts')
                    ->where('code', $discount['code'])
                    ->update(array_merge($discount, ['updated_at' => $now]));
            } else {
                DB::table('discounts')->insert(
                    array_merge($discount, [
                        'id'         => (string) Str::ulid(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]),
                );
            }
        }

        $this->command->info('DiscountSeeder: ' . count($discounts) . ' discounts seeded.');
    }
}
