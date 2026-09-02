<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModule extends Model
{
    protected $table = 'tenant_modules';

    protected $fillable = ['tenant_id', 'module_id', 'is_enabled', 'settings'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'settings'   => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
