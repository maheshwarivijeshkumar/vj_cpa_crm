<?php

declare(strict_types=1);

namespace App\Http\Requests\Platform;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Rules\UniqueEmailExcept;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePlatformUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPlatformAdmin() ?? false;
    }

    public function rules(): array
    {
        // Resolve the target user id from the route
        $userId = (int) $this->route('id');

        return [
            'first_name'           => ['sometimes', 'string', 'min:1', 'max:80'],
            'last_name'            => ['sometimes', 'string', 'min:1', 'max:80'],
            'email'                => ['sometimes', 'string', 'email:rfc', 'max:255', new UniqueEmailExcept($userId)],
            'user_type'            => ['sometimes', Rule::enum(UserType::class)],
            'status'               => ['sometimes', Rule::enum(UserStatus::class)],
            'password'             => ['sometimes', 'nullable', 'string', Password::defaults()],
            'must_change_password' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
    }
}
