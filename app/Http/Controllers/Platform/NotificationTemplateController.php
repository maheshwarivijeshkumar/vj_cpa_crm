<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\DTOs\PaginationDTO;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Services\Audit\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform NotificationTemplateController
 *
 * Manages email/in-app/SMS notification templates.
 * Provides CRUD for platform admins + preview rendering.
 *
 * Routes: /platform/notifications
 */
final class NotificationTemplateController extends Controller
{
    /** Inertia page: templates listing */
    public function index(Request $request): Response
    {
        $pagination = PaginationDTO::fromRequest(
            $request,
            'notification_templates',
            ['id', 'key', 'name', 'channel', 'category'],
        );

        $query = NotificationTemplate::query()
            ->orderBy($pagination->sortBy, $pagination->sortDir);

        if ($search = $request->string('search')->toString()) {
            $query->where(fn ($q) => $q
                ->where('key', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
            );
        }

        if ($channel = $request->string('channel')->toString()) {
            $query->where('channel', $channel);
        }

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        $templates = $query->paginate($pagination->perPage)->withQueryString();

        return Inertia::render('Platform/Notifications/Index', [
            'templates'        => $templates,
            'filters'          => array_merge($pagination->toFrontend(), [
                'channel'  => $request->string('channel')->toString(),
                'category' => $request->string('category')->toString(),
            ]),
            'perPageOpts'      => PaginationDTO::perPageOptions(),
            'channelOptions'   => ['email', 'in_app', 'sms'],
            'categoryOptions'  => NotificationTemplate::query()
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }

    /** Inertia: edit a single template */
    public function edit(int $id): Response
    {
        $template = NotificationTemplate::findOrFail($id);

        return Inertia::render('Platform/Notifications/Edit', [
            'template' => $template,
        ]);
    }

    /** API: update a template */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name'                => ['sometimes', 'required', 'string', 'max:150'],
            'subject'             => ['nullable', 'string', 'max:255'],
            'body_html'           => ['nullable', 'string'],
            'body_text'           => ['nullable', 'string'],
            'body_short'          => ['nullable', 'string', 'max:500'],
            'description'         => ['nullable', 'string', 'max:500'],
            'is_active'           => ['sometimes', 'boolean'],
        ]);

        /** @var NotificationTemplate $template */
        $template = NotificationTemplate::findOrFail($id);
        $template->update($validated);

        AuditService::log('notification_template.updated', $template, ['key' => $template->key]);

        return ApiResponse::success($template, 'Template updated.');
    }

    /** API: preview a template with sample variables */
    public function preview(Request $request, int $id): JsonResponse
    {
        $template = NotificationTemplate::findOrFail($id);

        // Replace {{variable}} placeholders with sample values
        $sampleVars = [
            'tenant.name'   => 'Acme Accounting',
            'user.first_name' => 'John',
            'discount_code' => 'SAMPLE20',
            'discount_value'=> '20%',
            'valid_until'   => now()->addDays(30)->format('M j, Y'),
            'plan'          => 'Professional',
            'ends_at'       => now()->addMonth()->format('M j, Y'),
            'reward_amount' => '50',
            'reward_type'   => 'Points',
            'portal_url'    => url('/portal'),
            'pricing_url'   => url('/pricing'),
        ];

        $rendered = $template->body_html ?? $template->body_short ?? '';

        foreach ($sampleVars as $key => $value) {
            $rendered = str_replace('{{' . $key . '}}', $value, $rendered);
        }

        return ApiResponse::success([
            'subject'  => $template->subject,
            'rendered' => $rendered,
            'channel'  => $template->channel,
        ]);
    }
}
