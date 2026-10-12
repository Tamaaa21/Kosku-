<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pemesanan\StorePemesananRequest;
use App\Http\Requests\Pemesanan\UpdatePemesananStatusRequest;
use App\Http\Resources\PemesananResource;
use App\Models\Kamar;
use App\Models\Pemesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PemesananController extends Controller
{
    /**
     * Display a listing of bookings based on the authenticated user's role.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $query = Pemesanan::with(['pengguna', 'kos.pemilik', 'kamar'])->latest();

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

        return PemesananResource::collection($pemesananList);
    }

    /**
     * Store a newly created booking with atomic double-booking prevention.
     */
    public function store(StorePemesananRequest $request): JsonResponse
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

            return Pemesanan::create([
                'pengguna_id' => $user->id,
                'kos_id' => $kamar->kos_id,
                'kamar_id' => $kamar->id,
                'tanggal_pemesanan' => $validated['tanggal_pemesanan'],
                'durasi_tipe' => $durasiTipe,
                'durasi_jumlah' => $durasiJumlah,
                'tanggal_selesai' => $endDate->format('Y-m-d'),
                'total_harga' => $totalHarga,
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
            'data' => new PemesananResource($pemesanan),
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
            'data' => new PemesananResource($pemesanan),
        ]);
    }

    /**
     * Update the booking status (approval/rejection/cancellation).
     */
    public function updateStatus(UpdatePemesananStatusRequest $request, int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kos', 'kamar'])->findOrFail($id);
        $newStatus = $request->validated('status');

        Gate::authorize('updateStatus', [$pemesanan, $newStatus]);

        DB::transaction(function () use ($pemesanan, $newStatus) {
            $pemesanan->update(['status' => $newStatus]);

            // Jika disetujui, ubah status kamar menjadi terisi secara otomatis
            if ($newStatus === 'disetujui') {
                $pemesanan->kamar->update(['status' => 'terisi']);
            }

            // Jika sebelumnya disetujui lalu dibatalkan atau ditolak, kembalikan status kamar ke tersedia
            if (in_array($newStatus, ['dibatalkan', 'ditolak'], true) && $pemesanan->kamar->status === 'terisi') {
                $pemesanan->kamar->update(['status' => 'tersedia']);
            }
        });

        $pemesanan->load(['pengguna', 'kos', 'kamar']);

        return response()->json([
            'message' => "Status pemesanan berhasil diperbarui menjadi {$newStatus}.",
            'data' => new PemesananResource($pemesanan),
        ]);
    }
}
