<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\NotificationServiceInterface;

// Auth
use App\Events\Auth\PasswordChanged;
use App\Events\Auth\UserRegistered;
use App\Events\Filing\FilingDeadlineApproached;
use App\Events\Tenant\TenantCreated;
use App\Listeners\Auth\LogPasswordChange;
use App\Listeners\Auth\SendEmailVerificationOnRegister;
use App\Listeners\Auth\SendWelcomeEmail;
use App\Listeners\Filing\SendDeadlineReminderNotification;
use App\Listeners\Tenant\SeedTenantModules;
use App\Listeners\Tenant\SeedTenantRoles;

// Subscription
use App\Events\Subscription\SubscriptionCreated;
use App\Events\Subscription\SubscriptionLapsed;
use App\Listeners\Subscription\SendSubscriptionConfirmationEmail;
use App\Listeners\Subscription\TriggerWinbackOnLapse;

// Discount
use App\Events\Discount\DiscountCreated;
use App\Listeners\Discount\SendDiscountEmail;

// Referral
use App\Events\Referral\ReferralRewarded;
use App\Listeners\Referral\NotifyReferrerOnReward;

// Repository Contracts
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use App\Repositories\Contracts\DiscountRepositoryInterface;
use App\Repositories\Contracts\ReferralRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

// Repository Implementations
use App\Repositories\Eloquents\BlogPostRepository;
use App\Repositories\Eloquents\DiscountRepository;
use App\Repositories\Eloquents\ReferralRepository;
use App\Repositories\Eloquents\SubscriptionRepository;
use App\Repositories\Eloquents\TenantRepository;
use App\Repositories\Eloquents\UserRepository;

// Services
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordResetService;
use App\Services\Auth\RegisterService;
use App\Services\Auth\TwoFactorService;
use App\Services\Discount\DiscountService;
use App\Services\Notification\NotificationService;
use App\Services\Referral\ReferralService;
use App\Services\Seo\SeoService;
use App\Services\Settings\SettingsService;
use App\Services\Subscription\SubscriptionService;

use App\Support\PaginationHelper;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * CpaServiceProvider — the single domain-wiring hub.
 *
 * Responsibilities:
 *  - Bind interfaces → concrete implementations
 *  - Register singletons
 *  - Wire Events → Listeners
 *  - Configure rate limiters
 *
 * Keep AppServiceProvider infrastructure-only.
 */
final class CpaServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $singletons = [
        // Infrastructure
        SettingsService::class           => SettingsService::class,
        SeoService::class                => SeoService::class,
        PaginationHelper::class          => PaginationHelper::class,

        // Auth services
        LoginService::class              => LoginService::class,
        RegisterService::class           => RegisterService::class,
        PasswordResetService::class      => PasswordResetService::class,
        EmailVerificationService::class  => EmailVerificationService::class,
        TwoFactorService::class          => TwoFactorService::class,
    ];

    public function register(): void
    {
        // ── Repository bindings ───────────────────────────────────────────────
        $this->app->bind(UserRepositoryInterface::class,         UserRepository::class);
        $this->app->bind(TenantRepositoryInterface::class,       TenantRepository::class);
        $this->app->bind(BlogPostRepositoryInterface::class,     BlogPostRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(DiscountRepositoryInterface::class,     DiscountRepository::class);
        $this->app->bind(ReferralRepositoryInterface::class,     ReferralRepository::class);

        // ── Service bindings ──────────────────────────────────────────────────
        $this->app->singleton(NotificationServiceInterface::class, NotificationService::class);
        $this->app->singleton(DiscountService::class,              DiscountService::class);
        $this->app->singleton(ReferralService::class,              ReferralService::class);
        $this->app->singleton(SubscriptionService::class,          SubscriptionService::class);
    }

    public function boot(): void
    {
        $this->registerEvents();
        $this->registerRateLimiters();
    }

    // ── Events → Listeners ────────────────────────────────────────────────────

    private function registerEvents(): void
    {
        // Auth
        Event::listen(UserRegistered::class,   SendWelcomeEmail::class);
        Event::listen(UserRegistered::class,   SendEmailVerificationOnRegister::class);
        Event::listen(PasswordChanged::class,  LogPasswordChange::class);

        // Tenant lifecycle
        Event::listen(TenantCreated::class, SeedTenantRoles::class);
        Event::listen(TenantCreated::class, SeedTenantModules::class);

        // Filing
        Event::listen(FilingDeadlineApproached::class, SendDeadlineReminderNotification::class);

        // Subscription lifecycle
        Event::listen(SubscriptionCreated::class, SendSubscriptionConfirmationEmail::class);
        Event::listen(SubscriptionLapsed::class,  TriggerWinbackOnLapse::class);

        // Discount
        Event::listen(DiscountCreated::class, SendDiscountEmail::class);

        // Referral
        Event::listen(ReferralRewarded::class, NotifyReferrerOnReward::class);
    }

    // ── Rate limiters ─────────────────────────────────────────────────────────

    private function registerRateLimiters(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('auth', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('contact', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('platform', function (Request $request): Limit {
            return Limit::perMinute(300)->by($request->user()?->id ?? $request->ip());
        });
    }
}
