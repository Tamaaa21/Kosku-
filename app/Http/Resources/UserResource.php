<?php

namespace App\Http\Resources;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Pengguna
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'email' => $this->email,
            'telepon' => $this->telepon,
            'no_hp' => $this->telepon,
            'foto' => $this->foto,
            'role' => $this->role,
            'aktif' => (bool) $this->aktif,
            'nik' => $this->nik,
            'foto_ktp' => $this->foto_ktp,
            'bukti_properti' => $this->bukti_properti,
            'foto_properti' => $this->foto_properti,
            'catatan_verifikasi' => $this->catatan_verifikasi,
            'status_verifikasi' => $this->status_verifikasi ?? 'unverified',
            'alasan_penolakan' => $this->alasan_penolakan,
            'verified_at' => $this->verified_at?->toISOString(),
            'kos_count' => $this->whenCounted('kos'),
            'pemesanan_count' => $this->whenCounted('pemesanan'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
