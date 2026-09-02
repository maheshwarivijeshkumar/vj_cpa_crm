<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeSetting extends Model
{
    protected $table = 'office_settings';

    protected $fillable = ['office_id', 'group', 'key', 'value', 'type'];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function typedValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json'    => json_decode((string) $this->value, true),
            default   => $this->value,
        };
    }
}
