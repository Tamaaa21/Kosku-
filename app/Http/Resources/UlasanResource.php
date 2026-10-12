<?php

namespace App\Http\Resources;

use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Ulasan
 */
class UlasanResource extends JsonResource
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
            'pemesanan_id' => $this->pemesanan_id,
            'rating' => (int) $this->rating,
            'komentar' => (string) $this->komentar,
            'created_at' => $this->created_at?->toISOString(),
            'formatted_date' => $this->created_at?->translatedFormat('d M Y') ?? '',
            'pengguna' => new PenggunaResource($this->whenLoaded('pengguna')),
        ];
    }
}
