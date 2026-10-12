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
            'harga_harian' => ['nullable', 'integer', 'min:0'],
            'harga_mingguan' => ['nullable', 'integer', 'min:0'],
            'durasi_sewa' => ['nullable', 'array'],
            'durasi_sewa.*' => ['string', 'in:harian,mingguan,bulanan'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'fasilitas_ids' => ['nullable', 'array'],
            'fasilitas_ids.*' => ['integer', 'exists:fasilitas,id'],
            'peraturan' => ['nullable', 'array'],
            'peraturan.*' => ['string', 'max:255'],
            'foto' => ['nullable', 'array', 'max:5'],
            'foto.*' => ['string', 'max:500'],
        ];
    }
}
