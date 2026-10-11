<?php

namespace App\Policies;

use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pengguna;

class KamarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?Pengguna $pengguna): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?Pengguna $pengguna, Kamar $kamar): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models for a specific kos.
     */
    public function create(Pengguna $pengguna, ?Kos $kos = null): bool
    {
        if (! $kos) {
            return $pengguna->role === 'pemilik';
        }

        return (int) $pengguna->id === (int) $kos->pemilik_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pengguna $pengguna, Kamar $kamar): bool
    {
        return (int) $pengguna->id === (int) $kamar->kos->pemilik_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pengguna $pengguna, Kamar $kamar): bool
    {
        return (int) $pengguna->id === (int) $kamar->kos->pemilik_id || $pengguna->role === 'admin';
    }
}
