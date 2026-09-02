<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'key', 'name', 'channel', 'category',
        'subject', 'body_html', 'body_text', 'body_short',
        'available_variables', 'description', 'status', 'version',
        'is_system', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'available_variables' => 'array',
            'is_system'           => 'boolean',
            'is_active'           => 'boolean',
            'version'             => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** Resolve the body for a given channel */
    public function resolveBody(string $channel = 'email'): ?string
    {
        return match ($channel) {
            'email'  => $this->body_html ?? $this->body_text,
            'sms'    => $this->body_short ?? $this->body_text,
            'in_app' => $this->body_short ?? $this->body_text,
            default  => $this->body_text,
        };
    }

    /** Replace {{variable}} placeholders with actual values */
    public function render(array $variables = [], string $channel = 'email'): array
    {
        $body    = $this->resolveBody($channel) ?? '';
        $subject = $this->subject ?? '';

        foreach ($variables as $key => $value) {
            $body    = str_replace("{{{{{$key}}}}}", (string) $value, $body);
            $subject = str_replace("{{{{{$key}}}}}", (string) $value, $subject);
        }

        return ['subject' => $subject, 'body' => $body];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'published');
    }

    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeForTenantOrPlatform($query, ?int $tenantId)
    {
        return $query->where(function ($q) use ($tenantId): void {
            $q->whereNull('tenant_id')
              ->when($tenantId, fn ($q2) => $q2->orWhere('tenant_id', $tenantId));
        });
    }
}
