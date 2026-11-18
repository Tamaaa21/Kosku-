<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingStatusRequest;
use App\Http\Resources\BookingResource;
use App\Models\Kamar;
use App\Models\Pemesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings based on the authenticated user's role.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $query = Pemesanan::with(['pengguna', 'kos.pemilik', 'kamar']);

        if ($request->input('sort') === 'latest') {
            $query->latest();
        } elseif ($request->input('sort') === 'id_asc' || $user->role === 'pemilik' || $user->role === 'admin') {
            $query->orderBy('id', 'asc');
        } else {
            $query->latest();
        }

        if ($user->role === 'pencari') {
            $query->where('pengguna_id', $user->id);
        } elseif ($user->role === 'pemilik') {
            $query->whereHas('kos', function ($q) use ($user) {
                $q->where('pemilik_id', $user->id);
            });
        }
        // Admin dapat melihat seluruh pemesanan

        // Filter: pencarian teks (ID booking, nama pemesan, email, telepon, nama kos, atau nama kamar)
        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $cleanDigits = preg_replace('/\D/', '', $keyword);

            $query->where(function ($q) use ($keyword, $cleanDigits) {
                if (! empty($cleanDigits)) {
                    $q->orWhere('id', (int) $cleanDigits);
                }
                $q->orWhereHas('pengguna', function ($u) use ($keyword) {
                    $u->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('telepon', 'like', "%{$keyword}%");
                })->orWhereHas('kos', function ($k) use ($keyword) {
                    $k->where('nama', 'like', "%{$keyword}%");
                })->orWhereHas('kamar', function ($r) use ($keyword) {
                    $r->where('nama_kamar', 'like', "%{$keyword}%");
                });
            });
        }

        // Filter: status pemesanan
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Filter: periode waktu tanggal pemesanan
        if ($request->filled('periode') && $request->input('periode') !== 'all') {
            match ($request->input('periode')) {
                'today' => $query->whereDate('created_at', Carbon::today()),
                '7d' => $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay()),
                '30d' => $query->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay()),
                default => null,
            };
        }

        $perPage = min(max((int) $request->input('per_page', 50), 1), 100);
        $pemesananList = $query->paginate($perPage);

        return BookingResource::collection($pemesananList);
    }

    /**
     * Export rekapitulasi data riwayat pemesanan ke format file CSV streaming.
     */
    public function exportCsv(Request $request): StreamedResponse|JsonResponse
    {
        $user = $request->user();

        if (! in_array($user->role, ['pemilik', 'admin'], true)) {
            return response()->json([
                'message' => 'Hanya mitra pemilik kos dan administrator yang dapat mengekspor rekap pemesanan.',
            ], 403);
        }

        $query = Pemesanan::query()->with(['pengguna', 'kos', 'kamar'])->orderBy('id', 'asc');

        if ($user->role === 'pemilik') {
            $query->whereHas('kos', function ($q) use ($user) {
                $q->where('pemilik_id', $user->id);
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('periode') && $request->input('periode') !== 'all') {
            match ($request->input('periode')) {
                'today' => $query->whereDate('created_at', Carbon::today()),
                '7d' => $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay()),
                '30d' => $query->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay()),
                default => null,
            };
        }

        $fileName = 'rekap-pemesanan-kostara-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Pemesanan',
                'Nama Penyewa',
                'Email Penyewa',
                'Telepon Penyewa',
                'Nama Properti Kos',
                'Nama Unit Kamar',
                'Tanggal Mulai (Check-in)',
                'Durasi Sewa',
                'Estimasi Check-out',
                'Total Harga (Rp)',
                'Status Reservasi',
                'Waktu Pengajuan',
            ]);

            $query->chunk(100, function ($pemesananChunk) use ($handle): void {
                foreach ($pemesananChunk as $p) {
                    $durasi = ($p->durasi_jumlah ?? 1).' '.ucfirst($p->durasi_tipe ?? 'bulanan');
                    fputcsv($handle, [
                        $p->id,
                        $p->pengguna?->nama ?? '-',
                        $p->pengguna?->email ?? '-',
                        $p->pengguna?->telepon ?? '-',
                        $p->kos?->nama ?? '-',
                        $p->kamar?->nama_kamar ?? '-',
                        $p->tanggal_pemesanan,
                        $durasi,
                        $p->tanggal_selesai ?? '-',
                        $p->total_harga,
                        ucfirst($p->status),
                        $p->created_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Store a newly created booking with atomic double-booking prevention.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $pemesanan = DB::transaction(function () use ($validated, $user) {
            /** @var Kamar|null $kamar */
            $kamar = Kamar::where('id', $validated['kamar_id'])
                ->lockForUpdate()
                ->first();

            if (! $kamar || $kamar->status !== 'tersedia') {
                return false;
            }

            // Cek apakah ada pemesanan lain yang masih aktif (menunggu atau disetujui) untuk kamar ini
            $hasConflict = Pemesanan::where('kamar_id', $kamar->id)
                ->whereIn('status', ['menunggu', 'disetujui'])
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                return false;
            }

            $durasiTipe = $validated['durasi_tipe'] ?? 'bulanan';
            $durasiJumlah = (int) ($validated['durasi_jumlah'] ?? 1);
            $startDate = Carbon::parse($validated['tanggal_pemesanan']);

            $endDate = match ($durasiTipe) {
                'harian' => (clone $startDate)->addDays($durasiJumlah),
                'mingguan' => (clone $startDate)->addWeeks($durasiJumlah),
                default => (clone $startDate)->addMonths($durasiJumlah),
            };

            $rate = match ($durasiTipe) {
                'harian' => $kamar->harga_harian ?: (int) ceil($kamar->harga / 30),
                'mingguan' => $kamar->harga_mingguan ?: (int) ceil($kamar->harga / 4),
                default => $kamar->harga,
            };

            $totalHarga = $rate * $durasiJumlah;

            $persentaseDp = (int) config('midtrans.dp_percentage', 20);
            $nominalDp = (int) round(($totalHarga * $persentaseDp) / 100);
            $sisaPelunasan = $totalHarga - $nominalDp;

            $komisiPersen = (int) config('midtrans.commission_percentage', 5);
            $komisiNominal = (int) round(($totalHarga * $komisiPersen) / 100);
            $totalHakMitra = $totalHarga - $komisiNominal;

            return Pemesanan::create([
                'pengguna_id' => $user->id,
                'kos_id' => $kamar->kos_id,
                'kamar_id' => $kamar->id,
                'tanggal_pemesanan' => $validated['tanggal_pemesanan'],
                'durasi_tipe' => $durasiTipe,
                'durasi_jumlah' => $durasiJumlah,
                'tanggal_selesai' => $endDate->format('Y-m-d'),
                'total_harga' => $totalHarga,
                'persentase_dp' => $persentaseDp,
                'nominal_dp' => $nominalDp,
                'sisa_pelunasan' => $sisaPelunasan,
                'komisi_persen' => $komisiPersen,
                'komisi_nominal' => $komisiNominal,
                'total_hak_mitra' => $totalHakMitra,
                'status_pembayaran' => 'belum_dibayar',
                'status' => 'menunggu',
            ]);
        });

        if (! $pemesanan) {
            return response()->json([
                'message' => 'Kamar tidak tersedia atau sedang dalam proses pemesanan.',
            ], 409);
        }

        $pemesanan->load(['pengguna', 'kos', 'kamar']);

        return response()->json([
            'message' => 'Pemesanan berhasil diajukan.',
            'data' => new BookingResource($pemesanan),
        ], 201);
    }

    /**
     * Display the specified booking details.
     */
    public function show(int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['pengguna', 'kos', 'kamar'])->findOrFail($id);
        Gate::authorize('view', $pemesanan);

        return response()->json([
            'data' => new BookingResource($pemesanan),
        ]);
    }

    /**
     * Update the booking status (approval/rejection/cancellation).
     */
    public function updateStatus(UpdateBookingStatusRequest $request, int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kos', 'kamar'])->findOrFail($id);
        $newStatus = $request->validated('status');

        Gate::authorize('updateStatus', [$pemesanan, $newStatus]);

        $result = DB::transaction(function () use ($pemesanan, $newStatus) {
            /** @var Kamar|null $kamar */
            $kamar = Kamar::where('id', $pemesanan->kamar_id)->lockForUpdate()->first();

            if (! $kamar) {
                return false;
            }

            if ($newStatus === 'disetujui') {
                $alreadyOccupied = Pemesanan::where('kamar_id', $pemesanan->kamar_id)
                    ->where('id', '!=', $pemesanan->id)
                    ->where('status', 'disetujui')
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyOccupied || $kamar->status === 'terisi') {
                    return false;
                }

                $kamar->update(['status' => 'terisi']);
            } elseif (in_array($newStatus, ['dibatalkan', 'ditolak'], true) && $kamar->status === 'terisi') {
                $kamar->update(['status' => 'tersedia']);
            }

            $pemesanan->update(['status' => $newStatus]);

            return true;
        });

        if (! $result) {
            return response()->json([
                'message' => 'Tidak dapat menyetujui pemesanan karena kamar sudah terisi oleh penyewa lain.',
            ], 409);
        }

        $pemesanan->load(['pengguna', 'kos', 'kamar']);

        return response()->json([
            'message' => "Status pemesanan berhasil diperbarui menjadi {$newStatus}.",
            'data' => new BookingResource($pemesanan),
        ]);
    }

    /**
     * Get active tenants for properties owned by the authenticated owner.
     */
    public function activeTenants(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! in_array($user->role, ['pemilik', 'admin'], true)) {
            return response()->json([
                'message' => 'Hanya mitra pemilik kos dan administrator yang dapat mengakses daftar penghuni.',
            ], 403);
        }

        $baseQuery = Pemesanan::query()->with(['pengguna', 'kos.pemilik', 'kamar'])
            ->whereIn('status', ['dp_dibayar', 'siap_serah_terima', 'aktif', 'disetujui']);

        if ($user->role === 'pemilik') {
            $baseQuery->whereHas('kos', function ($q) use ($user): void {
                $q->where('pemilik_id', $user->id);
            });
        }

        // Summary counts
        $allOwnerActiveBookings = (clone $baseQuery)->get();
        $totalPenghuni = $allOwnerActiveBookings->where('status', 'aktif')->count();
        $totalSiapMasuk = $allOwnerActiveBookings->whereIn('status', ['dp_dibayar', 'siap_serah_terima', 'disetujui'])->count();

        $today = Carbon::today();
        $sevenDaysLater = Carbon::today()->addDays(7);
        $totalJatuhTempo = $allOwnerActiveBookings->filter(function ($item) use ($sevenDaysLater): bool {
            if ($item->status !== 'aktif' || ! $item->tanggal_selesai) {
                return false;
            }
            $endDate = Carbon::parse($item->tanggal_selesai);

            return $endDate->lte($sevenDaysLater);
        })->count();

        $kamarTerisiQuery = Kamar::query()->where('status', 'terisi');
        if ($user->role === 'pemilik') {
            $kamarTerisiQuery->whereHas('kos', fn ($k) => $k->where('pemilik_id', $user->id));
        }
        $kamarTerisi = $kamarTerisiQuery->count();

        // Apply filters
        $query = clone $baseQuery;

        $filter = $request->input('filter', 'all');
        if ($filter === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($filter === 'siap_masuk') {
            $query->whereIn('status', ['dp_dibayar', 'siap_serah_terima', 'disetujui']);
        } elseif ($filter === 'jatuh_tempo') {
            $query->where('status', 'aktif')
                ->where('tanggal_selesai', '<=', $sevenDaysLater->format('Y-m-d'));
        }

        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $cleanDigits = preg_replace('/\D/', '', $keyword);

            $query->where(function ($q) use ($keyword, $cleanDigits): void {
                if (! empty($cleanDigits)) {
                    $q->orWhere('id', (int) $cleanDigits);
                }
                $q->orWhereHas('pengguna', function ($u) use ($keyword): void {
                    $u->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('telepon', 'like', "%{$keyword}%");
                })->orWhereHas('kos', function ($k) use ($keyword): void {
                    $k->where('nama', 'like', "%{$keyword}%");
                })->orWhereHas('kamar', function ($r) use ($keyword): void {
                    $r->where('nama_kamar', 'like', "%{$keyword}%");
                });
            });
        }

        $tenants = $query->orderBy('tanggal_selesai', 'asc')->get();

        $data = $tenants->map(function ($item) use ($today) {
            $endDate = $item->tanggal_selesai ? Carbon::parse($item->tanggal_selesai) : null;
            $sisaHari = $endDate ? (int) $today->diffInDays($endDate, false) : null;

            $statusLabel = match ($item->status) {
                'aktif' => $sisaHari !== null && $sisaHari < 0
                    ? 'Masa Sewa Habis'
                    : ($sisaHari !== null && $sisaHari <= 7 ? "Segera Berakhir (H-{$sisaHari})" : 'Aktif'),
                'siap_serah_terima' => 'Lunas - Siap Serah Kunci',
                'dp_dibayar' => 'DP Terbayar - Siap Masuk',
                'disetujui' => 'Disetujui - Belum DP',
                default => ucfirst((string) $item->status),
            };

            return [
                'id' => $item->id,
                'pengguna' => [
                    'id' => $item->pengguna?->id,
                    'nama' => $item->pengguna?->nama ?? 'Calon Penghuni',
                    'email' => $item->pengguna?->email,
                    'telepon' => $item->pengguna?->telepon,
                    'foto' => $item->pengguna?->foto,
                ],
                'kos' => [
                    'id' => $item->kos?->id,
                    'nama' => $item->kos?->nama ?? 'Properti Kos',
                    'alamat' => $item->kos?->alamat,
                    'kota' => $item->kos?->kota,
                ],
                'kamar' => [
                    'id' => $item->kamar?->id,
                    'nama_kamar' => $item->kamar?->nama_kamar ?? 'Unit Kamar',
                    'tipe_kamar' => $item->kamar?->tipe_kamar,
                    'harga' => $item->kamar?->harga,
                ],
                'tanggal_pemesanan' => $item->tanggal_pemesanan,
                'tanggal_selesai' => $item->tanggal_selesai,
                'durasi_tipe' => $item->durasi_tipe,
                'durasi_jumlah' => $item->durasi_jumlah,
                'total_harga' => $item->total_harga,
                'persentase_dp' => $item->persentase_dp,
                'nominal_dp' => $item->nominal_dp,
                'sisa_pelunasan' => $item->sisa_pelunasan,
                'status_pembayaran' => $item->status_pembayaran,
                'status' => $item->status,
                'sisa_hari' => $sisaHari,
                'status_label' => $statusLabel,
                'kunci_diserahkan_at' => $item->kunci_diserahkan_at?->format('Y-m-d H:i:s'),
                'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'summary' => [
                'total_penghuni' => $totalPenghuni,
                'total_siap_masuk' => $totalSiapMasuk,
                'total_jatuh_tempo' => $totalJatuhTempo,
                'kamar_terisi' => $kamarTerisi,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Complete rental and check-out tenant, freeing up the room.
     */
    public function checkoutTenant(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $pemesanan = Pemesanan::with(['kos', 'kamar'])->findOrFail($id);

        $isOwner = $pemesanan->kos && (int) $pemesanan->kos->pemilik_id === (int) $user->id;
        $isAdmin = $user->role === 'admin';

        if (! $isOwner && ! $isAdmin) {
            return response()->json([
                'message' => 'Anda tidak berwenang melakukan check-out untuk pemesanan ini.',
            ], 403);
        }

        if ($pemesanan->status === 'selesai') {
            return response()->json([
                'message' => 'Penghuni untuk pemesanan ini sudah berstatus selesai (check-out).',
            ], 422);
        }

        DB::transaction(function () use ($pemesanan): void {
            $pemesanan->update(['status' => 'selesai']);

            if ($pemesanan->kamar) {
                $pemesanan->kamar->update(['status' => 'tersedia']);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Penghuni berhasil di-checkout dan kamar kembali tersedia.',
            'data' => new BookingResource($pemesanan->fresh(['pengguna', 'kos', 'kamar'])),
        ]);
    }
}
