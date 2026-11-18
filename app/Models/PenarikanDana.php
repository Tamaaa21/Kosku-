<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PenarikanDana extends Model
{
    use HasFactory;

    protected $table = 'penarikan_dana';

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'diproses_pada' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function rekeningBank(): BelongsTo
    {
        return $this->belongsTo(RekeningBankMitra::class, 'rekening_bank_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'disetujui_oleh');
    }

    public function mutasiSaldo(): HasOne
    {
        return $this->hasOne(MutasiSaldoMitra::class, 'penarikan_dana_id');
    }
}
