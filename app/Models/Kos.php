<?php

namespace App\Models;

use Database\Factories\KosFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kos extends Model
{
    /** @use HasFactory<KosFactory> */
    use HasFactory;

    protected $table = 'kos';

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'harga_harian' => 'integer',
            'harga_mingguan' => 'integer',
            'durasi_sewa' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'peraturan' => 'array',
            'foto' => 'array',
        ];
    }

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pemilik_id');
    }

    public function kamar(): HasMany
    {
        return $this->hasMany(Kamar::class, 'kos_id');
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'kos_fasilitas', 'kos_id', 'fasilitas_id')
            ->using(KosFasilitas::class);
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'kos_id');
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class, 'kos_id');
    }

    public function getRatingRataRataAttribute(): ?float
    {
        $avg = $this->ulasan()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getUlasanCountAttribute(): int
    {
        return (int) $this->ulasan()->count();
    }

    /**
     * Synchronize minimum starting prices and available durations from associated rooms.
     */
    public function syncPricesAndDurationsFromRooms(): void
    {
        $rooms = $this->kamar()->get();

        if ($rooms->isNotEmpty()) {
            $minHarga = $rooms->min('harga');
            $minHarian = $rooms->whereNotNull('harga_harian')->min('harga_harian');
            $minMingguan = $rooms->whereNotNull('harga_mingguan')->min('harga_mingguan');

            $allDurations = [];
            foreach ($rooms as $room) {
                if (is_array($room->durasi_sewa)) {
                    $allDurations = array_merge($allDurations, $room->durasi_sewa);
                }
            }
            $durations = ! empty($allDurations) ? array_values(array_unique($allDurations)) : ['bulanan'];

            $this->update([
                'harga' => $minHarga ?? $this->harga,
                'harga_harian' => $minHarian,
                'harga_mingguan' => $minMingguan,
                'durasi_sewa' => $durations,
            ]);
        }
    }
}
