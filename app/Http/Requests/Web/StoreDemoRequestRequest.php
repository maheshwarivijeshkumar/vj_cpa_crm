<?php

declare(strict_types=1);

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemoRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public form
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:120'],
            'email'   => ['required', 'string', 'email:rfc', 'max:255'],
            'company' => ['required', 'string', 'min:2', 'max:150'],
            'size'    => ['nullable', 'string', 'in:1,2-5,6-15,16-30,31+'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'    => 'Please enter your full name.',
            'company.min' => 'Please enter your firm name.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
    }
}
