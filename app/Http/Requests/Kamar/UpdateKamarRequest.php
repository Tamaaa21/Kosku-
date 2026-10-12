<?php

namespace App\Http\Requests\Kamar;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKamarRequest extends FormRequest
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
            'nama_kamar' => ['sometimes', 'required', 'string', 'max:50'],
            'harga' => ['sometimes', 'required', 'integer', 'min:0'],
            'status' => ['sometimes', 'required', 'in:tersedia,terisi'],
            'ukuran' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'array', 'max:5'],
            'foto.*' => ['string', 'max:500'],
            'fasilitas_kamar' => ['nullable', 'array'],
            'fasilitas_kamar.*' => ['string', 'max:100'],
        ];
    }
}
