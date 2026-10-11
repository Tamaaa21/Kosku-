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
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'pengguna' => new PenggunaResource($this->whenLoaded('pengguna')),
            'kos' => new KosResource($this->whenLoaded('kos')),
            'kamar' => new KamarResource($this->whenLoaded('kamar')),
        ];
    }
}
