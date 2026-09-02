<?php

declare(strict_types=1);

namespace App\Http\Requests\Platform;

use App\Enums\SettingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatformSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'settings'             => ['required', 'array', 'min:1'],
            'settings.*.group'     => ['required', 'string', 'max:60'],
            'settings.*.key'       => ['required', 'string', 'max:100'],
            'settings.*.value'     => ['nullable'],
            'settings.*.type'      => ['required', Rule::enum(SettingType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'settings.required'        => 'No settings were submitted.',
            'settings.*.type.enum'     => 'Invalid setting type. Allowed: string, boolean, integer, float, json.',
        ];
    }
}
