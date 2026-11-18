<?php

namespace App\Services;

use App\Models\MutasiSaldoMitra;
use App\Models\Pemesanan;
use App\Models\PenarikanDana;
use App\Models\Pengguna;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Credit owner wallet (dana masuk dari transaksi pemesanan sewa).
     */
    public function credit(Pengguna $pemilik, int $amount, string $description, ?Pemesanan $pemesanan = null): MutasiSaldoMitra
    {
        if ($amount <= 0) {
            throw new Exception('Nominal kredit harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($pemilik, $amount, $description, $pemesanan) {
            // Lock latest mutasi record to calculate balance accurately
            $latest = MutasiSaldoMitra::where('pengguna_id', $pemilik->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $currentSaldo = $latest ? (int) $latest->saldo_akhir : 0;
            $newSaldo = $currentSaldo + $amount;

            return MutasiSaldoMitra::create([
                'pengguna_id' => $pemilik->id,
                'pemesanan_id' => $pemesanan?->id,
                'tipe' => 'kredit',
                'nominal' => $amount,
                'saldo_akhir' => $newSaldo,
                'deskripsi' => $description,
            ]);
        });
    }

    /**
     * Debit owner wallet (penarikan dana).
     */
    public function debit(Pengguna $pemilik, int $amount, string $description, ?PenarikanDana $penarikan = null): MutasiSaldoMitra
    {
        if ($amount <= 0) {
            throw new Exception('Nominal debit harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($pemilik, $amount, $description, $penarikan) {
            $latest = MutasiSaldoMitra::where('pengguna_id', $pemilik->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $currentSaldo = $latest ? (int) $latest->saldo_akhir : 0;

            if ($currentSaldo < $amount) {
                throw new Exception("Saldo tidak mencukupi untuk melakukan penarikan dana. Saldo tersedia: Rp {$currentSaldo}");
            }

            $newSaldo = $currentSaldo - $amount;

            return MutasiSaldoMitra::create([
                'pengguna_id' => $pemilik->id,
                'penarikan_dana_id' => $penarikan?->id,
                'tipe' => 'debit',
                'nominal' => $amount,
                'saldo_akhir' => $newSaldo,
                'deskripsi' => $description,
            ]);
        });
    }

    /**
     * Get financial summary for an owner.
     *
     * @return array{saldo_tersedia: int, total_pendapatan: int, total_ditarik: int, sedang_ditarik: int}
     */
    public function getSummary(Pengguna $pemilik): array
    {
        $saldoTersedia = $pemilik->saldoTersedia();

        $totalPendapatan = (int) MutasiSaldoMitra::where('pengguna_id', $pemilik->id)
            ->where('tipe', 'kredit')
            ->sum('nominal');

        $totalDitarik = (int) PenarikanDana::where('pengguna_id', $pemilik->id)
            ->where('status', 'selesai')
            ->sum('nominal');

        $sedangDitarik = (int) PenarikanDana::where('pengguna_id', $pemilik->id)
            ->whereIn('status', ['menunggu', 'diproses'])
            ->sum('nominal');

        return [
            'saldo_tersedia' => $saldoTersedia,
            'total_pendapatan' => $totalPendapatan,
            'total_ditarik' => $totalDitarik,
            'sedang_ditarik' => $sedangDitarik,
        ];
    }
}
