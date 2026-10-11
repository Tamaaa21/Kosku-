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
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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
}
