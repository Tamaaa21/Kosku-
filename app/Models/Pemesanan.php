<?php

namespace App\Models;

use Database\Factories\PemesananFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pemesanan extends Model
{
    /** @use HasFactory<PemesananFactory> */
    use HasFactory;

    protected $table = 'pemesanan';

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pemesanan' => 'date',
            'tanggal_selesai' => 'date',
            'durasi_jumlah' => 'integer',
            'total_harga' => 'integer',
            'persentase_dp' => 'integer',
            'nominal_dp' => 'integer',
            'sisa_pelunasan' => 'integer',
            'komisi_persen' => 'integer',
            'komisi_nominal' => 'integer',
            'total_hak_mitra' => 'integer',
            'kunci_diserahkan_at' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'kos_id');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class, 'pemesanan_id');
    }

    public function transaksiPembayaran(): HasMany
    {
        return $this->hasMany(TransaksiPembayaran::class, 'pemesanan_id');
    }

    public function transaksiDp(): HasOne
    {
        return $this->hasOne(TransaksiPembayaran::class, 'pemesanan_id')
            ->where('tipe', 'dp')
            ->latestOfMany();
    }

    public function transaksiPelunasan(): HasOne
    {
        return $this->hasOne(TransaksiPembayaran::class, 'pemesanan_id')
            ->where('tipe', 'pelunasan')
            ->latestOfMany();
    }

    public function mutasiSaldo(): HasOne
    {
        return $this->hasOne(MutasiSaldoMitra::class, 'pemesanan_id');
    }
}
