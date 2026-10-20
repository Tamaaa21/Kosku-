<?php

namespace App\Http\Requests\Facility;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fasilitas = $this->route('facility') ?? $this->route('fasilitas') ?? $this->route('id');
        $fasilitasId = is_object($fasilitas) ? $fasilitas->id : $fasilitas;

        return [
            'nama' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('fasilitas', 'nama')->ignore($fasilitasId)],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
