<?php

namespace App\Http\Resources;

use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Pemesanan
 */
class BookingResource extends JsonResource
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
            'persentase_dp' => (int) ($this->persentase_dp ?? 20),
            'nominal_dp' => (int) ($this->nominal_dp ?? 0),
            'sisa_pelunasan' => (int) ($this->sisa_pelunasan ?? 0),
            'komisi_persen' => (int) ($this->komisi_persen ?? 5),
            'komisi_nominal' => (int) ($this->komisi_nominal ?? 0),
            'total_hak_mitra' => (int) ($this->total_hak_mitra ?? 0),
            'metode_transaksi' => $this->metode_transaksi ?? 'manual',
            'status_pembayaran' => $this->status_pembayaran ?? 'belum_dibayar',
            'kunci_diserahkan_at' => $this->kunci_diserahkan_at?->toISOString(),
            'catatan_serah_terima' => $this->catatan_serah_terima,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'pengguna' => new UserResource($this->whenLoaded('pengguna')),
            'kos' => new KosResource($this->whenLoaded('kos')),
            'kamar' => new RoomResource($this->whenLoaded('kamar')),
            'ulasan' => new ReviewResource($this->whenLoaded('ulasan')),
            'transaksi_dp' => $this->whenLoaded('transaksiDp'),
            'transaksi_pelunasan' => $this->whenLoaded('transaksiPelunasan'),
        ];
    }
}
