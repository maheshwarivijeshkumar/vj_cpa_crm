<?php

declare(strict_types=1);

namespace App\Listeners\Discount;

use App\Events\Discount\DiscountCreated;
use App\Models\Discount;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

/**
 * SendDiscountEmail — emails the discount code to assigned tenants
 * when a discount has auto_email = true.
 *
 * For 'specific' applicability discounts: emails assigned tenants.
 * For 'all' applicability: no auto-email (too broad — requires a campaign).
 */
final class SendDiscountEmail implements ShouldQueue
{
    public string $queue = 'notifications';
    public int    $tries = 3;

    public function __construct(private readonly NotificationService $notifications) {}

    public function handle(DiscountCreated $event): void
    {
        $discount = $event->discount;

        if (! $discount->auto_email) {
            return;
        }

        // For specific-tenant discounts, email the assigned tenants
        if ($discount->applicability->value === 'specific') {
            $this->emailAssignedTenants($discount);
        }
    }

    private function emailAssignedTenants(Discount $discount): void
    {
        $tenantIds = DB::table('discount_tenant_assignments')
            ->where('discount_id', $discount->id)
            ->pluck('tenant_id');

        foreach ($tenantIds as $tenantId) {
            $owner = DB::table('users')
                ->where('tenant_id', $tenantId)
                ->where('user_type', 'firm_owner')
                ->where('status', 'active')
                ->first();

            if ($owner === null) continue;

            try {
                $this->notifications->send(
                    \App\Models\User::find($owner->id),
                    'discount.received',
                    [
                        'discount_code'       => $discount->code,
                        'discount_name'       => $discount->name,
                        'discount_value'      => $discount->type->value === 'percentage'
                            ? $discount->value . '% off'
                            : '$' . number_format((float) $discount->value, 2) . ' off',
                        'valid_until'         => $discount->valid_until?->format('M j, Y') ?? 'No expiry',
                        'trigger_description' => $discount->trigger->label(),
                    ],
                );
            } catch (\Throwable $e) {
                logger()->error('SendDiscountEmail failed', [
                    'discount_id' => $discount->id,
                    'tenant_id'   => $tenantId,
                    'error'       => $e->getMessage(),
                ]);
            }
        }
    }
}
