<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-bold text-on-surface tracking-tight">Dashboard Administrator</h1>
          <span class="bg-primary-fixed text-on-primary-fixed text-xs font-bold px-2.5 py-0.5 rounded-full">System Admin</span>
        </div>
        <p class="text-sm text-slate-500 mt-1">
          Ringkasan eksekutif, analitik platform, kelola pengguna, master data fasilitas, dan log transaksi pemesanan.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          v-if="activeTab === 'ringkasan'"
          @click="fetchStats"
          :disabled="loadingStats"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loadingStats }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data
        </button>
        <button
          v-if="activeTab === 'fasilitas'"
          @click="openCreateFasilitasModal"
          class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Tambah Fasilitas Baru
        </button>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div v-if="successMsg" class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200 flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = null" class="text-emerald-700 font-bold ml-4">&times;</button>
    </div>
    <div v-if="errorMsg" class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 text-sm border border-rose-200 flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = null" class="text-rose-700 font-bold ml-4">&times;</button>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-3 overflow-x-auto">
      <button
        @click="activeTab = 'ringkasan'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'ringkasan' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Ringkasan & Analitik
      </button>

      <button
        @click="activeTab = 'pengguna'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'pengguna' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        Manajemen Pengguna ({{ usersList.length }})
      </button>

      <button
        @click="activeTab = 'fasilitas'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'fasilitas' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        Master Data Fasilitas ({{ fasilitasList.length }})
      </button>

      <button
        @click="activeTab = 'pemesanan'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'pemesanan' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Log Seluruh Pemesanan ({{ allBookings.length }})
      </button>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 0: RINGKASAN & ANALITIK (MODUL 1) -->
    <!-- ================================================================= -->
    <div v-if="activeTab === 'ringkasan'" class="space-y-8">
      <!-- Loading State -->
      <div v-if="loadingStats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-pulse">
        <div v-for="i in 4" :key="i" class="h-36 bg-slate-100 rounded-3xl"></div>
      </div>

      <!-- 4 KPI Cards Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Pengguna -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengguna</span>
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
          <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
            {{ stats.total_pengguna || 0 }}
          </div>
          <div class="mt-3 flex items-center justify-between text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span class="font-medium">
              <strong class="text-slate-800">{{ stats.total_pencari || 0 }}</strong> Pencari &bull;
              <strong class="text-slate-800">{{ stats.total_pemilik || 0 }}</strong> Pemilik
            </span>
            <span class="inline-flex items-center text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full text-[11px]">
              {{ stats.pengguna_aktif || 0 }} Aktif
            </span>
          </div>
        </div>

        <!-- Card 2: Kos & Okupansi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kos & Okupansi</span>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl font-extrabold text-slate-900 tracking-tight">
              {{ stats.total_kos || 0 }}
            </span>
            <span class="text-xs font-semibold text-slate-500">Properti Kos</span>
          </div>
          <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex justify-between text-xs mb-1">
              <span class="text-slate-500 font-medium">Tingkat Okupansi</span>
              <span class="font-bold text-emerald-700">{{ stats.okupansi_rate || 0 }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
              <div
                class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500"
                :style="{ width: `${Math.min(stats.okupansi_rate || 0, 100)}%` }"
              ></div>
            </div>
            <div class="text-[11px] text-slate-400 mt-1.5">
              {{ stats.kamar_terisi || 0 }} terisi dari {{ stats.total_kamar || 0 }} total kamar
            </div>
          </div>
        </div>

        <!-- Card 3: Reservasi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Reservasi</span>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
            {{ stats.total_pemesanan || 0 }}
          </div>
          <div class="mt-3 flex items-center gap-1.5 text-[11px] pt-3 border-t border-slate-100 flex-wrap">
            <span class="bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded-full border border-amber-200">
              {{ stats.pemesanan_menunggu || 0 }} Menunggu
            </span>
            <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
              {{ stats.pemesanan_disetujui || 0 }} Disetujui
            </span>
            <span class="bg-rose-50 text-rose-700 font-bold px-2 py-0.5 rounded-full border border-rose-200">
              {{ stats.pemesanan_ditolak || 0 }} Ditolak
            </span>
          </div>
        </div>

        <!-- Card 4: Estimasi Transaksi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perputaran Nilai Sewa</span>
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl font-extrabold text-slate-900 tracking-tight">
            {{ formatRupiah(stats.total_nilai_transaksi || 0) }}
          </div>
          <div class="mt-3 flex items-center justify-between text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span class="text-[11px] text-slate-500">Estimasi sewa terkonfirmasi</span>
            <span class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-full">
              Sewa Disetujui
            </span>
          </div>
        </div>
      </div>

      <!-- Charts & Breakdown Section (2 Columns) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart: Tren Reservasi 7 Hari -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-base font-bold text-slate-900">Tren Pemesanan (7 Hari Terakhir)</h2>
              <p class="text-xs text-slate-500 mt-0.5">Pantau lonjakan reservasi kamar sewa harian platform</p>
            </div>
            <div class="text-right">
              <span class="text-xs font-semibold text-slate-500">Total 7 Hari:</span>
              <span class="ml-1.5 text-sm font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                {{ total7HariCount }} Transaksi
              </span>
            </div>
          </div>

          <!-- SVG Chart -->
          <div class="relative w-full h-52">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 500 150" preserveAspectRatio="none">
              <defs>
                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#10b981" stop-opacity="0.25" />
                  <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                </linearGradient>
              </defs>
              <!-- Horizontal Grid Lines -->
              <line x1="20" y1="20" x2="480" y2="20" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
              <line x1="20" y1="65" x2="480" y2="65" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
              <line x1="20" y1="110" x2="480" y2="110" stroke="#f1f5f9" stroke-width="1" />

              <!-- Area Fill -->
              <polygon v-if="chartAreaPath" :points="chartAreaPath" fill="url(#chartGradient)" />

              <!-- Curve Line -->
              <polyline
                v-if="chartLinePath"
                :points="chartLinePath"
                fill="none"
                stroke="#10b981"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
              />

              <!-- Points & Tooltips -->
              <g v-for="(p, idx) in chartPoints" :key="idx">
                <circle
                  :cx="p.x"
                  :cy="p.y"
                  r="5"
                  fill="#ffffff"
                  stroke="#10b981"
                  stroke-width="2.5"
                  class="transition-all hover:r-7 cursor-pointer"
                />
                <!-- Value Text -->
                <text
                  :x="p.x"
                  :y="p.y - 10"
                  text-anchor="middle"
                  class="text-[10px] font-bold fill-slate-700"
                >
                  {{ p.count }}
                </text>
              </g>
            </svg>
          </div>

          <!-- Date Labels Below Chart -->
          <div class="flex justify-between text-[11px] text-slate-400 font-semibold px-2 mt-2">
            <span v-for="(item, idx) in stats.tren_harian || []" :key="idx">
              {{ item.label }}
            </span>
          </div>
        </div>

        <!-- Breakdown: Distribusi Jenis Kos -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between">
          <div>
            <h2 class="text-base font-bold text-slate-900 mb-1">Distribusi Jenis Kos</h2>
            <p class="text-xs text-slate-500 mb-6">Proporsi hunian berdasarkan kategori gender</p>

            <div class="space-y-5">
              <!-- Putri -->
              <div>
                <div class="flex justify-between items-center text-xs mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="font-bold text-slate-700">Kos Putri</span>
                  </div>
                  <span class="font-extrabold text-slate-900">
                    {{ stats.distribusi_jenis?.putri || 0 }} unit
                    <span class="text-slate-400 font-normal">({{ jenisPct('putri') }}%)</span>
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="bg-rose-500 h-2 rounded-full transition-all duration-500" :style="{ width: `${jenisPct('putri')}%` }"></div>
                </div>
              </div>

              <!-- Putra -->
              <div>
                <div class="flex justify-between items-center text-xs mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-500"></span>
                    <span class="font-bold text-slate-700">Kos Putra</span>
                  </div>
                  <span class="font-extrabold text-slate-900">
                    {{ stats.distribusi_jenis?.putra || 0 }} unit
                    <span class="text-slate-400 font-normal">({{ jenisPct('putra') }}%)</span>
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="bg-sky-500 h-2 rounded-full transition-all duration-500" :style="{ width: `${jenisPct('putra')}%` }"></div>
                </div>
              </div>

              <!-- Campur -->
              <div>
                <div class="flex justify-between items-center text-xs mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                    <span class="font-bold text-slate-700">Kos Campur</span>
                  </div>
                  <span class="font-extrabold text-slate-900">
                    {{ stats.distribusi_jenis?.campur || 0 }} unit
                    <span class="text-slate-400 font-normal">({{ jenisPct('campur') }}%)</span>
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" :style="{ width: `${jenisPct('campur')}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
            <span>Total Kos Terdaftar</span>
            <span class="font-bold text-slate-900">{{ stats.total_kos || 0 }} Properti</span>
          </div>
        </div>
      </div>

      <!-- Recent Reservations Activity Log -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="text-base font-bold text-slate-900">Aktivitas Reservasi Terbaru</h2>
            <p class="text-xs text-slate-500 mt-0.5">5 transaksi pemesanan terkini yang masuk ke dalam sistem</p>
          </div>
          <button
            @click="activeTab = 'pemesanan'"
            class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition"
          >
            Lihat Semua Log Pemesanan
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
              <tr>
                <th class="py-3 px-4">ID</th>
                <th class="py-3 px-4">Penyewa</th>
                <th class="py-3 px-4">Kos & Kamar</th>
                <th class="py-3 px-4">Tarif Sewa</th>
                <th class="py-3 px-4">Tanggal Pesan</th>
                <th class="py-3 px-4">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="(stats.pemesanan_terbaru || []).length === 0">
                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada transaksi pemesanan terkini.</td>
              </tr>
              <tr v-for="b in stats.pemesanan_terbaru || []" :key="b.id" class="hover:bg-slate-50/60 transition">
                <td class="py-3 px-4 font-mono font-bold text-slate-900">#{{ b.id }}</td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900">{{ b.pengguna?.nama || '-' }}</div>
                  <div class="text-slate-400 text-[11px]">{{ b.pengguna?.email || '-' }}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900">{{ b.kos?.nama || '-' }}</div>
                  <div class="text-emerald-700 font-bold text-[11px]">Kamar: {{ b.kamar?.nama_kamar || '-' }}</div>
                </td>
                <td class="py-3 px-4 font-bold text-slate-900 font-mono">
                  {{ b.kamar?.harga ? formatRupiah(b.kamar.harga) : '-' }}
                </td>
                <td class="py-3 px-4">{{ formatDateOnly(b.tanggal_pemesanan) }}</td>
                <td class="py-3 px-4">
                  <span
                    :class="[
                      'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border',
                      statusBadgeClass(b.status)
                    ]"
                  >
                    {{ b.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 1: MANAJEMEN PENGGUNA -->
    <!-- ================================================================= -->
    <div v-if="activeTab === 'pengguna'" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
      <!-- Search & Role Filter -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
          <input
            type="text"
            v-model="userSearchQuery"
            placeholder="Cari nama atau email..."
            class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5"
            @input="debounceUserSearch"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="userRoleFilter"
            @change="fetchUsers"
            class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white"
          >
            <option value="">Semua Peran</option>
            <option value="pencari">Pencari</option>
            <option value="pemilik">Pemilik</option>
            <option value="admin">Admin</option>
          </select>
          <button
            @click="fetchUsers"
            class="p-2.5 text-slate-600 hover:bg-slate-100 rounded-xl border border-slate-200"
            title="Segarkan Data"
          >
            <svg class="w-4 h-4" :class="{ 'animate-spin': loadingUsers }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Users Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
            <tr>
              <th class="py-3 px-4">Pengguna</th>
              <th class="py-3 px-4">Kontak / HP</th>
              <th class="py-3 px-4">Peran (Role)</th>
              <th class="py-3 px-4">Status Akun</th>
              <th class="py-3 px-4">Terdaftar</th>
              <th class="py-3 px-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingUsers">
              <td colspan="6" class="py-8 text-center text-slate-400">Memuat data pengguna...</td>
            </tr>
            <tr v-else-if="usersList.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengguna ditemukan.</td>
            </tr>
            <tr v-for="user in usersList" :key="user.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">{{ user.nama }}</div>
                <div class="text-slate-400 text-[11px]">{{ user.email }}</div>
              </td>
              <td class="py-3 px-4 font-mono">{{ user.no_hp || '-' }}</td>
              <td class="py-3 px-4">
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border',
                    user.role === 'admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' :
                    user.role === 'pemilik' ? 'bg-amber-50 text-amber-700 border-amber-200' :
                    'bg-emerald-50 text-emerald-700 border-emerald-200'
                  ]"
                >
                  {{ user.role }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span
                  :class="[
                    'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full',
                    user.aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'
                  ]"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="user.aktif ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                  {{ user.aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="py-3 px-4 text-slate-400">{{ formatDate(user.created_at) }}</td>
              <td class="py-3 px-4 text-right">
                <button
                  @click="toggleUserActive(user)"
                  :disabled="togglingUserId === user.id"
                  :class="[
                    'text-[11px] font-bold px-3 py-1.5 rounded-xl border transition',
                    user.aktif
                      ? 'border-rose-200 text-rose-600 hover:bg-rose-50'
                      : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50'
                  ]"
                >
                  {{ user.aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: MASTER FASILITAS -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'fasilitas'" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
            <tr>
              <th class="py-3 px-4">ID</th>
              <th class="py-3 px-4">Nama Fasilitas</th>
              <th class="py-3 px-4">Icon Identifier</th>
              <th class="py-3 px-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingFasilitas">
              <td colspan="4" class="py-8 text-center text-slate-400">Memuat data fasilitas...</td>
            </tr>
            <tr v-else-if="fasilitasList.length === 0">
              <td colspan="4" class="py-8 text-center text-slate-400">Belum ada master fasilitas.</td>
            </tr>
            <tr v-for="fas in fasilitasList" :key="fas.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4 font-mono font-bold text-slate-400">#{{ fas.id }}</td>
              <td class="py-3 px-4 font-bold text-slate-900">{{ fas.nama }}</td>
              <td class="py-3 px-4 font-mono text-slate-500">{{ fas.icon || '-' }}</td>
              <td class="py-3 px-4 text-right space-x-2">
                <button
                  @click="openEditFasilitasModal(fas)"
                  class="text-[11px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition"
                >
                  Edit
                </button>
                <button
                  @click="deleteFasilitas(fas.id)"
                  class="text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-xl transition"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 3: SELURUH PEMESANAN SISTEM -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'pemesanan'" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
            <tr>
              <th class="py-3 px-4">ID Pesanan</th>
              <th class="py-3 px-4">Penyewa (Pencari)</th>
              <th class="py-3 px-4">Properti Kos</th>
              <th class="py-3 px-4">Kamar</th>
              <th class="py-3 px-4">Tanggal Masuk</th>
              <th class="py-3 px-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingBookings">
              <td colspan="6" class="py-8 text-center text-slate-400">Memuat log pemesanan...</td>
            </tr>
            <tr v-else-if="allBookings.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">Belum ada aktivitas transaksi pemesanan.</td>
            </tr>
            <tr v-for="b in allBookings" :key="b.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4 font-mono font-bold text-slate-900">#{{ b.id }}</td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">{{ b.pengguna?.nama || '-' }}</div>
                <div class="text-slate-400 text-[11px]">{{ b.pengguna?.email || '-' }}</div>
              </td>
              <td class="py-3 px-4 font-semibold text-slate-800">{{ b.kos?.nama || '-' }}</td>
              <td class="py-3 px-4 text-emerald-700 font-bold">{{ b.kamar?.nama_kamar || '-' }}</td>
              <td class="py-3 px-4">{{ formatDateOnly(b.tanggal_pemesanan) }}</td>
              <td class="py-3 px-4">
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border',
                    statusBadgeClass(b.status)
                  ]"
                >
                  {{ b.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: CREATE / EDIT FASILITAS -->
    <!-- ================================================================= -->
    <div
      v-if="showFasilitasModal"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <h3 class="text-base font-black text-slate-900">
            {{ isEditingFasilitas ? 'Edit Master Fasilitas' : 'Tambah Fasilitas Baru' }}
          </h3>
          <button @click="showFasilitasModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveFasilitas" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Fasilitas</label>
            <input
              type="text"
              v-model="fasilitasForm.nama"
              required
              class="w-full text-xs rounded-xl border-slate-200 p-2.5"
              placeholder="Cth: WiFi Cepat 50 Mbps"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Icon Key (Opsional)</label>
            <input
              type="text"
              v-model="fasilitasForm.icon"
              class="w-full text-xs rounded-xl border-slate-200 p-2.5"
              placeholder="Cth: wifi, ac, parking"
            />
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showFasilitasModal = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="savingFasilitas"
              class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition disabled:opacity-50 shadow-sm"
            >
              {{ savingFasilitas ? 'Menyimpan...' : 'Simpan Fasilitas' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import api from '../services/api'

const activeTab = ref('ringkasan')

const stats = ref({})
const loadingStats = ref(false)

const usersList = ref([])
const fasilitasList = ref([])
const allBookings = ref([])

const loadingUsers = ref(false)
const loadingFasilitas = ref(false)
const loadingBookings = ref(false)
const togglingUserId = ref(null)

const userSearchQuery = ref('')
const userRoleFilter = ref('')

const successMsg = ref(null)
const errorMsg = ref(null)

const fetchStats = async () => {
  loadingStats.value = true
  try {
    const res = await api.get('/admin/stats')
    stats.value = res.data.data || {}
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat analitik dashboard.'
  } finally {
    loadingStats.value = false
  }
}

const chartPoints = computed(() => {
  const trend = stats.value.tren_harian || []
  if (trend.length === 0) return []
  const maxVal = Math.max(...trend.map(d => d.count), 5)
  const left = 30
  const right = 470
  const top = 25
  const bottom = 110

  return trend.map((d, i) => {
    const x = trend.length > 1 ? left + (i / (trend.length - 1)) * (right - left) : 250
    const y = bottom - (d.count / maxVal) * (bottom - top)
    return { x, y, count: d.count, label: d.label }
  })
})

const chartLinePath = computed(() => {
  if (chartPoints.value.length === 0) return ''
  return chartPoints.value.map(p => `${p.x},${p.y}`).join(' ')
})

const chartAreaPath = computed(() => {
  if (chartPoints.value.length === 0) return ''
  const bottom = 110
  const first = chartPoints.value[0]
  const last = chartPoints.value[chartPoints.value.length - 1]
  const lineCoords = chartPoints.value.map(p => `${p.x},${p.y}`).join(' ')
  return `${first.x},${bottom} ${lineCoords} ${last.x},${bottom}`
})

const total7HariCount = computed(() => {
  const trend = stats.value.tren_harian || []
  return trend.reduce((sum, item) => sum + (item.count || 0), 0)
})

const jenisPct = (type) => {
  const total = stats.value.total_kos || 0
  if (total === 0) return 0
  const count = stats.value.distribusi_jenis?.[type] || 0
  return Math.round((count / total) * 100)
}

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val || 0)
}

// Fasilitas Modal State
const showFasilitasModal = ref(false)
const isEditingFasilitas = ref(false)
const editingFasilitasId = ref(null)
const savingFasilitas = ref(false)
const fasilitasForm = reactive({
  nama: '',
  icon: '',
})

let searchTimeout = null
const debounceUserSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchUsers()
  }, 350)
}

const fetchUsers = async () => {
  loadingUsers.value = true
  try {
    const params = {}
    if (userSearchQuery.value) params.q = userSearchQuery.value
    if (userRoleFilter.value) params.role = userRoleFilter.value

    const res = await api.get('/admin/pengguna', { params })
    usersList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat pengguna.'
  } finally {
    loadingUsers.value = false
  }
}

const toggleUserActive = async (user) => {
  togglingUserId.value = user.id
  try {
    const nextStatus = !user.aktif
    await api.patch(`/admin/pengguna/${user.id}/status`, { aktif: nextStatus })
    user.aktif = nextStatus
    successMsg.value = `Status akun ${user.nama} berhasil diubah.`
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memperbarui status akun.'
  } finally {
    togglingUserId.value = null
  }
}

const fetchFasilitas = async () => {
  loadingFasilitas.value = true
  try {
    const res = await api.get('/fasilitas')
    fasilitasList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat master fasilitas.'
  } finally {
    loadingFasilitas.value = false
  }
}

const openCreateFasilitasModal = () => {
  isEditingFasilitas.value = false
  editingFasilitasId.value = null
  fasilitasForm.nama = ''
  fasilitasForm.icon = ''
  showFasilitasModal.value = true
}

const openEditFasilitasModal = (fas) => {
  isEditingFasilitas.value = true
  editingFasilitasId.value = fas.id
  fasilitasForm.nama = fas.nama
  fasilitasForm.icon = fas.icon || ''
  showFasilitasModal.value = true
}

const saveFasilitas = async () => {
  savingFasilitas.value = true
  try {
    if (isEditingFasilitas.value) {
      await api.put(`/fasilitas/${editingFasilitasId.value}`, fasilitasForm)
      successMsg.value = 'Fasilitas berhasil diperbarui.'
    } else {
      await api.post('/fasilitas', fasilitasForm)
      successMsg.value = 'Fasilitas baru berhasil ditambahkan.'
    }
    showFasilitasModal.value = false
    await fetchFasilitas()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan fasilitas.'
  } finally {
    savingFasilitas.value = false
  }
}

const deleteFasilitas = async (id) => {
  if (!confirm('Hapus master fasilitas ini?')) return
  try {
    await api.delete(`/fasilitas/${id}`)
    successMsg.value = 'Fasilitas berhasil dihapus.'
    await fetchFasilitas()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menghapus fasilitas.'
  }
}

const fetchAllBookings = async () => {
  loadingBookings.value = true
  try {
    const res = await api.get('/pemesanan')
    allBookings.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat pemesanan.'
  } finally {
    loadingBookings.value = false
  }
}

const statusBadgeClass = (status) => {
  switch (status) {
    case 'menunggu':
      return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'disetujui':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'ditolak':
      return 'bg-rose-50 text-rose-700 border-rose-200'
    case 'dibatalkan':
      return 'bg-slate-100 text-slate-600 border-slate-300'
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchStats()
  fetchUsers()
  fetchFasilitas()
  fetchAllBookings()
})
</script>
