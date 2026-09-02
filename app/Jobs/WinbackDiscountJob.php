<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\DiscountApplicability;
use App\Enums\DiscountStatus;
use App\Enums\DiscountTrigger;
use App\Enums\DiscountType;
use App\Enums\SubscriptionStatus;
use App\Models\Discount;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\Notification\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * WinbackDiscountJob
 *
 * Runs daily. Identifies tenants that lapsed 30–60 days ago and have
 * NOT yet received a winback discount. Creates a tenant-specific 20%-off
 * discount code valid for 30 days and sends a winback email.
 *
 * Rules (constants below):
 *  - Window: 30–60 days post-lapse.
 *  - Discount: 20% off, expires in 30 days.
 *  - One winback per lapse event (idempotency via DiscountTrigger::Winback).
 */
final class WinbackDiscountJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Days after lapse before the first winback offer is sent. */
    private const WINBACK_DAYS_MIN = 30;

    /** Days after lapse — after this the tenant is considered gone. */
    private const WINBACK_DAYS_MAX = 60;

    /** Percentage discount to offer. */
    private const WINBACK_PERCENT = 20;

    /** How many days the generated discount code is valid. */
    private const DISCOUNT_VALIDITY_DAYS = 30;

    public int $timeout = 180;
    public int $tries   = 2;

    public function __construct()
    {
        $this->onQueue('subscriptions');
    }

    public function handle(NotificationService $notificationService): void
    {
        $now     = now();
        $minDate = $now->copy()->subDays(self::WINBACK_DAYS_MAX);
        $maxDate = $now->copy()->subDays(self::WINBACK_DAYS_MIN);

        // Find lapsed subscriptions in the 30–60 day window
        Subscription::query()
            ->where('status', SubscriptionStatus::Lapsed)
            ->whereBetween('lapsed_at', [$minDate, $maxDate])
            ->chunkById(50, function (\Illuminate\Support\Collection $subscriptions) use ($now, $notificationService): void {
                foreach ($subscriptions as $subscription) {
                    $tenant = $subscription->tenant;

                    if ($tenant === null) {
                        continue;
                    }

                    // Idempotency: skip if a winback discount already exists for this tenant+lapse
                    $alreadySent = Discount::query()
                        ->where('trigger', DiscountTrigger::Winback)
                        ->where('applicability', DiscountApplicability::Specific)
                        ->whereJsonContains('applicable_tenant_ids', [$tenant->id])
                        ->where('created_at', '>=', $subscription->lapsed_at)
                        ->exists();

                    if ($alreadySent) {
                        continue;
                    }

                    // Generate unique code
                    $code = 'WINBACK-' . strtoupper(Str::random(6)) . '-' . $tenant->id;

                    /** @var Discount $discount */
                    $discount = Discount::create([
                        'code'                  => $code,
                        'name'                  => "Win-back offer for {$tenant->name}",
                        'description'           => 'Exclusive win-back discount. Limited time.',
                        'type'                  => DiscountType::Percentage,
                        'value'                 => self::WINBACK_PERCENT,
                        'max_discount_amount'   => null,
                        'applicability'         => DiscountApplicability::Specific,
                        'applicable_tenant_ids' => json_encode([$tenant->id]),
                        'applicable_plans'      => null,
                        'trigger'               => DiscountTrigger::Winback,
                        'status'                => DiscountStatus::Active,
                        'valid_from'            => $now,
                        'valid_until'           => $now->copy()->addDays(self::DISCOUNT_VALIDITY_DAYS),
                        'max_uses'              => 1,
                        'max_uses_per_tenant'   => 1,
                        'uses_count'            => 0,
                        'auto_email'            => true,
                        'created_by'            => null, // system-generated
                    ]);

                    // Send winback email via notification system
                    $notificationService->send(
                        key:       'discount.winback',
                        channel:   'email',
                        recipient: $tenant->ownerUser(),
                        variables: [
                            'discount_code'    => $code,
                            'discount_value'   => self::WINBACK_PERCENT . '%',
                            'valid_until'      => $now->copy()->addDays(self::DISCOUNT_VALIDITY_DAYS)->format('M j, Y'),
                            'tenant_name'      => $tenant->name,
                            'resubscribe_url'  => config('app.url') . '/pricing',
                        ],
                    );

                    Log::info('WinbackDiscountJob: winback discount created & email sent', [
                        'tenant_id'   => $tenant->id,
                        'discount_id' => $discount->id,
                        'code'        => $code,
                    ]);
                }
            });
    }

    public function uniqueId(): string
    {
        return 'winback-discount-' . now()->format('Y-m-d');
    }
}
