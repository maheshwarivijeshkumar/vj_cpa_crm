<?php

use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\MarketingController;
use App\Http\Controllers\Web\SeoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── SEO / crawler files ───────────────────────────────────────────────────────
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// ── Marketing / public pages ──────────────────────────────────────────────────
Route::get('/',         [MarketingController::class, 'home'])->name('home');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/pricing',  [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/about',    [MarketingController::class, 'about'])->name('about');
Route::get('/contact',  [MarketingController::class, 'contact'])->name('contact');
Route::post('/contact', [MarketingController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:10,1');
Route::get('/privacy',  [MarketingController::class, 'privacy'])->name('privacy');
Route::get('/terms',    [MarketingController::class, 'terms'])->name('terms');
Route::get('/security', [MarketingController::class, 'security'])->name('security');
Route::get('/demo',     [MarketingController::class, 'demo'])->name('demo');
Route::post('/demo/request', [MarketingController::class, 'demoRequest'])->name('demo.request')->middleware('throttle:5,1');

// ── Blog (public) ─────────────────────────────────────────────────────────────
Route::get('/blog',        [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// ── Referral click handler (public — no auth required) ───────────────────────
Route::get('/r/{code}', function (string $code) {
    // Store referral code in session for linking after signup
    session(['referral_code' => $code, 'referral_clicked_at' => now()->toIso8601String()]);

    /** @var \App\Services\Referral\ReferralService $service */
    $service = app(\App\Services\Referral\ReferralService::class);

    try {
        $referral = $service->handleClick($code, request());
        session(['referral_id' => $referral->id]);
    } catch (\DomainException) {
        // Invalid/inactive link — still redirect to register
    }

    return redirect()->route('register');
})->name('referral.click');

// ── Auth routes ───────────────────────────────────────────────────────────────
require __DIR__ . '/auth.php';
// ── Authenticated application ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'tenant'])->group(function (): void {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard/Index'))->name('dashboard');
    // ── Settings ─────────────────────────────────────────────────────────────
    // Redirect /settings → /settings/profile
    Route::get('/settings', fn () => redirect()->route('settings.profile'))->name('settings');
    Route::prefix('settings')->name('settings.')->group(function (): void {
        Route::get('/profile',        fn () => Inertia::render('Settings/Profile',       ['user' => auth()->user()]))->name('profile');
        Route::get('/security',       fn () => Inertia::render('Settings/Security',      ['twoFactorEnabled' => !empty(auth()->user()?->two_factor_secret)]))->name('security');
        Route::get('/notifications',  fn () => Inertia::render('Settings/Notifications', ['user' => auth()->user()]))->name('notifications');

        // Form submissions (web routes so Inertia handles redirect + flash)
        Route::patch('/profile',  [App\Http\Controllers\Settings\ProfileController::class,  'update'])->name('profile.update');
        Route::patch('/password', [App\Http\Controllers\Settings\SecurityController::class, 'updatePassword'])->name('password.update');
    });

    // ── Team / User management (tenant users only) ───────────────────────────
    Route::prefix('portal/team')->name('team.')->group(function (): void {
        Route::get('/',             [App\Http\Controllers\Tenant\TeamController::class, 'index'])->name('index');
        Route::post('/invite',      [App\Http\Controllers\Tenant\TeamController::class, 'invite'])->name('invite');
        Route::patch('/{id}',       [App\Http\Controllers\Tenant\TeamController::class, 'update'])->name('update');
        Route::delete('/{id}',      [App\Http\Controllers\Tenant\TeamController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/resend-invite', [App\Http\Controllers\Tenant\TeamController::class, 'resendInvite'])->name('resend-invite');
    });

    // ── Offices (tenant firm owner only) ─────────────────────────────────────
    Route::prefix('portal/offices')->name('offices.')->group(function (): void {
        Route::get('/',                  [App\Http\Controllers\Tenant\OfficeController::class, 'index'])->name('index');
        Route::post('/',                 [App\Http\Controllers\Tenant\OfficeController::class, 'store'])->name('store');
        Route::patch('/{id}',            [App\Http\Controllers\Tenant\OfficeController::class, 'update'])->name('update');
        Route::delete('/{id}',           [App\Http\Controllers\Tenant\OfficeController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/settings',     [App\Http\Controllers\Tenant\OfficeController::class, 'settings'])->name('settings');
        Route::patch('/{id}/settings',   [App\Http\Controllers\Tenant\OfficeController::class, 'saveSettings'])->name('settings.save');
    });

    // ── Two-Factor Auth setup (web flow) ─────────────────────────────────────
    Route::get('/two-factor/setup', fn () => Inertia::render('Auth/TwoFactor/Setup'))->name('two-factor.setup');

    // CRM
    Route::get('/clients',        fn () => Inertia::render('Clients/Index'))->name('clients.index');
    Route::get('/clients/create', fn () => Inertia::render('Clients/Create'))->name('clients.create');

    // Catch-all SPA shell
    Route::get('/{any}', fn () => Inertia::render('Dashboard/Index'))
        ->where('any', '^(?!api|platform|portal|robots\.txt|sitemap\.xml).*$')
        ->name('catch-all');
});

// ── Platform admin ────────────────────────────────────────────────────────────
Route::prefix('platform')->name('platform.')->middleware(['auth', 'verified'])->group(
    base_path('routes/platform.php')
);

// ── Client portal ─────────────────────────────────────────────────────────────
Route::prefix('portal')->name('portal.')->middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/',             fn () => Inertia::render('Portal/Dashboard'))->name('dashboard');
    Route::get('/subscription', fn () => Inertia::render('Portal/Subscription'))->name('subscription');
    Route::get('/referrals',    fn () => Inertia::render('Portal/Referrals'))->name('referrals');
    Route::get('/profile',      fn () => Inertia::render('Portal/Profile', ['user' => auth()->user()]))->name('profile');
    // Business module portal pages — stub until those modules are built
    Route::get('/documents',    fn () => Inertia::render('Portal/Documents'))->name('documents');
    Route::get('/messages',     fn () => Inertia::render('Portal/Messages'))->name('messages');
    Route::get('/invoices',     fn () => Inertia::render('Portal/Invoices'))->name('invoices');
    Route::get('/filings',      fn () => Inertia::render('Portal/Filings'))->name('filings');
});
