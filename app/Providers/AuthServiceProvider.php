<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Discount;
use App\Models\Referral;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\BlogPostPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\ReferralPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TenantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * AuthServiceProvider
 *
 * Maps Eloquent models to their Policy classes.
 * Policies gate all mutations — controllers call $this->authorize() or
 * the Gate facade. The frontend's can() is UI-only and never trusted.
 */
final class AuthServiceProvider extends ServiceProvider
{
    /**
     * Model → Policy map.
     * Platform admins bypass all policies automatically via Gate::before()
     * registered in the CpaServiceProvider.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Tenant::class       => TenantPolicy::class,
        User::class         => UserPolicy::class,
        BlogPost::class     => BlogPostPolicy::class,
        Discount::class     => DiscountPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        Referral::class     => ReferralPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
