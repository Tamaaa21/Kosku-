<?php

namespace App\Http\Resources;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Kamar
 */
class KamarResource extends JsonResource
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
            'kos_id' => $this->kos_id,
            'nama_kamar' => $this->nama_kamar,
            'harga' => (int) $this->harga,
            'status' => $this->status,
            'ukuran' => $this->ukuran,
            'foto' => $this->foto ?? [],
            'fasilitas_kamar' => $this->fasilitas_kamar ?? [],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'kos' => new KosResource($this->whenLoaded('kos')),
        ];
    }
}
