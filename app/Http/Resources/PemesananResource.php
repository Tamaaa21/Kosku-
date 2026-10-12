<?php

namespace App\Http\Resources;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Pemesanan
 */
class PemesananResource extends JsonResource
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
            'pengguna_id' => $this->pengguna_id,
            'kos_id' => $this->kos_id,
            'kamar_id' => $this->kamar_id,
            'tanggal_pemesanan' => $this->tanggal_pemesanan?->format('Y-m-d'),
            'durasi_tipe' => $this->durasi_tipe ?? 'bulanan',
            'durasi_jumlah' => (int) ($this->durasi_jumlah ?? 1),
            'tanggal_selesai' => $this->tanggal_selesai?->format('Y-m-d'),
            'total_harga' => $this->total_harga !== null ? (int) $this->total_harga : null,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'pengguna' => new PenggunaResource($this->whenLoaded('pengguna')),
            'kos' => new KosResource($this->whenLoaded('kos')),
            'kamar' => new KamarResource($this->whenLoaded('kamar')),
            'ulasan' => new UlasanResource($this->whenLoaded('ulasan')),
        ];
    }
}
