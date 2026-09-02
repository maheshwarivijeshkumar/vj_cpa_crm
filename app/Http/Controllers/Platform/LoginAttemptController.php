<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\DTOs\PaginationDTO;
use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform LoginAttemptController
 *
 * Read-only security log of all authentication attempts.
 * Useful for detecting brute-force attacks and compromised accounts.
 *
 * Routes: /platform/login-attempts
 */
final class LoginAttemptController extends Controller
{
    /** Inertia page: login attempts listing */
    public function index(Request $request): Response
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'login_attempts',
            ['id', 'email', 'ip_address', 'was_successful', 'created_at'],
        );

        $query = LoginAttempt::query()
            ->orderBy($pagination->sortBy, $pagination->sortDir);

        if ($search = $request->string('search')->toString()) {
            $query->where(fn ($q) => $q
                ->where('email', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%")
            );
        }

        if ($request->boolean('failed_only')) {
            $query->where('was_successful', false);
        }

        if ($date = $request->string('date')->toString()) {
            $query->whereDate('created_at', $date);
        }

        $attempts = $query->paginate($pagination->perPage)->withQueryString();

        // Summary stats for the header
        $stats = [
            'total_today'  => LoginAttempt::whereDate('created_at', today())->count(),
            'failed_today' => LoginAttempt::whereDate('created_at', today())->where('was_successful', false)->count(),
            'unique_ips'   => LoginAttempt::whereDate('created_at', today())->distinct('ip_address')->count('ip_address'),
        ];

        return Inertia::render('Platform/LoginAttempts/Index', [
            'attempts'    => $attempts,
            'filters'     => array_merge($pagination->toFrontend(), [
                'failed_only' => $request->boolean('failed_only'),
                'date'        => $request->string('date')->toString(),
            ]),
            'perPageOpts' => PaginationDTO::perPageOptions(),
            'stats'       => $stats,
        ]);
    }
}
