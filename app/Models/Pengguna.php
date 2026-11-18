<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function isVerifiedOwner(): bool
    {
        return $this->role === 'pemilik' && $this->status_verifikasi === 'verified';
    }

    public function kos(): HasMany
    {
        return $this->hasMany(Kos::class, 'pemilik_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'pengguna_id');
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class, 'pengguna_id');
    }

    public function rekeningBank(): HasMany
    {
        return $this->hasMany(RekeningBankMitra::class, 'pengguna_id');
    }

    public function rekeningBankUtama(): HasOne
    {
        return $this->hasOne(RekeningBankMitra::class, 'pengguna_id')
            ->where('is_utama', true);
    }

    public function penarikanDana(): HasMany
    {
        return $this->hasMany(PenarikanDana::class, 'pengguna_id');
    }

    public function mutasiSaldo(): HasMany
    {
        return $this->hasMany(MutasiSaldoMitra::class, 'pengguna_id');
    }

    /**
     * Get available balance for owner.
     */
    public function saldoTersedia(): int
    {
        $latest = $this->mutasiSaldo()->latest('id')->first();

        return $latest ? (int) $latest->saldo_akhir : 0;
    }
}
