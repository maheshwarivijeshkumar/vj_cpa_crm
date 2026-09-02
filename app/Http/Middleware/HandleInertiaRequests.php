<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     * These are available in every Vue page as `$page.props.*`
     */
    public function share(Request $request): array
    {
        $user   = $request->user();
        $tenant = TenantContext::get();

        return array_merge(parent::share($request), [

            // ── Auth ──────────────────────────────────────────────────────────
            'auth' => [
                'user' => $user ? [
                    'id'                   => $user->id,
                    'uuid'                 => $user->uuid,
                    'name'                 => $user->full_name,
                    'first_name'           => $user->first_name,
                    'last_name'            => $user->last_name,
                    'email'                => $user->email,
                    'username'             => $user->username,
                    'avatar_path'          => $user->avatar_path,
                    'user_type'            => $user->user_type,
                    'status'               => $user->status,
                    'must_change_password' => $user->must_change_password,
                    'two_factor_enabled'   => $user->two_factor_enabled,
                    'timezone_id'          => $user->timezone_id,
                    'language_id'          => $user->language_id,
                    'currency_id'          => $user->currency_id,
                    'date_format'          => $user->date_format,
                    'preferences'          => $user->preferences ?? [],
                ] : null,

                // Flat permission array for frontend can() helper
                'permissions' => $user
                    ? $user->roles()
                        ->with('permissions')
                        ->get()
                        ->flatMap(fn ($role) => $role->permissions->pluck('name'))
                        ->unique()
                        ->values()
                        ->toArray()
                    : [],

                // Role slugs for role-based UI checks
                'roles' => $user
                    ? $user->roles()->pluck('slug')->toArray()
                    : [],
            ],

            // ── Tenant ────────────────────────────────────────────────────────
            'tenant' => $tenant ? [
                'id'          => $tenant->id,
                'uuid'        => $tenant->uuid,
                'name'        => $tenant->name,
                'slug'        => $tenant->slug,
                'plan'        => $tenant->plan,
                'status'      => $tenant->status,
                'logo_path'   => $tenant->logo_path,
                'brand_colors'=> $tenant->brand_colors,
                'currency_id' => $tenant->currency_id,
                'timezone_id' => $tenant->timezone_id,
                'language_id' => $tenant->language_id,
                'fiscal_year_start_month' => $tenant->fiscal_year_start_month,
                'fiscal_year_start_day'   => $tenant->fiscal_year_start_day,
            ] : null,

            // ── Flash messages ────────────────────────────────────────────────
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info'    => fn () => $request->session()->get('info'),
            ],

            // ── App meta ──────────────────────────────────────────────────────
            'app' => [
                'name'    => config('app.name', 'VJ CPA CRM'),
                'env'     => config('app.env'),
                'locale'  => app()->getLocale(),
                'version' => config('app.version', '1.0.0'),
            ],

            // ── SEO defaults (pages override via SeoService::make()) ──────────
            'seo' => [
                'title'       => config('app.name', 'VJ CPA CRM'),
                'description' => config('cpa.seo.default_description', ''),
                'canonical'   => url($request->getPathInfo()),
                'robots'      => 'index,follow',
                'og'          => [
                    'type'      => 'website',
                    'site_name' => config('app.name', 'VJ CPA CRM'),
                    'image'     => url(config('cpa.seo.default_image', '/images/og-default.png')),
                ],
                'twitter' => [
                    'card' => 'summary_large_image',
                    'site' => config('cpa.seo.twitter_handle', ''),
                ],
            ],
        ]);
    }
}
