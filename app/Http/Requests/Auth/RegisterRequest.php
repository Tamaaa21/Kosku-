<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'in:pencari,pemilik'],
        ];
    }
}
