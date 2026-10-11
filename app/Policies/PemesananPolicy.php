<?php

namespace App\Policies;

use App\Models\Pemesanan;
use App\Models\Pengguna;

class PemesananPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Pengguna $pengguna): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Pencari yang memesan, Pemilik kos terkait, atau Admin.
     */
    public function view(Pengguna $pengguna, Pemesanan $pemesanan): bool
    {
        return (int) $pengguna->id === (int) $pemesanan->pengguna_id
            || (int) $pengguna->id === (int) $pemesanan->kos->pemilik_id
            || $pengguna->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     * Hanya role pencari yang dapat membuat pemesanan.
     */
    public function create(Pengguna $pengguna): bool
    {
        return $pengguna->role === 'pencari';
    }

    /**
     * Determine whether the user can update the status of the booking.
     * Pemilik: approve (disetujui) / reject (ditolak).
     * Pencari: cancel (dibatalkan) hanya jika status masih 'menunggu'.
     */
    public function updateStatus(Pengguna $pengguna, Pemesanan $pemesanan, ?string $targetStatus = null): bool
    {
        if ($pengguna->role === 'admin') {
            return true;
        }

        // Pemilik kos dapat menyetujui atau menolak
        if ((int) $pengguna->id === (int) $pemesanan->kos->pemilik_id) {
            return in_array($targetStatus, ['disetujui', 'ditolak'], true) || $targetStatus === null;
        }

        // Pencari hanya dapat membatalkan jika status saat ini masih 'menunggu'
        if ((int) $pengguna->id === (int) $pemesanan->pengguna_id) {
            return $pemesanan->status === 'menunggu' && ($targetStatus === 'dibatalkan' || $targetStatus === null);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pengguna $pengguna, Pemesanan $pemesanan): bool
    {
        return $this->updateStatus($pengguna, $pemesanan);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pengguna $pengguna, Pemesanan $pemesanan): bool
    {
        return $pengguna->role === 'admin';
    }
}
