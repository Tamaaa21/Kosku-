<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Http\Resources\UserResource;
use App\Models\Kamar;
use App\Models\Kos;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users for admin management.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Pengguna::query()
            ->withCount(['kos', 'pemesanan']);

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('q')) {
            $keyword = (string) $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('telepon', 'like', "%{$keyword}%");
            });
        }

        if ($request->has('aktif') && $request->input('aktif') !== '' && $request->input('aktif') !== 'all') {
            $query->where('aktif', $request->boolean('aktif'));
        }

        if ($request->input('sort') === 'unit_terbanyak') {
            $query->orderByDesc('kos_count');
        } elseif ($request->input('sort') === 'nama_asc') {
            $query->reorder()->orderBy('nama', 'asc');
        } elseif ($request->input('sort') === 'latest') {
            $query->latest();
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = min(max((int) $request->input('per_page', 50), 1), 100);
        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Display the specified user details with their related kos or pemesanan for admin inspection.
     */
    public function show(int $id): JsonResponse
    {
        $pengguna = Pengguna::withCount(['kos', 'pemesanan'])
            ->with([
                'kos' => fn ($q) => $q->withCount('kamar')->latest(),
                'pemesanan' => fn ($q) => $q->with(['kos:id,nama', 'kamar:id,nama_kamar,harga'])->latest()->limit(10),
            ])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $pengguna->id,
                'nama' => $pengguna->nama,
                'email' => $pengguna->email,
                'telepon' => $pengguna->telepon,
                'no_hp' => $pengguna->telepon,
                'role' => $pengguna->role,
                'aktif' => (bool) $pengguna->aktif,
                'kos_count' => $pengguna->kos_count,
                'pemesanan_count' => $pengguna->pemesanan_count,
                'daftar_kos' => $pengguna->kos,
                'riwayat_pemesanan' => $pengguna->pemesanan,
                'created_at' => $pengguna->created_at?->toISOString(),
                'updated_at' => $pengguna->updated_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Update active status of a user account.
     */
    public function updateStatus(UpdateUserStatusRequest $request, int $id): JsonResponse
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update([
            'aktif' => $request->validated('aktif'),
        ]);

        return response()->json([
            'message' => 'Status akun pengguna berhasil diperbarui.',
            'data' => new UserResource($pengguna),
        ]);
    }

    /**
     * Get aggregated system KPIs, occupancy, revenue, and daily trends for admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $totalPengguna = Pengguna::count();
        $totalPencari = Pengguna::where('role', 'pencari')->count();
        $totalPemilik = Pengguna::where('role', 'pemilik')->count();
        $penggunaAktif = Pengguna::where('aktif', true)->count();
        $penggunaNonaktif = Pengguna::where('aktif', false)->count();

        $totalKos = Kos::count();
        $totalKamar = Kamar::count();
        $kamarTersedia = Kamar::where('status', 'tersedia')->count();
        $kamarTerisi = Kamar::where('status', 'terisi')->count();
        $okupansiRate = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100, 1) : 0;

        $totalPemesanan = Pemesanan::count();
        $pemesananMenunggu = Pemesanan::where('status', 'menunggu')->count();
        $pemesananDisetujui = Pemesanan::where('status', 'disetujui')->count();
        $pemesananDitolak = Pemesanan::where('status', 'ditolak')->count();

        $totalEstimasiNilai = (int) Pemesanan::where('pemesanan.status', 'disetujui')
            ->join('kamar', 'pemesanan.kamar_id', '=', 'kamar.id')
            ->sum('kamar.harga');

        $kosJenis = [
            'putri' => Kos::where('jenis', 'putri')->count(),
            'putra' => Kos::where('jenis', 'putra')->count(),
            'campur' => Kos::where('jenis', 'campur')->count(),
        ];

        // 7 days daily booking trends
        $dailyBookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $count = Pemesanan::whereDate('created_at', $dateStr)->count();
            $dailyBookings[] = [
                'tanggal' => $dateStr,
                'label' => $date->format('d M'),
                'count' => $count,
            ];
        }

        $pemesananTerbaru = Pemesanan::with(['pengguna:id,nama,email', 'kos:id,nama', 'kamar:id,nama_kamar,harga'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'total_pengguna' => $totalPengguna,
                'total_pencari' => $totalPencari,
                'total_pemilik' => $totalPemilik,
                'pengguna_aktif' => $penggunaAktif,
                'pengguna_nonaktif' => $penggunaNonaktif,
                'total_kos' => $totalKos,
                'total_kamar' => $totalKamar,
                'kamar_tersedia' => $kamarTersedia,
                'kamar_terisi' => $kamarTerisi,
                'okupansi_rate' => $okupansiRate,
                'total_pemesanan' => $totalPemesanan,
                'pemesanan_menunggu' => $pemesananMenunggu,
                'pemesanan_disetujui' => $pemesananDisetujui,
                'pemesanan_ditolak' => $pemesananDitolak,
                'total_nilai_transaksi' => $totalEstimasiNilai,
                'distribusi_jenis' => $kosJenis,
                'tren_harian' => $dailyBookings,
                'pemesanan_terbaru' => $pemesananTerbaru,
            ],
        ]);
    }
}
