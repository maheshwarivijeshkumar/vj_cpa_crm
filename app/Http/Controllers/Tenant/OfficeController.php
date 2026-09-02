<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\OfficeSetting;
use App\Services\Audit\AuditService;
use App\Services\Settings\SettingsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * OfficeController — tenant firm manages their own offices and per-office settings.
 *
 * Routes: /portal/offices  (firm_owner / firm_admin only)
 *
 * Covers:
 *   GET    /portal/offices                  — list offices + settings
 *   POST   /portal/offices                  — create a new office
 *   GET    /portal/offices/{id}/settings    — show per-office settings
 *   PATCH  /portal/offices/{id}             — update office details
 *   PATCH  /portal/offices/{id}/settings    — save per-office setting overrides
 *   DELETE /portal/offices/{id}             — deactivate (soft-delete)
 */
final class OfficeController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
    ) {}

    /** Inertia page: offices list */
    public function index(Request $request): Response
    {
        $tenantId = $request->user()->tenant_id;

        $offices = Office::query()
            ->where('tenant_id', $tenantId)
            ->with(['country:id,name', 'timezone:id,zone_name'])
            ->withCount('users')
            ->orderByDesc('is_headquarters')
            ->orderBy('name')
            ->get();

        return Inertia::render('Tenant/Offices', [
            'offices' => $offices->map(fn (Office $o) => [
                'id'              => $o->id,
                'name'            => $o->name,
                'code'            => $o->code,
                'email'           => $o->email,
                'phone'           => $o->phone,
                'address'         => implode(', ', array_filter([
                    $o->address_line1, $o->city, $o->state, $o->postal_code,
                    $o->country?->name,
                ])),
                'is_headquarters' => $o->is_headquarters,
                'is_active'       => $o->is_active,
                'users_count'     => $o->users_count,
            ]),
        ]);
    }

    /** Inertia page: per-office settings overrides */
    public function settings(Request $request, int $id): Response
    {
        $tenantId = $request->user()->tenant_id;

        /** @var Office $office */
        $office = Office::where('tenant_id', $tenantId)->findOrFail($id);

        // Load existing office-level setting overrides
        $overrides = OfficeSetting::where('office_id', $id)
            ->pluck('value', 'key')
            ->toArray();

        // Merge with platform + tenant defaults so the form shows current effective values
        $defaults = [
            'fiscal_year_start_month' => SettingsService::get('fiscal_year_start_month', 1, tenantId: $tenantId),
            'invoice_prefix'          => SettingsService::get('invoice.prefix', 'INV', tenantId: $tenantId),
            'invoice_due_days'        => SettingsService::get('invoice.due_days', 30, tenantId: $tenantId),
            'currency'                => SettingsService::get('default_currency', 'CAD', tenantId: $tenantId),
            'date_format'             => SettingsService::get('date_format', 'Y-m-d', tenantId: $tenantId),
            'timezone'                => SettingsService::get('timezone', 'America/Toronto', tenantId: $tenantId),
        ];

        return Inertia::render('Tenant/OfficeSettings', [
            'office'    => $office,
            'settings'  => array_merge($defaults, $overrides),
        ]);
    }

    /** API: create a new office */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'code'         => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address_line1'=> ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'postal_code'  => ['nullable', 'string', 'max:20'],
            'country_id'   => ['nullable', 'integer', 'exists:countries,id'],
            'timezone_id'  => ['nullable', 'integer', 'exists:timezones,id'],
        ]);

        $office = Office::create(array_merge($validated, [
            'tenant_id'       => $request->user()->tenant_id,
            'is_headquarters' => false,
            'is_active'       => true,
        ]));

        AuditService::log('office.created', $office, ['name' => $office->name]);

        return ApiResponse::created(['id' => $office->id, 'name' => $office->name], 'Office created.');
    }

    /** API: update office details */
    public function update(Request $request, int $id): JsonResponse
    {
        $office = Office::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        $validated = $request->validate([
            'name'         => ['sometimes', 'required', 'string', 'max:150'],
            'code'         => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address_line1'=> ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'postal_code'  => ['nullable', 'string', 'max:20'],
            'country_id'   => ['nullable', 'integer', 'exists:countries,id'],
            'timezone_id'  => ['nullable', 'integer', 'exists:timezones,id'],
            'is_active'    => ['sometimes', 'boolean'],
        ]);

        $office->update($validated);

        AuditService::log('office.updated', $office, ['fields' => array_keys($validated)]);

        return ApiResponse::success(['id' => $office->id], 'Office updated.');
    }

    /** API: save per-office settings overrides */
    public function saveSettings(Request $request, int $id): JsonResponse
    {
        $office = Office::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);

        $validated = $request->validate([
            'settings'              => ['required', 'array'],
            'settings.invoice_prefix'   => ['nullable', 'string', 'max:20'],
            'settings.invoice_due_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'settings.date_format'      => ['nullable', 'string', 'max:20'],
            'settings.timezone'         => ['nullable', 'string', 'max:64'],
            'settings.currency'         => ['nullable', 'string', 'size:3'],
        ]);

        foreach ($validated['settings'] as $key => $value) {
            SettingsService::setForOffice($office->id, $key, $value, 'string');
        }

        AuditService::log('office.settings_updated', $office, [
            'keys' => array_keys($validated['settings']),
        ]);

        return ApiResponse::success(null, 'Office settings saved.');
    }

    /** API: soft-delete an office */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $office = Office::where('tenant_id', $request->user()->tenant_id)
            ->where('is_headquarters', false) // Cannot delete HQ
            ->findOrFail($id);

        AuditService::log('office.deleted', $office, ['name' => $office->name]);

        $office->delete();

        return ApiResponse::noContent('Office removed.');
    }
}
