<?php

namespace App\Http\Requests\Room;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'nama_kamar' => ['required', 'string', 'max:50'],
            'harga' => ['required', 'integer', 'min:0'],
            'harga_harian' => ['nullable', 'integer', 'min:0'],
            'harga_mingguan' => ['nullable', 'integer', 'min:0'],
            'durasi_sewa' => ['nullable', 'array'],
            'durasi_sewa.*' => ['string', 'in:harian,mingguan,bulanan'],
            'status' => ['nullable', 'in:tersedia,terisi'],
            'ukuran' => ['nullable', 'string', 'max:30'],
            'foto' => ['nullable', 'array', 'max:5'],
            'foto.*' => ['string', 'max:500'],
            'fasilitas_kamar' => ['nullable', 'array'],
            'fasilitas_kamar.*' => ['string', 'max:100'],
        ];
    }
}
