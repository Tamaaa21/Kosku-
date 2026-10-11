<?php

namespace App\Http\Requests\Kos;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreKosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'pemilik';
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
            'alamat' => ['required', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'jenis' => ['required', 'in:putra,putri,campur'],
            'harga' => ['required', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'fasilitas_ids' => ['nullable', 'array'],
            'fasilitas_ids.*' => ['integer', 'exists:fasilitas,id'],
        ];
    }
}
