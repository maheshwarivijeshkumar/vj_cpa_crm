<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * UpdatePasswordRequest
 *
 * Validates a user's request to change their own password.
 * Requires current password to prevent hijacking.
 */
final class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'current_password'      => ['required', 'string', 'current_password'],
            'password'              => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'current_password.required'      => 'Please enter your current password.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required'              => 'Please enter a new password.',
            'password.confirmed'             => 'Password confirmation does not match.',
        ];
    }
}
