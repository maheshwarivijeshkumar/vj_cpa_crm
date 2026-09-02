<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateNotificationPreferencesRequest
 *
 * Validates a user's notification channel preferences.
 * Each key maps to a notification category; value is an array of enabled channels.
 */
final class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'preferences'                  => ['required', 'array'],
            'preferences.*.email'          => ['boolean'],
            'preferences.*.in_app'         => ['boolean'],
            'preferences.*.sms'            => ['boolean'],
        ];
    }
}
