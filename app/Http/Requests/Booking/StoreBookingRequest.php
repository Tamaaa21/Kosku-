<?php

namespace App\Http\Requests\Booking;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'pencari';
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('durasi_sewa') && ! $this->has('durasi_jumlah')) {
            $this->merge([
                'durasi_jumlah' => $this->input('durasi_sewa'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxUnits = match ($this->input('durasi_tipe', 'bulanan')) {
            'harian' => 30,
            'mingguan' => 52,
            default => 36,
        };

        return [
            'kamar_id' => ['required', 'integer', 'exists:kamar,id'],
            'tanggal_pemesanan' => ['required', 'date', 'after_or_equal:today'],
            'durasi_tipe' => ['nullable', 'string', 'in:harian,mingguan,bulanan'],
            'durasi_jumlah' => ['nullable', 'integer', 'min:1', "max:{$maxUnits}"],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kamar_id.required' => 'Kamar wajib dipilih.',
            'kamar_id.exists' => 'Kamar yang dipilih tidak valid atau tidak ditemukan.',
            'tanggal_pemesanan.required' => 'Tanggal mulai sewa wajib ditentukan.',
            'tanggal_pemesanan.after_or_equal' => 'Tanggal mulai sewa tidak boleh di masa lampau.',
            'durasi_tipe.in' => 'Tipe durasi sewa harus berupa harian, mingguan, atau bulanan.',
            'durasi_jumlah.min' => 'Durasi sewa minimal 1 satuan waktu.',
            'durasi_jumlah.max' => 'Durasi sewa melebihi batas wajar yang diizinkan sistem.',
        ];
    }
}
