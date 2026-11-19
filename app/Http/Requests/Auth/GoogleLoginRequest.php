<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_token' => ['nullable', 'string'],
            'credential' => ['nullable', 'string'],
            'role' => ['nullable', 'string', 'in:pencari,pemilik'],
            'demo_user' => ['nullable', 'array'],
        ];
    }

    /**
     * Get the token from either id_token or credential input.
     */
    public function getToken(): ?string
    {
        return $this->input('id_token') ?? $this->input('credential');
    }
}
