<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateProfileRequest
 *
 * Validates a tenant user's own profile update.
 * Email uniqueness excludes the current user.
 */
final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => [
                'required', 'email:rfc,dns', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'phone'      => ['nullable', 'string', 'max:30'],
            'timezone'   => ['nullable', 'string', 'max:64'],
            'locale'     => ['nullable', 'string', 'size:5'],
            'avatar'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'email.required'      => 'Email address is required.',
            'email.email'         => 'Please enter a valid email address.',
            'email.unique'        => 'This email is already in use by another account.',
            'avatar.image'        => 'Avatar must be an image file.',
            'avatar.max'          => 'Avatar must be under 2 MB.',
        ];
    }
}
