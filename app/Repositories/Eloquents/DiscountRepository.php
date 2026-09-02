<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Filters\DiscountFilters;
use App\Models\Discount;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

final class DiscountRepository extends BaseRepository implements DiscountRepositoryInterface
{
    protected function model(): string
    {
        return Discount::class;
    }

    protected function allowedSortColumns(): array
    {
        return ['id', 'code', 'name', 'type', 'value', 'status', 'trigger', 'uses_count', 'valid_until', 'created_at'];
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        DiscountFilters::applyTo($query, Request::instance());
    }

    public function findByCode(string $code): ?Discount
    {
        return Discount::query()->where('code', strtoupper($code))->first();
    }

    public function usableForTenant(int $tenantId, string $plan): Collection
    {
        return Discount::query()
            ->usable()
            ->forTenant($tenantId, $plan)
            ->with(['currency:id,currency,currency_symbol'])
            ->get();
    }

    public function incrementUsesCount(string $discountId): void
    {
        // Atomic — prevents lost-update race condition
        DB::table('discounts')
            ->where('id', $discountId)
            ->increment('uses_count');

        // Check if depleted and update status
        DB::statement(
            "UPDATE discounts SET status = 'depleted'
             WHERE id = ? AND max_uses IS NOT NULL AND uses_count >= max_uses AND status = 'active'",
            [$discountId],
        );
    }

    public function syncTenantAssignments(string $discountId, array $tenantIds): void
    {
        $now = now();

        DB::transaction(function () use ($discountId, $tenantIds, $now): void {
            DB::table('discount_tenant_assignments')
                ->where('discount_id', $discountId)
                ->delete();

            if (! empty($tenantIds)) {
                $rows = array_map(fn ($id) => [
                    'discount_id' => $discountId,
                    'tenant_id'   => $id,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ], $tenantIds);

                DB::table('discount_tenant_assignments')->insert($rows);
            }
        });
    }

    public function expiringWithin(int $days): Collection
    {
        return Discount::query()
            ->where('status', 'active')
            ->whereNotNull('valid_until')
            ->where('valid_until', '<=', now()->addDays($days))
            ->where('valid_until', '>=', now())
            ->get();
    }

    public function totalSaved(string $discountId): string
    {
        return (string) (DB::table('discount_usages')
            ->where('discount_id', $discountId)
            ->sum('discount_amount') ?? '0');
    }
}
