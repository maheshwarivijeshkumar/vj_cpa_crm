<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\DTOs\PaginationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\InviteUserRequest;
use App\Http\Requests\Tenant\UpdateTenantUserRequest;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * TeamController — tenant firm owner manages their own team.
 *
 * Routes: /portal/team  (tenant-scoped, firm_owner / firm_admin only)
 *
 * Covers:
 *   GET    /portal/team          — list team members
 *   POST   /portal/team/invite   — invite a new member (create pending user + send email)
 *   PATCH  /portal/team/{id}     — update role / office / status
 *   DELETE /portal/team/{id}     — deactivate / remove from team (soft-delete)
 *
 * Clean controller: validation in Form Requests, logic stays thin.
 */
final class TeamController extends Controller
{
    /** Inertia page: team member list */
    public function index(Request $request): Response
    {
        $tenantId = $request->user()->tenant_id;

        $pagination = PaginationDTO::fromRequest($request, 'users', ['id', 'first_name', 'last_name', 'email', 'status']);

        $users = User::query()
            ->where('tenant_id', $tenantId)
            ->with(['roles:id,name,slug', 'office:id,name'])
            ->when($pagination->search, fn ($q) => $q->where(
                fn ($q) => $q->where('email', 'like', "%{$pagination->search}%")
                             ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$pagination->search}%"])
            ))
            ->when($request->string('role')->toString(), fn ($q, $role) =>
                $q->whereHas('roles', fn ($r) => $r->where('slug', $role))
            )
            ->orderBy($pagination->sortBy, $pagination->sortDir)
            ->paginate($pagination->perPage)
            ->withQueryString();

        $roles = Role::query()
            ->whereIn('scope', ['tenant', 'both'])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $offices = Office::query()
            ->where('tenant_id', $tenantId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Tenant/Team', [
            'users'      => $users,
            'roles'      => $roles,
            'offices'    => $offices,
            'filters'    => array_merge($pagination->toFrontend(), [
                'role' => $request->string('role')->toString(),
            ]),
            'perPageOpts'=> PaginationDTO::perPageOptions(),
        ]);
    }

    /** POST /portal/team/invite — create user record + send invitation email */
    public function invite(InviteUserRequest $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $data     = $request->validated();

        /** @var \App\Models\Role|null $role */
        $role = Role::where('slug', $data['role'])->first();

        // Create user in 'invited' status with a temporary random password.
        // The invitation email will let them set their own password.
        $user = User::create([
            'uuid'                  => (string) Str::uuid(),
            'tenant_id'             => $tenantId,
            'office_id'             => $data['office_id'] ?? null,
            'first_name'            => $data['first_name'],
            'last_name'             => $data['last_name'],
            'email'                 => $data['email'],
            'password'              => Hash::make(Str::random(32)),
            'user_type'             => 'firm_user',
            'status'                => 'invited',
            'must_change_password'  => true,
            'invited_at'            => now(),
        ]);

        // Assign role
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        // Send invitation via Fortify-compatible password-reset flow
        // (same mechanism — user clicks link, sets their own password, then verifies email)
        $user->sendEmailVerificationNotification();

        AuditService::log('team.user_invited', $user, [
            'invited_by' => $request->user()->id,
            'role'       => $data['role'],
        ]);

        return ApiResponse::created([
            'id'         => $user->id,
            'email'      => $user->email,
            'full_name'  => $user->full_name,
            'status'     => $user->status,
        ], 'Invitation sent to ' . $user->email . '.');
    }

    /** PATCH /portal/team/{id} — update role / office / status */
    public function update(UpdateTenantUserRequest $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        /** @var User $user */
        $user = User::where('tenant_id', $tenantId)
            ->whereNot('id', $request->user()->id) // cannot demote yourself
            ->findOrFail($id);

        $data = $request->validated();

        $user->update([
            'office_id' => $data['office_id'] ?? $user->office_id,
            'status'    => $data['status'],
        ]);

        // Sync role
        $role = Role::where('slug', $data['role'])->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        AuditService::log('team.user_updated', $user, ['changes' => $data]);

        return ApiResponse::success([
            'id'     => $user->id,
            'status' => $user->status,
            'role'   => $data['role'],
        ], 'Team member updated.');
    }

    /** DELETE /portal/team/{id} — deactivate (soft-delete) a team member */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        /** @var User $user */
        $user = User::where('tenant_id', $tenantId)
            ->whereNot('id', $request->user()->id) // cannot remove yourself
            ->findOrFail($id);

        AuditService::log('team.user_removed', $user, ['removed_by' => $request->user()->id]);

        $user->delete();

        return ApiResponse::noContent('Team member removed.');
    }

    /** POST /portal/team/{id}/resend-invite — resend invitation email */
    public function resendInvite(Request $request, int $id): JsonResponse
    {
        $user = User::where('tenant_id', $request->user()->tenant_id)
            ->where('status', 'invited')
            ->findOrFail($id);

        $user->sendEmailVerificationNotification();

        return ApiResponse::success(null, 'Invitation resent to ' . $user->email . '.');
    }
}
