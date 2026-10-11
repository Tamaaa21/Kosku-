<?php

namespace App\Models;

use Database\Factories\FasilitasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fasilitas extends Model
{
    /** @use HasFactory<FasilitasFactory> */
    use HasFactory;

    protected $table = 'fasilitas';

    protected $guarded = ['id'];

    public function kos(): BelongsToMany
    {
        return $this->belongsToMany(Kos::class, 'kos_fasilitas', 'fasilitas_id', 'kos_id')
            ->using(KosFasilitas::class);
    }
}
