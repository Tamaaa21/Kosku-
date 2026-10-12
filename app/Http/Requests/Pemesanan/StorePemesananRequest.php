<?php

namespace App\Http\Requests\Pemesanan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePemesananRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'pencari';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kamar_id' => ['required', 'integer', 'exists:kamar,id'],
            'tanggal_pemesanan' => ['required', 'date', 'after_or_equal:today'],
            'durasi_tipe' => ['nullable', 'string', 'in:harian,mingguan,bulanan'],
            'durasi_jumlah' => ['nullable', 'integer', 'min:1', 'max:365'],
        ];
    }
}
