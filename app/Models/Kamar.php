<?php

namespace App\Models;

use Database\Factories\KamarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    /** @use HasFactory<KamarFactory> */
    use HasFactory;

    protected $table = 'kamar';

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
            'foto' => 'array',
            'fasilitas_kamar' => 'array',
        ];
    }

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'kos_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'kamar_id');
    }
}
