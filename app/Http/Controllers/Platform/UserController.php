<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\DTOs\PaginationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\UpdatePlatformUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform UserController — manages users from the platform admin perspective.
 *
 * Clean controller: validation delegated to UpdatePlatformUserRequest.
 * Reads delegated to UserRepositoryInterface.
 */
final class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    /** Inertia page: users listing with filters */
    public function index(Request $request): Response
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'users',
            ['id', 'first_name', 'last_name', 'email', 'user_type', 'status', 'created_at'],
        );

        $users = $this->users->paginate(
            perPage: $pagination->perPage,
            sortBy:  $pagination->sortBy,
            sortDir: $pagination->sortDir,
            filters: array_filter([
                'search'    => $pagination->search ?: null,
                'user_type' => $request->string('user_type', '')->toString() ?: null,
                'status'    => $request->string('status', '')->toString()    ?: null,
                'tenant_id' => $request->integer('tenant_id') ?: null,
            ]),
            with: ['tenant:id,name,slug', 'roles:id,slug,name'],
        );

        return Inertia::render('Platform/Users/Index', [
            'users'       => new UserCollection($users),
            'filters'     => array_merge($pagination->toFrontend(), [
                'user_type' => $request->string('user_type')->toString(),
                'status'    => $request->string('status')->toString(),
                'tenant_id' => $request->integer('tenant_id') ?: null,
            ]),
            'perPageOpts' => PaginationDTO::perPageOptions(),
        ]);
    }

    /** API: single user detail */
    public function show(int $id): JsonResponse
    {
        $user = $this->users->findOrFail($id);
        $user->load(['tenant:id,name,slug', 'roles:id,name,slug', 'office:id,name']);

        return ApiResponse::success(new UserResource($user));
    }

    /** API: update a user — all validation in UpdatePlatformUserRequest */
    public function update(UpdatePlatformUserRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        // Hash password if provided (never pass plaintext to repository)
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->users->update($id, $data);

        return ApiResponse::success(new UserResource($user), 'User updated.');
    }

    /** API: force logout all sessions for a user */
    public function forceLogout(int $id): JsonResponse
    {
        $this->users->findOrFail($id); // Ensure user exists

        DB::table('sessions')
            ->where('user_id', $id)
            ->delete();

        return ApiResponse::success(null, 'All sessions terminated.');
    }

    /** API: soft-delete a user */
    public function destroy(int $id): JsonResponse
    {
        $this->users->delete($id);

        return ApiResponse::noContent('User deleted.');
    }

    /** API: restore a soft-deleted user */
    public function restore(int $id): JsonResponse
    {
        $this->users->restore($id);

        return ApiResponse::success(null, 'User restored.');
    }
}
