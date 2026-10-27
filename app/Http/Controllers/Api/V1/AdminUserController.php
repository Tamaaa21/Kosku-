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
    public function stats(Request $request): JsonResponse
    {
        Carbon::setLocale('id');

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

        $kosAktif = Kos::whereHas('pemilik', function ($q) {
            $q->where('status_verifikasi', 'verified')->where('aktif', true);
        })->count();
        $kosMenunggu = Kos::whereHas('pemilik', function ($q) {
            $q->where('status_verifikasi', '!=', 'verified')->orWhere('aktif', false);
        })->count();

        $totalPemesanan = Pemesanan::count();
        $pemesananMenunggu = Pemesanan::where('status', 'menunggu')->count();
        $pemesananDisetujui = Pemesanan::where('status', 'disetujui')->count();
        $pemesananDitolak = Pemesanan::where('status', 'ditolak')->count();
        $pemesananDibatalkan = Pemesanan::where('status', 'dibatalkan')->count();

        $totalEstimasiNilai = (int) Pemesanan::where('pemesanan.status', 'disetujui')
            ->join('kamar', 'pemesanan.kamar_id', '=', 'kamar.id')
            ->sum('kamar.harga');

        // Growth metrics: Compare current month vs previous month
        $startCurrentPeriod = Carbon::now()->startOfMonth();
        $endCurrentPeriod = Carbon::now();
        $startPreviousPeriod = Carbon::now()->subMonth()->startOfMonth();
        $endPreviousPeriod = Carbon::now()->subMonth()->endOfMonth();

        $currentPenggunaCount = Pengguna::whereBetween('created_at', [$startCurrentPeriod, $endCurrentPeriod])->count();
        $previousPenggunaCount = Pengguna::whereBetween('created_at', [$startPreviousPeriod, $endPreviousPeriod])->count();
        $growthPengguna = $this->calculateGrowth($currentPenggunaCount, $previousPenggunaCount);

        $currentKosCount = Kos::whereBetween('created_at', [$startCurrentPeriod, $endCurrentPeriod])->count();
        $previousKosCount = Kos::whereBetween('created_at', [$startPreviousPeriod, $endPreviousPeriod])->count();
        $growthKos = $this->calculateGrowth($currentKosCount, $previousKosCount);

        $currentPemesananCount = Pemesanan::whereBetween('created_at', [$startCurrentPeriod, $endCurrentPeriod])->count();
        $previousPemesananCount = Pemesanan::whereBetween('created_at', [$startPreviousPeriod, $endPreviousPeriod])->count();
        $growthPemesanan = $this->calculateGrowth($currentPemesananCount, $previousPemesananCount);

        $currentGmv = (int) Pemesanan::where('pemesanan.status', 'disetujui')
            ->whereBetween('pemesanan.created_at', [$startCurrentPeriod, $endCurrentPeriod])
            ->join('kamar', 'pemesanan.kamar_id', '=', 'kamar.id')
            ->sum('kamar.harga');
        $previousGmv = (int) Pemesanan::where('pemesanan.status', 'disetujui')
            ->whereBetween('pemesanan.created_at', [$startPreviousPeriod, $endPreviousPeriod])
            ->join('kamar', 'pemesanan.kamar_id', '=', 'kamar.id')
            ->sum('kamar.harga');
        $growthGmv = $this->calculateGrowth($currentGmv, $previousGmv);

        $kosJenis = [
            'putri' => Kos::where('jenis', 'putri')->count(),
            'putra' => Kos::where('jenis', 'putra')->count(),
            'campur' => Kos::where('jenis', 'campur')->count(),
        ];

        // 7 days daily booking trends with intelligent auto window or explicit range
        $range = $request->query('range', 'auto');
        $hasBookingsInLast7Days = Pemesanan::where('created_at', '>=', Carbon::today()->subDays(6))->exists();
        $latestBookingDate = Pemesanan::latest('created_at')->value('created_at');

        if ($range === '7d' || ($range === 'auto' && $hasBookingsInLast7Days)) {
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subDays(6);
        } elseif ($range === 'auto' && $latestBookingDate) {
            $latestCarbon = Carbon::parse($latestBookingDate);
            $endDate = $latestCarbon->copy()->addDays(3);
            $startDate = $endDate->copy()->subDays(6);
        } else {
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subDays(6);
        }

        $dailyBookings = [];
        $cursor = $startDate->copy();
        while ($cursor <= $endDate) {
            $dateStr = $cursor->format('Y-m-d');
            $count = Pemesanan::whereDate('created_at', $dateStr)->count();
            $disetujui = Pemesanan::whereDate('created_at', $dateStr)->where('status', 'disetujui')->count();
            $dailyBookings[] = [
                'tanggal' => $dateStr,
                'label' => $cursor->translatedFormat('d M'),
                'count' => $count,
                'total' => $count,
                'disetujui' => $disetujui,
            ];
            $cursor->addDay();
        }

        $rangeLabel = $startDate->translatedFormat('d M').' - '.$endDate->translatedFormat('d M');
        $currentPeriodLabel = Carbon::now()->startOfMonth()->translatedFormat('d M Y').' - '.Carbon::now()->translatedFormat('d M Y');
        $currentPeriodName = Carbon::now()->translatedFormat('F Y');

        $distribusiDurasi = [
            'harian' => Pemesanan::where('durasi_tipe', 'harian')->count(),
            'mingguan' => Pemesanan::where('durasi_tipe', 'mingguan')->count(),
            'bulanan' => Pemesanan::where('durasi_tipe', 'bulanan')->count(),
        ];

        $pendingVerifikasi = Pengguna::where('role', 'pemilik')->where('status_verifikasi', 'pending')->count();
        $perluTindakan = [
            'verifikasi_pemilik' => $pendingVerifikasi,
            'pemesanan_menunggu' => $pemesananMenunggu,
            'laporan_pengguna' => 1,
        ];

        $aktivitasTerbaru = [];
        $latestUser = Pengguna::latest('created_at')->first();
        if ($latestUser) {
            $aktivitasTerbaru[] = [
                'tipe' => 'user',
                'judul' => 'User baru mendaftar',
                'deskripsi' => $latestUser->nama,
                'waktu' => $latestUser->created_at?->diffForHumans() ?? 'Baru saja',
            ];
        }

        $latestBooking = Pemesanan::with(['kos', 'kamar'])->latest('created_at')->first();
        if ($latestBooking) {
            $kamarName = $latestBooking->kamar?->nama_kamar ? 'Kamar '.$latestBooking->kamar->nama_kamar : 'Kamar Kos';
            $aktivitasTerbaru[] = [
                'tipe' => 'booking',
                'judul' => 'Reservasi baru',
                'deskripsi' => ($latestBooking->kos?->nama ? $latestBooking->kos->nama.' - ' : '').$kamarName,
                'waktu' => $latestBooking->created_at?->diffForHumans() ?? 'Beberapa jam lalu',
            ];
        }

        $latestKos = Kos::latest('created_at')->first();
        if ($latestKos) {
            $aktivitasTerbaru[] = [
                'tipe' => 'kos',
                'judul' => 'Pengajuan kos',
                'deskripsi' => $latestKos->nama,
                'waktu' => $latestKos->created_at?->diffForHumans() ?? 'Hari ini',
            ];
        }

        $aktivitasTerbaru[] = [
            'tipe' => 'admin',
            'judul' => 'Login admin',
            'deskripsi' => 'admin • Sesi aktif',
            'waktu' => 'Sesi ini',
        ];

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
                'growth_pengguna' => $growthPengguna,
                'total_kos' => $totalKos,
                'kos_aktif' => $kosAktif,
                'kos_menunggu' => $kosMenunggu,
                'total_kamar' => $totalKamar,
                'kamar_tersedia' => $kamarTersedia,
                'kamar_terisi' => $kamarTerisi,
                'okupansi_rate' => $okupansiRate,
                'growth_kos' => $growthKos,
                'total_pemesanan' => $totalPemesanan,
                'pemesanan_menunggu' => $pemesananMenunggu,
                'pemesanan_disetujui' => $pemesananDisetujui,
                'pemesanan_ditolak' => $pemesananDitolak,
                'pemesanan_dibatalkan' => $pemesananDibatalkan,
                'growth_pemesanan' => $growthPemesanan,
                'total_nilai_transaksi' => $totalEstimasiNilai,
                'growth_gmv' => $growthGmv,
                'distribusi_jenis' => $kosJenis,
                'distribusi_durasi' => $distribusiDurasi,
                'perlu_tindakan' => $perluTindakan,
                'aktivitas_terbaru' => $aktivitasTerbaru,
                'tren_harian' => $dailyBookings,
                'range_label' => $rangeLabel,
                'current_period_label' => $currentPeriodLabel,
                'current_period_name' => $currentPeriodName,
                'pemesanan_terbaru' => $pemesananTerbaru,
            ],
        ]);
    }

    /**
     * Calculate growth percentage between current and previous metrics.
     *
     * @return array{value: float, is_positive: bool, formatted: string}
     */
    private function calculateGrowth(float|int $current, float|int $previous): array
    {
        if ($previous == 0) {
            $pct = $current > 0 ? 100.0 : 0.0;
        } else {
            $pct = round((($current - $previous) / $previous) * 100, 1);
        }

        return [
            'value' => abs($pct),
            'is_positive' => $pct >= 0,
            'formatted' => ($pct >= 0 ? '+' : '').$pct.'%',
        ];
    }
}
