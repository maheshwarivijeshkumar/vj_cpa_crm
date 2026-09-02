<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\ParallelTesting;
use Illuminate\Validation\Rules\Password;

/**
 * AppServiceProvider — infrastructure configuration only.
 *
 * No domain wiring here. All bindings + event registration live in
 * CpaServiceProvider. This provider handles:
 *   - Eloquent safety settings (strict mode, lazy loading prevention)
 *   - Date immutability
 *   - Password strength defaults
 *   - Gate::before() for platform admins (bypass all policies)
 *   - Parallel test DB isolation
 */
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureEloquent();
        $this->configureDates();
        $this->configurePasswordDefaults();
        $this->configurePlatformAdminGate();
        $this->configureParallelTesting();
    }

    private function configureEloquent(): void
    {
        // Prevent lazy loading in non-production — forces eager loading discipline
        Model::preventLazyLoading(! app()->isProduction());

        // Prevent silently discarding mass-assignment on non-fillable attributes
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        // Prevent accessing missing attributes instead of returning null
        Model::preventAccessingMissingAttributes(! app()->isProduction());

        // Destructive query guard in production
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function configureDates(): void
    {
        // All date casts return CarbonImmutable — prevents accidental mutation
        Date::use(CarbonImmutable::class);
    }

    private function configurePasswordDefaults(): void
    {
        Password::defaults(function (): Password|null {
            return app()->isProduction()
                ? Password::min(10)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
                : Password::min(8);
        });
    }

    private function configurePlatformAdminGate(): void
    {
        // Platform admins bypass every Policy check
        Gate::before(function (\App\Models\User $user, string $ability): ?bool {
            return $user->isPlatformAdmin() ? true : null;
        });
    }

    private function configureParallelTesting(): void
    {
        if (! app()->environment('testing')) {
            return;
        }

        ParallelTesting::setUpTestDatabase(function (string $database, int $token): void {
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
                '--force' => true,
                '--seed'  => false,
            ]);
        });
    }
}
