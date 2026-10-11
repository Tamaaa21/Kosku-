<?php

namespace App\Http\Resources;

use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Kos
 */
class KosResource extends JsonResource
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
            'pemilik_id' => $this->pemilik_id,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'deskripsi' => $this->deskripsi,
            'jenis' => $this->jenis,
            'harga' => (int) $this->harga,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'pemilik' => new PenggunaResource($this->whenLoaded('pemilik')),
            'fasilitas' => FasilitasResource::collection($this->whenLoaded('fasilitas')),
            'kamar' => KamarResource::collection($this->whenLoaded('kamar')),
            'kamar_tersedia_count' => $this->when(
                isset($this->kamar_tersedia_count) || $this->relationLoaded('kamar'),
                fn () => isset($this->kamar_tersedia_count)
                    ? (int) $this->kamar_tersedia_count
                    : $this->kamar->where('status', 'tersedia')->count()
            ),
        ];
    }
}
