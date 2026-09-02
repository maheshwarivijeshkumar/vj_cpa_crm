<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Route Hub
|--------------------------------------------------------------------------
| All API routes are versioned and grouped per module.
| Base: /api/v1/{module}
| Auth: Laravel Sanctum (auth:sanctum)
| Tenant: EnsureTenantContext middleware ('tenant' alias)
*/

Route::prefix('v1')->name('api.v1.')->group(function (): void {

    // ── Public / unauthenticated ───────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->group(base_path('routes/api/v1/auth.php'));

    // ── Authenticated + tenant-aware ──────────────────────────────────────
    Route::middleware(['auth:sanctum', 'tenant'])->group(function (): void {

        // Reference data (read-only, no tenant restriction)
        Route::prefix('reference')->name('reference.')->group(base_path('routes/api/v1/reference.php'));

        // Settings
        Route::prefix('settings')->name('settings.')->group(base_path('routes/api/v1/settings.php'));

        // Users & Access
        Route::prefix('users')->name('users.')->group(base_path('routes/api/v1/users.php'));

        // CRM
        Route::prefix('crm')->name('crm.')->group(base_path('routes/api/v1/crm.php'));
        Route::prefix('clients')->name('clients.')->group(base_path('routes/api/v1/clients.php'));
        Route::prefix('contacts')->name('contacts.')->group(base_path('routes/api/v1/contacts.php'));
        Route::prefix('entities')->name('entities.')->group(base_path('routes/api/v1/entities.php'));

        // Practice Management
        Route::prefix('services')->name('services.')->group(base_path('routes/api/v1/services.php'));
        Route::prefix('engagements')->name('engagements.')->group(base_path('routes/api/v1/engagements.php'));
        Route::prefix('filings')->name('filings.')->group(base_path('routes/api/v1/filings.php'));
        Route::prefix('taxation')->name('taxation.')->group(base_path('routes/api/v1/taxation.php'));
        Route::prefix('deadlines')->name('deadlines.')->group(base_path('routes/api/v1/deadlines.php'));
        Route::prefix('workflows')->name('workflows.')->group(base_path('routes/api/v1/workflows.php'));
        Route::prefix('tasks')->name('tasks.')->group(base_path('routes/api/v1/tasks.php'));
        Route::prefix('time')->name('time.')->group(base_path('routes/api/v1/time.php'));
        Route::prefix('calendar')->name('calendar.')->group(base_path('routes/api/v1/calendar.php'));
        Route::prefix('capacity')->name('capacity.')->group(base_path('routes/api/v1/capacity.php'));

        // Documents
        Route::prefix('documents')->name('documents.')->group(base_path('routes/api/v1/documents.php'));
        Route::prefix('signatures')->name('signatures.')->group(base_path('routes/api/v1/signatures.php'));

        // Templates & Communications
        Route::prefix('templates')->name('templates.')->group(base_path('routes/api/v1/templates.php'));
        Route::prefix('communications')->name('communications.')->group(base_path('routes/api/v1/communications.php'));
        Route::prefix('notifications')->name('notifications.')->group(base_path('routes/api/v1/notifications.php'));

        // Proposals
        Route::prefix('proposals')->name('proposals.')->group(base_path('routes/api/v1/proposals.php'));

        // Accounting
        Route::prefix('accounting')->name('accounting.')->group(base_path('routes/api/v1/accounting.php'));
        Route::prefix('banking')->name('banking.')->group(base_path('routes/api/v1/banking.php'));
        Route::prefix('invoicing')->name('invoicing.')->group(base_path('routes/api/v1/invoicing.php'));
        Route::prefix('payments')->name('payments.')->group(base_path('routes/api/v1/payments.php'));
        Route::prefix('expenses')->name('expenses.')->group(base_path('routes/api/v1/expenses.php'));

        // Reports
        Route::prefix('reports')->name('reports.')->group(base_path('routes/api/v1/reports.php'));

        // Imports / Exports
        Route::prefix('imports')->name('imports.')->group(base_path('routes/api/v1/imports.php'));
        Route::prefix('exports')->name('exports.')->group(base_path('routes/api/v1/exports.php'));

        // Webhooks
        Route::prefix('webhooks')->name('webhooks.')->group(base_path('routes/api/v1/webhooks.php'));

        // AI
        Route::prefix('ai')->name('ai.')->group(base_path('routes/api/v1/ai.php'));
    });

    // ── Platform subscriptions, discounts (auth:sanctum, platform + tenant) ──
    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::prefix('subscription')->name('subscription.')
            ->group(base_path('routes/api/v1/subscription.php'));

        Route::prefix('discount')->name('discount.')
            ->group(base_path('routes/api/v1/discount.php'));
    });

    // ── Referral (auth:sanctum + tenant context) ───────────────────────────
    Route::middleware(['auth:sanctum', 'tenant'])
        ->prefix('referral')->name('referral.')
        ->group(base_path('routes/api/v1/referral.php'));

    // ── Platform admin (auth:sanctum, platform_admin type required) ────────
    Route::middleware(['auth:sanctum'])->prefix('platform')->name('platform.')->group(
        base_path('routes/api/v1/platform.php')
    );

    // ── Client portal ──────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'tenant'])->prefix('portal')->name('portal.')->group(
        base_path('routes/api/v1/portal.php')
    );
});
