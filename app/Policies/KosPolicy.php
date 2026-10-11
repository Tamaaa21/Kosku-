<?php

namespace App\Policies;

use App\Models\Kos;
use App\Models\Pengguna;

class KosPolicy
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
    public function view(?Pengguna $pengguna, Kos $kos): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pengguna $pengguna): bool
    {
        return $pengguna->role === 'pemilik';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pengguna $pengguna, Kos $kos): bool
    {
        return (int) $pengguna->id === (int) $kos->pemilik_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pengguna $pengguna, Kos $kos): bool
    {
        return (int) $pengguna->id === (int) $kos->pemilik_id || $pengguna->role === 'admin';
    }
}
