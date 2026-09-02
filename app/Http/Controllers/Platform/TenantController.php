<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\DTOs\TenantData;
use App\DTOs\PaginationDTO;
use App\Events\Tenant\TenantCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StoreTenantRequest;
use App\Http\Requests\Platform\UpdateTenantRequest;
use App\Http\Resources\Platform\TenantCollection;
use App\Http\Resources\Platform\TenantResource;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform TenantController — manages accounting firm tenants.
 *
 * Clean controller: all validation delegated to Form Requests.
 * Business logic delegated to TenantRepositoryInterface + Events.
 */
final class TenantController extends Controller
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenants,
    ) {}

    /** Inertia page: tenants listing */
    public function index(Request $request): Response
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'tenants',
            ['id', 'name', 'email', 'plan', 'status', 'created_at'],
        );

        $tenants = $this->tenants->paginate(
            perPage: $pagination->perPage,
            sortBy:  $pagination->sortBy,
            sortDir: $pagination->sortDir,
            filters: array_filter(['search' => $pagination->search]),
            with:    ['country:id,name,iso2'],
        );

        return Inertia::render('Platform/Tenants/Index', [
            'tenants'     => new TenantCollection($tenants),
            'filters'     => $pagination->toFrontend(),
            'perPageOpts' => PaginationDTO::perPageOptions(),
            'stats'       => [
                'total'     => $this->tenants->all(['id'])->count(),
                'active'    => $this->tenants->countByStatus('active'),
                'trial'     => $this->tenants->countByStatus('trial'),
                'suspended' => $this->tenants->countByStatus('suspended'),
            ],
        ]);
    }

    /** Inertia page: single tenant detail */
    public function show(int $id): Response
    {
        $tenant = $this->tenants->findOrFail($id);
        $tenant->loadCount(['users', 'offices']);
        $tenant->load(['country:id,name,iso2', 'currency:id,currency,currency_name,currency_symbol', 'timezone:id,zone_name,gmt_offset_name']);

        return Inertia::render('Platform/Tenants/Show', [
            'tenant' => new TenantResource($tenant),
        ]);
    }

    /** API: create a new tenant + firm owner */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $validated = $request->validated();

        [$tenant, $owner] = DB::transaction(function () use ($validated): array {
            $data = TenantData::fromArray(array_merge($validated, [
                'status' => $validated['plan'] === 'trial' ? 'trial' : 'active',
            ]));

            $tenant = $this->tenants->create(array_merge($data->toModelArray(), [
                'uuid'          => (string) Str::uuid(),
                'slug'          => Str::slug($validated['name']) . '-' . Str::lower(Str::random(4)),
                'trial_ends_at' => $data->plan->value === 'trial'
                    ? now()->addDays(config('cpa.trial_days', 14))
                    : null,
                'is_active' => true,
            ]));

            $owner = \App\Models\User::create([
                'uuid'                 => (string) Str::uuid(),
                'tenant_id'            => $tenant->id,
                'first_name'           => $validated['owner_first_name'],
                'last_name'            => $validated['owner_last_name'],
                'email'                => $validated['owner_email'],
                'password'             => Hash::make($validated['owner_password']),
                'user_type'            => 'firm_owner',
                'status'               => 'active',
                'must_change_password' => true,
                'email_verified_at'    => now(),
            ]);

            return [$tenant, $owner];
        });

        // Fire AFTER commit — SeedTenantRoles + SeedTenantModules listeners run async
        DB::afterCommit(fn () => TenantCreated::dispatch($tenant, $owner));

        return ApiResponse::created(
            new TenantResource($tenant),
            'Tenant created successfully.',
        );
    }

    /** API: update tenant */
    public function update(UpdateTenantRequest $request, int $id): JsonResponse
    {
        $tenant = $this->tenants->update($id, $request->validated());

        return ApiResponse::success(
            new TenantResource($tenant),
            'Tenant updated.',
        );
    }

    /** API: suspend a tenant */
    public function suspend(int $id): JsonResponse
    {
        $this->tenants->update($id, [
            'status'       => 'suspended',
            'suspended_at' => now(),
            'is_active'    => false,
        ]);

        return ApiResponse::success(null, 'Tenant suspended.');
    }

    /** API: reinstate a suspended tenant */
    public function reinstate(int $id): JsonResponse
    {
        $this->tenants->update($id, [
            'status'       => 'active',
            'suspended_at' => null,
            'is_active'    => true,
        ]);

        return ApiResponse::success(null, 'Tenant reinstated.');
    }

    /** API: soft-delete a tenant */
    public function destroy(int $id): JsonResponse
    {
        $this->tenants->delete($id);

        return ApiResponse::noContent('Tenant deleted.');
    }

    /** API: stats for the platform dashboard */
    public function stats(): JsonResponse
    {
        return ApiResponse::success([
            'total'          => $this->tenants->all(['id'])->count(),
            'active'         => $this->tenants->countByStatus('active'),
            'trial'          => $this->tenants->countByStatus('trial'),
            'suspended'      => $this->tenants->countByStatus('suspended'),
            'by_plan'        => collect(['trial', 'starter', 'professional', 'enterprise'])
                ->mapWithKeys(fn ($plan) => [$plan => $this->tenants->countByPlan($plan)]),
            'trial_expiring' => $this->tenants->trialExpiringSoon(7)->count(),
        ]);
    }
}
