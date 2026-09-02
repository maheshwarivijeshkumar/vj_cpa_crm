<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\DTOs\PaginationDTO;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform AuditLogController
 *
 * Read-only viewer for all platform and tenant audit events.
 * Audit logs are immutable — no create/update/delete operations here.
 *
 * Routes: /platform/audit-logs
 */
final class AuditLogController extends Controller
{
    /** Inertia page: audit log listing with filters */
    public function index(Request $request): Response
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'created_at',
            ['id', 'event', 'auditable_type', 'causer_id', 'created_at'],
        );

        $query = AuditLog::query()
            ->with(['causer:id,first_name,last_name,email'])
            ->orderBy($pagination->sortBy, $pagination->sortDir);

        // Filters
        if ($search = $request->string('search')->toString()) {
            $query->where(fn ($q) => $q
                ->where('event', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%")
                ->orWhereHas('causer', fn ($q) => $q->where('email', 'like', "%{$search}%"))
            );
        }

        if ($event = $request->string('event')->toString()) {
            $query->where('event', $event);
        }

        if ($tenantId = $request->integer('tenant_id')) {
            $query->where('tenant_id', $tenantId);
        }

        if ($date = $request->string('date')->toString()) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->paginate($pagination->perPage)->withQueryString();

        return Inertia::render('Platform/AuditLogs/Index', [
            'logs'        => $logs,
            'filters'     => array_merge($pagination->toFrontend(), [
                'event'     => $request->string('event')->toString(),
                'tenant_id' => $request->integer('tenant_id') ?: null,
                'date'      => $request->string('date')->toString(),
            ]),
            'perPageOpts' => PaginationDTO::perPageOptions(),
            // Distinct events for filter dropdown
            'eventOptions' => AuditLog::query()
                ->select('event')
                ->distinct()
                ->orderBy('event')
                ->pluck('event')
                ->take(100),
        ]);
    }
}
