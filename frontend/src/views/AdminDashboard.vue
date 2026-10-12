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
          Ringkasan eksekutif, kelola akun pencari, kemitraan pemilik kos, master fasilitas, dan audit transaksi.
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
          v-if="activeTab === 'pencari'"
          @click="fetchPencari"
          :disabled="loadingPencari"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loadingPencari }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Pencari
        </button>

        <button
          v-if="activeTab === 'pemilik'"
          @click="fetchPemilik"
          :disabled="loadingPemilik"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loadingPemilik }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Mitra
        </button>

        <button
          v-if="activeTab === 'kos'"
          @click="fetchKos"
          :disabled="loadingKos"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loadingKos }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data Kos
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
      <!-- 1. Ringkasan & Analitik (Modul 1) -->
      <button
        @click="switchTab('ringkasan')"
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

      <!-- 2. Kelola Pencari Kos (Modul 2) -->
      <button
        @click="switchTab('pencari')"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'pencari' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        Pencari Kos ({{ pencariList.length }})
      </button>

      <!-- 3. Kelola Mitra Pemilik (Modul 3) -->
      <button
        @click="switchTab('pemilik')"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'pemilik' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Mitra Pemilik Kos ({{ pemilikList.length }})
      </button>

      <!-- 4. Data & Moderasi Kos (Modul 4) -->
      <button
        @click="switchTab('kos')"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 shrink-0',
          activeTab === 'kos' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        Data & Moderasi Kos ({{ kosList.length }})
      </button>

      <!-- 5. Master Data Fasilitas -->
      <button
        @click="switchTab('fasilitas')"
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

      <!-- 5. Log Seluruh Pemesanan -->
      <button
        @click="switchTab('pemesanan')"
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
    <!-- TAB 1: KELOLA PENCARI KOS (MODUL 2) -->
    <!-- ================================================================= -->
    <div v-if="activeTab === 'pencari'" class="space-y-6">
      <!-- Search & Status Filter Bar -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="relative w-full sm:w-88">
            <input
              type="text"
              v-model="pencariSearchQuery"
              placeholder="Cari nama, email, atau nomor HP pencari..."
              class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5"
              @input="debouncePencariSearch"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <select
              v-model="pencariStatusFilter"
              @change="fetchPencari"
              class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white font-medium text-slate-700"
            >
              <option value="all">Semua Status Akun</option>
              <option value="1">Akun Aktif</option>
              <option value="0">Akun Nonaktif</option>
            </select>

            <button
              @click="fetchPencari"
              class="p-2.5 text-slate-600 hover:bg-slate-100 rounded-xl border border-slate-200 transition"
              title="Segarkan Data Pencari"
            >
              <svg class="w-4 h-4" :class="{ 'animate-spin': loadingPencari }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Pencari Table Card -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
              <tr>
                <th class="py-3.5 px-4 w-12 text-center">No</th>
                <th class="py-3.5 px-4">Pencari Kos</th>
                <th class="py-3.5 px-4">Kontak WhatsApp</th>
                <th class="py-3.5 px-4 text-center">Riwayat Pesanan</th>
                <th class="py-3.5 px-4 text-center">Status Akun</th>
                <th class="py-3.5 px-4">Terdaftar</th>
                <th class="py-3.5 px-4 text-right pr-6">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loadingPencari">
                <td colspan="7" class="py-10 text-center text-slate-400">Memuat data pencari kos...</td>
              </tr>
              <tr v-else-if="pencariList.length === 0">
                <td colspan="7" class="py-10 text-center text-slate-400">Tidak ada data pencari kos yang sesuai.</td>
              </tr>
              <tr v-for="(user, idx) in pencariList" :key="user.id" class="hover:bg-slate-50/70 transition">
                <td class="py-4 px-4 text-center font-mono text-slate-400">{{ idx + 1 }}</td>
                <td class="py-4 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-xs shrink-0">
                      {{ getInitials(user.nama) }}
                    </div>
                    <div>
                      <div class="font-bold text-slate-900">{{ user.nama }}</div>
                      <div class="text-[11px] text-slate-400 font-mono">#USR-{{ user.id }} &bull; {{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4">
                  <div v-if="user.telepon" class="flex items-center gap-2">
                    <span class="font-mono text-slate-700 font-semibold">{{ user.telepon }}</span>
                    <button
                      @click="chatUserWhatsApp(user)"
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[10px] font-bold border border-emerald-200 transition"
                      title="Hubungi Pencari lewat WhatsApp"
                    >
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.053-1.018-.073-.615-.2-1.397-.577-2.316-1.493-.923-.92-1.294-1.696-1.496-2.314-.125-.385-.119-.705-.074-1.018.05-.333.418-1.026.823-1.17.135-.048.281-.035.405.029.135.07.247.202.321.36.197.42.502 1.155.539 1.246.037.091.042.186.005.281-.036.096-.073.185-.143.255-.07.07-.151.139-.214.195-.064.056-.128.117-.064.227.148.256.45 1.019 1.156 1.649.914.814 1.616.994 1.872 1.111.11.05.185.04.24-.037.056-.077.24-.287.327-.406.087-.119.176-.101.272-.065.096.036.83.392 1.25.59.42.197.552.31.6.446.048.136.048.421-.096.826z"/>
                      </svg>
                      Chat WA
                    </button>
                  </div>
                  <span v-else class="text-slate-400 italic text-[11px]">Belum diisi</span>
                </td>
                <td class="py-4 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 font-bold text-[11px]">
                    {{ user.pemesanan_count || 0 }} Pesanan
                  </span>
                </td>
                <td class="py-4 px-4 text-center">
                  <span
                    :class="[
                      'inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full',
                      user.aktif ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="user.aktif ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                    {{ user.aktif ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="py-4 px-4 text-slate-500 text-[11px]">{{ formatDate(user.created_at) }}</td>
                <td class="py-4 px-4 text-right pr-6 space-x-1.5 whitespace-nowrap">
                  <button
                    @click="viewUserDetail(user.id)"
                    class="px-2.5 py-1.5 text-[11px] font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    title="Lihat Detail Profil & Riwayat"
                  >
                    Detail
                  </button>
                  <button
                    @click="toggleUserActive(user)"
                    :disabled="togglingUserId === user.id"
                    :class="[
                      'px-2.5 py-1.5 text-[11px] font-bold rounded-xl border transition',
                      user.aktif
                        ? 'border-rose-200 text-rose-600 hover:bg-rose-50'
                        : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50'
                    ]"
                  >
                    {{ user.aktif ? 'Tangguhkan' : 'Aktifkan' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: KELOLA MITRA PEMILIK KOS (MODUL 3) -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'pemilik'" class="space-y-6">
      <!-- Search, Status Filter & Sorting Bar -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="relative w-full sm:w-80">
            <input
              type="text"
              v-model="pemilikSearchQuery"
              placeholder="Cari nama pemilik, email, atau nomor HP..."
              class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5"
              @input="debouncePemilikSearch"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <div class="flex items-center gap-2.5 w-full sm:w-auto flex-wrap sm:flex-nowrap">
            <select
              v-model="pemilikStatusFilter"
              @change="fetchPemilik"
              class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white font-medium text-slate-700"
            >
              <option value="all">Semua Status Kemitraan</option>
              <option value="1">Terverifikasi (Aktif)</option>
              <option value="0">Ditangguhkan (Nonaktif)</option>
            </select>

            <select
              v-model="pemilikSort"
              @change="fetchPemilik"
              class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white font-medium text-slate-700"
            >
              <option value="latest">Terbaru</option>
              <option value="unit_terbanyak">Properti Kos Terbanyak</option>
              <option value="nama_asc">Nama A-Z</option>
            </select>

            <button
              @click="fetchPemilik"
              class="p-2.5 text-slate-600 hover:bg-slate-100 rounded-xl border border-slate-200 transition"
              title="Segarkan Data Mitra Pemilik"
            >
              <svg class="w-4 h-4" :class="{ 'animate-spin': loadingPemilik }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Pemilik Table Card -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
              <tr>
                <th class="py-3.5 px-4 w-12 text-center">No</th>
                <th class="py-3.5 px-4">Mitra Pemilik Kos</th>
                <th class="py-3.5 px-4">Kontak WhatsApp</th>
                <th class="py-3.5 px-4 text-center">Unit Properti Kos</th>
                <th class="py-3.5 px-4 text-center">Status Kemitraan</th>
                <th class="py-3.5 px-4">Bergabung</th>
                <th class="py-3.5 px-4 text-right pr-6">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loadingPemilik">
                <td colspan="7" class="py-10 text-center text-slate-400">Memuat data mitra pemilik kos...</td>
              </tr>
              <tr v-else-if="pemilikList.length === 0">
                <td colspan="7" class="py-10 text-center text-slate-400">Tidak ada mitra pemilik kos yang ditemukan.</td>
              </tr>
              <tr v-for="(user, idx) in pemilikList" :key="user.id" class="hover:bg-slate-50/70 transition">
                <td class="py-4 px-4 text-center font-mono text-slate-400">{{ idx + 1 }}</td>
                <td class="py-4 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 font-extrabold flex items-center justify-center text-xs shrink-0">
                      {{ getInitials(user.nama) }}
                    </div>
                    <div>
                      <div class="font-bold text-slate-900">{{ user.nama }}</div>
                      <div class="text-[11px] text-slate-400 font-mono">#PMK-{{ user.id }} &bull; {{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4">
                  <div v-if="user.telepon" class="flex items-center gap-2">
                    <span class="font-mono text-slate-700 font-semibold">{{ user.telepon }}</span>
                    <button
                      @click="chatUserWhatsApp(user)"
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[10px] font-bold border border-emerald-200 transition"
                      title="Koordinasi dengan Mitra via WhatsApp"
                    >
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.053-1.018-.073-.615-.2-1.397-.577-2.316-1.493-.923-.92-1.294-1.696-1.496-2.314-.125-.385-.119-.705-.074-1.018.05-.333.418-1.026.823-1.17.135-.048.281-.035.405.029.135.07.247.202.321.36.197.42.502 1.155.539 1.246.037.091.042.186.005.281-.036.096-.073.185-.143.255-.07.07-.151.139-.214.195-.064.056-.128.117-.064.227.148.256.45 1.019 1.156 1.649.914.814 1.616.994 1.872 1.111.11.05.185.04.24-.037.056-.077.24-.287.327-.406.087-.119.176-.101.272-.065.096.036.83.392 1.25.59.42.197.552.31.6.446.048.136.048.421-.096.826z"/>
                      </svg>
                      Chat WA
                    </button>
                  </div>
                  <span v-else class="text-slate-400 italic text-[11px]">Belum diisi</span>
                </td>
                <td class="py-4 px-4 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ user.kos_count || 0 }} Properti
                  </span>
                </td>
                <td class="py-4 px-4 text-center">
                  <span
                    :class="[
                      'inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full',
                      user.aktif ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'
                    ]"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="user.aktif ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                    {{ user.aktif ? 'Terverifikasi' : 'Ditangguhkan' }}
                  </span>
                </td>
                <td class="py-4 px-4 text-slate-500 text-[11px]">{{ formatDate(user.created_at) }}</td>
                <td class="py-4 px-4 text-right pr-6 space-x-1.5 whitespace-nowrap">
                  <button
                    @click="viewUserDetail(user.id)"
                    class="px-2.5 py-1.5 text-[11px] font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    title="Lihat Detail Profil & Daftar Kos"
                  >
                    Detail
                  </button>
                  <button
                    @click="toggleUserActive(user)"
                    :disabled="togglingUserId === user.id"
                    :class="[
                      'px-2.5 py-1.5 text-[11px] font-bold rounded-xl border transition',
                      user.aktif
                        ? 'border-rose-200 text-rose-600 hover:bg-rose-50'
                        : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50'
                    ]"
                  >
                    {{ user.aktif ? 'Tangguhkan' : 'Aktifkan' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 4: DATA & MODERASI KOS (MODUL 4) -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'kos'" class="space-y-6">
      <!-- 4 Quick Metric Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Listing -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Listing Kos</p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ kosList.length }}</h4>
            <span class="inline-flex items-center text-emerald-600 text-[11px] font-bold mt-1">
              Terdaftar di Platform
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-[24px]">home_work</span>
          </div>
        </div>

        <!-- Kos Putri -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Khusus Putri</p>
            <h4 class="text-2xl font-black text-rose-600 mt-1">{{ kosPutriCount }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Kategori Penghuni Putri
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-[24px]">female</span>
          </div>
        </div>

        <!-- Kos Putra -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Khusus Putra</p>
            <h4 class="text-2xl font-black text-sky-600 mt-1">{{ kosPutraCount }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Kategori Penghuni Putra
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-[24px]">male</span>
          </div>
        </div>

        <!-- Kos Campur -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Campur / Pasutri</p>
            <h4 class="text-2xl font-black text-indigo-600 mt-1">{{ kosCampurCount }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Kategori Penghuni Campur
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-[24px]">group</span>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative flex-1 w-full">
          <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="kosSearchQuery"
            @input="debounceKosSearch"
            type="text"
            placeholder="Cari nama kos, alamat, atau nama/kontak pemilik..."
            class="w-full pl-10 pr-4 py-2 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 transition"
          />
        </div>

        <!-- Filter Controls -->
        <div class="flex items-center gap-2.5 w-full md:w-auto">
          <!-- Filter Jenis -->
          <select
            v-model="kosJenisFilter"
            @change="fetchKos"
            class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 font-semibold cursor-pointer"
          >
            <option value="all">Semua Jenis Kos</option>
            <option value="putri">Khusus Putri</option>
            <option value="putra">Khusus Putra</option>
            <option value="campur">Campur</option>
          </select>

          <!-- Filter Urutan -->
          <select
            v-model="kosSort"
            @change="fetchKos"
            class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 font-semibold cursor-pointer"
          >
            <option value="latest">Terbaru Didata</option>
            <option value="harga_asc">Tarif Termurah</option>
            <option value="harga_desc">Tarif Tertinggi</option>
            <option value="kamar_banyak">Kapasitas Terbanyak</option>
            <option value="nama_asc">Nama (A - Z)</option>
          </select>

          <!-- Reset Filter -->
          <button
            @click="resetKosFilters"
            title="Reset Filter"
            class="p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Main Content Layout (Table + Side Preview Panel) -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        <!-- Left Table Container -->
        <div class="xl:col-span-7 2xl:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
          <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <h3 class="font-bold text-sm text-slate-900">Daftar Seluruh Kos Nasional</h3>
              <span class="text-xs text-slate-400">({{ kosList.length }} listing)</span>
            </div>
            <p class="text-[11px] text-slate-400 hidden sm:block">Klik baris untuk pratinjau detail properti</p>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 border-collapse">
              <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <tr>
                  <th class="py-3.5 px-4 text-center w-12">No</th>
                  <th class="py-3.5 px-4 w-20">Foto</th>
                  <th class="py-3.5 px-4">Properti Kos</th>
                  <th class="py-3.5 px-4">Pemilik & Kontak</th>
                  <th class="py-3.5 px-4">Alamat</th>
                  <th class="py-3.5 px-4 text-center">Jenis</th>
                  <th class="py-3.5 px-4 text-center">Kamar</th>
                  <th class="py-3.5 px-4 text-right pr-4 w-32">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-if="loadingKos">
                  <td colspan="8" class="py-12 text-center text-slate-400">
                    <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Memuat data kos untuk moderasi...
                  </td>
                </tr>
                <tr v-else-if="kosList.length === 0">
                  <td colspan="8" class="py-12 text-center text-slate-400">
                    Tidak ada data properti kos yang sesuai dengan filter pencarian.
                  </td>
                </tr>
                <tr
                  v-for="(kos, idx) in kosList"
                  :key="kos.id"
                  @click="selectKosPreview(kos)"
                  :class="[
                    'hover:bg-slate-50/80 transition cursor-pointer',
                    selectedKosPreview?.id === kos.id ? 'bg-primary-fixed/20 border-l-4 border-l-primary' : ''
                  ]"
                >
                  <td class="py-3.5 px-4 text-center font-mono text-slate-400">{{ idx + 1 }}</td>
                  <td class="py-3.5 px-4">
                    <div class="w-14 h-11 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                      <img
                        :src="getKosThumbnail(kos)"
                        :alt="kos.nama"
                        class="w-full h-full object-cover"
                        loading="lazy"
                      />
                    </div>
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-900 group-hover:text-primary transition line-clamp-1">{{ kos.nama }}</div>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span class="text-[10px] font-mono text-slate-400">#KOS-{{ kos.id }}</span>
                      <span class="text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                        {{ formatRupiah(kos.harga) }}/bln
                      </span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4">
                    <div v-if="kos.pemilik" class="flex flex-col">
                      <span class="font-semibold text-slate-800 line-clamp-1">{{ kos.pemilik.nama }}</span>
                      <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] text-slate-400 font-mono">{{ kos.pemilik.telepon || '-' }}</span>
                        <button
                          v-if="kos.pemilik.telepon"
                          @click.stop="chatKosPemilik(kos)"
                          class="p-1 rounded text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition"
                          title="Chat Pemilik Kos via WhatsApp"
                        >
                          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.053-1.018-.073-.615-.2-1.397-.577-2.316-1.493-.923-.92-1.294-1.696-1.496-2.314-.125-.385-.119-.705-.074-1.018.05-.333.418-1.026.823-1.17.135-.048.281-.035.405.029.135.07.247.202.321.36.197.42.502 1.155.539 1.246.037.091.042.186.005.281-.036.096-.073.185-.143.255-.07.07-.151.139-.214.195-.064.056-.128.117-.064.227.148.256.45 1.019 1.156 1.649.914.814 1.616.994 1.872 1.111.11.05.185.04.24-.037.056-.077.24-.287.327-.406.087-.119.176-.101.272-.065.096.036.83.392 1.25.59.42.197.552.31.6.446.048.136.048.421-.096.826z"/>
                          </svg>
                        </button>
                      </div>
                    </div>
                    <span v-else class="text-slate-400 italic text-[11px]">-</span>
                  </td>
                  <td class="py-3.5 px-4 max-w-[180px]">
                    <span class="text-slate-600 line-clamp-1 text-[11px]" :title="kos.alamat">{{ kos.alamat }}</span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <span
                      :class="[
                        'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border capitalize',
                        kos.jenis === 'putri' ? 'bg-rose-50 text-rose-700 border-rose-200' :
                        kos.jenis === 'putra' ? 'bg-sky-50 text-sky-700 border-sky-200' :
                        'bg-indigo-50 text-indigo-700 border-indigo-200'
                      ]"
                    >
                      {{ kos.jenis }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 font-mono">
                      {{ kos.kamar_tersedia_count ?? 0 }} / {{ kos.kamar_count || (kos.kamar || []).length }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-right pr-4 space-x-1 whitespace-nowrap">
                    <button
                      @click.stop="selectKosPreview(kos)"
                      class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition"
                      title="Pratinjau Detail"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <router-link
                      :to="`/kos/${kos.id}`"
                      target="_blank"
                      class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition inline-block align-middle"
                      title="Buka Listing Publik"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </router-link>
                    <button
                      @click.stop="deleteKosModerasi(kos)"
                      :disabled="deletingKosId === kos.id"
                      class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition"
                      title="Hapus Moderasi Admin"
                    >
                      <svg class="w-4 h-4" :class="{ 'animate-spin': deletingKosId === kos.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Right Side Sticky Preview Panel -->
        <div class="xl:col-span-5 2xl:col-span-4 flex flex-col">
          <div
            v-if="selectedKosPreview"
            class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm sticky top-24 space-y-5"
          >
            <!-- Panel Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">domain_verification</span>
                <h3 class="font-bold text-sm text-slate-900">Pratinjau Listing Kos</h3>
              </div>
              <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                #KOS-{{ selectedKosPreview.id }}
              </span>
            </div>

            <!-- Hero Image with Gender Badge & Title Overlay -->
            <div class="relative w-full h-44 rounded-2xl overflow-hidden shadow-inner bg-slate-100 group">
              <img
                :src="getKosThumbnail(selectedKosPreview)"
                :alt="selectedKosPreview.nama"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent flex flex-col justify-end p-4">
                <span
                  :class="[
                    'inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white uppercase tracking-wider mb-1 w-max shadow-sm',
                    selectedKosPreview.jenis === 'putri' ? 'bg-rose-600' :
                    selectedKosPreview.jenis === 'putra' ? 'bg-sky-600' :
                    'bg-indigo-600'
                  ]"
                >
                  Khusus {{ selectedKosPreview.jenis }}
                </span>
                <h4 class="text-base font-bold text-white line-clamp-1 drop-shadow-sm">{{ selectedKosPreview.nama }}</h4>
              </div>
            </div>

            <!-- Price & Occupancy Metrics -->
            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-100">
              <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tarif Sewa</p>
                <p class="text-sm font-extrabold text-primary mt-0.5">
                  {{ formatRupiah(selectedKosPreview.harga) }}<span class="text-[10px] text-slate-400 font-normal">/bln</span>
                </p>
              </div>
              <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ketersediaan Kamar</p>
                <p class="text-sm font-extrabold text-emerald-600 mt-0.5">
                  {{ selectedKosPreview.kamar_tersedia_count ?? 0 }} <span class="text-[10px] text-slate-500 font-normal">/ {{ selectedKosPreview.kamar_count || (selectedKosPreview.kamar || []).length }} Tersedia</span>
                </p>
              </div>
            </div>

            <!-- Address & Location Details -->
            <div class="space-y-3 text-xs">
              <div class="flex items-start gap-2.5">
                <span class="material-symbols-outlined text-[18px] text-primary shrink-0 mt-0.5">pin_drop</span>
                <div>
                  <p class="font-bold text-slate-800 text-[11px]">Alamat Lengkap</p>
                  <p class="text-slate-500 leading-relaxed text-[11px] mt-0.5">{{ selectedKosPreview.alamat }}</p>
                </div>
              </div>

              <!-- Geocoding & GPS Coordinates -->
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-[10px] font-bold uppercase text-slate-400">Koordinat Geocoding</span>
                  <a
                    v-if="selectedKosPreview.latitude && selectedKosPreview.longitude"
                    :href="`https://www.google.com/maps/search/?api=1&query=${selectedKosPreview.latitude},${selectedKosPreview.longitude}`"
                    target="_blank"
                    class="text-[10px] font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1"
                  >
                    Buka Peta &rarr;
                  </a>
                </div>
                <div class="font-mono text-[11px] text-slate-700 flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Lat: {{ selectedKosPreview.latitude || '-' }} &bull; Lng: {{ selectedKosPreview.longitude || '-' }}
                </div>
              </div>

              <!-- Owner Information Card -->
              <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 block">Pemilik Properti</span>
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 font-extrabold flex items-center justify-center text-xs shrink-0">
                    {{ getInitials(selectedKosPreview.pemilik?.nama) }}
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-xs truncate">{{ selectedKosPreview.pemilik?.nama || 'Tanpa Pemilik' }}</p>
                    <p class="text-[10px] text-slate-400 font-mono truncate">{{ selectedKosPreview.pemilik?.email || '-' }}</p>
                  </div>
                </div>

                <div v-if="selectedKosPreview.pemilik?.telepon" class="pt-1">
                  <button
                    @click="chatKosPemilik(selectedKosPreview)"
                    class="w-full py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm"
                  >
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.053-1.018-.073-.615-.2-1.397-.577-2.316-1.493-.923-.92-1.294-1.696-1.496-2.314-.125-.385-.119-.705-.074-1.018.05-.333.418-1.026.823-1.17.135-.048.281-.035.405.029.135.07.247.202.321.36.197.42.502 1.155.539 1.246.037.091.042.186.005.281-.036.096-.073.185-.143.255-.07.07-.151.139-.214.195-.064.056-.128.117-.064.227.148.256.45 1.019 1.156 1.649.914.814 1.616.994 1.872 1.111.11.05.185.04.24-.037.056-.077.24-.287.327-.406.087-.119.176-.101.272-.065.096.036.83.392 1.25.59.42.197.552.31.6.446.048.136.048.421-.096.826z"/>
                    </svg>
                    Chat Pemilik via WhatsApp
                  </button>
                </div>
              </div>

              <!-- Facilities Badges -->
              <div v-if="(selectedKosPreview.fasilitas || []).length > 0">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1.5">Fasilitas Terdaftar</span>
                <div class="flex flex-wrap gap-1.5">
                  <span
                    v-for="fas in selectedKosPreview.fasilitas"
                    :key="fas.id"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-semibold"
                  >
                    <span class="material-symbols-outlined text-[13px] text-primary">check_small</span>
                    {{ fas.nama }}
                  </span>
                </div>
              </div>

              <!-- Description -->
              <div v-if="selectedKosPreview.deskripsi" class="pt-1">
                <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Deskripsi Properti</span>
                <p class="text-[11px] text-slate-500 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                  {{ selectedKosPreview.deskripsi }}
                </p>
              </div>
            </div>

            <!-- Actions Footer -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
              <router-link
                :to="`/kos/${selectedKosPreview.id}`"
                target="_blank"
                class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm text-center"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Listing Publik
              </router-link>

              <button
                @click="deleteKosModerasi(selectedKosPreview)"
                :disabled="deletingKosId === selectedKosPreview.id"
                class="w-full py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center justify-center gap-1.5"
              >
                <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': deletingKosId === selectedKosPreview.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Moderasi
              </button>
            </div>
          </div>

          <!-- Empty Preview Placeholder -->
          <div
            v-else
            class="bg-white rounded-3xl border border-dashed border-slate-200 p-8 text-center sticky top-24 flex flex-col items-center justify-center text-slate-400 min-h-[350px]"
          >
            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 mb-3">
              <span class="material-symbols-outlined text-[28px]">home_work</span>
            </div>
            <h4 class="font-bold text-sm text-slate-700 mb-1">Pratinjau Kos</h4>
            <p class="text-xs text-slate-400 max-w-xs leading-relaxed">
              Pilih salah satu properti kos pada tabel di samping untuk melihat rincian pratinjau, fasilitas, koordinat peta, dan kontak pemilik.
            </p>
          </div>
        </div>
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

    <!-- ================================================================= -->
    <!-- MODAL: DETAIL PENGGUNA (PENCARI / MITRA PEMILIK) -->
    <!-- ================================================================= -->
    <div
      v-if="showUserDetailModal"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <div class="flex items-center gap-3">
            <div
              :class="[
                'w-12 h-12 rounded-2xl flex items-center justify-center font-black text-base',
                selectedUserDetail?.role === 'pemilik' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'
              ]"
            >
              {{ getInitials(selectedUserDetail?.nama) }}
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-bold text-slate-900">{{ selectedUserDetail?.nama }}</h3>
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full border',
                    selectedUserDetail?.role === 'pemilik' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                  ]"
                >
                  {{ selectedUserDetail?.role === 'pemilik' ? 'Mitra Pemilik' : 'Pencari Kos' }}
                </span>
                <span
                  :class="[
                    'text-[10px] font-extrabold px-2 py-0.5 rounded-full',
                    selectedUserDetail?.aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'
                  ]"
                >
                  {{ selectedUserDetail?.aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
              </div>
              <p class="text-xs text-slate-400 mt-0.5 font-mono">
                ID: {{ selectedUserDetail?.role === 'pemilik' ? '#PMK-' : '#USR-' }}{{ selectedUserDetail?.id }} &bull; Bergabung: {{ formatDate(selectedUserDetail?.created_at) }}
              </p>
            </div>
          </div>
          <button @click="showUserDetailModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
        </div>

        <div v-if="loadingUserDetail" class="py-12 text-center text-slate-400">
          <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Memuat rincian informasi pengguna...
        </div>

        <div v-else class="space-y-6">
          <!-- Contact & Info Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Email Terdaftar</span>
              <span class="text-xs font-semibold text-slate-800">{{ selectedUserDetail?.email }}</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kontak WhatsApp</span>
                <span class="text-xs font-mono font-bold text-slate-800">{{ selectedUserDetail?.telepon || 'Belum diisi' }}</span>
              </div>
              <button
                v-if="selectedUserDetail?.telepon"
                @click="chatUserWhatsApp(selectedUserDetail)"
                class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 shadow-sm"
              >
                Chat WA
              </button>
            </div>
          </div>

          <!-- Section: If Pemilik Kos -> Daftar Properti Kos -->
          <div v-if="selectedUserDetail?.role === 'pemilik'">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Daftar Properti Kos ({{ (selectedUserDetail?.daftar_kos || []).length }} Properti)
              </h4>
            </div>
            <div v-if="(selectedUserDetail?.daftar_kos || []).length === 0" class="p-4 rounded-2xl bg-slate-50 text-center text-xs text-slate-400">
              Mitra ini belum mendaftarkan properti kos.
            </div>
            <div v-else class="space-y-2 max-h-56 overflow-y-auto pr-1">
              <div
                v-for="kos in selectedUserDetail?.daftar_kos"
                :key="kos.id"
                class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between"
              >
                <div>
                  <div class="font-bold text-xs text-slate-900">{{ kos.nama }}</div>
                  <div class="text-[11px] text-slate-500">{{ kos.alamat }}</div>
                  <div class="text-[10px] text-emerald-700 font-bold mt-0.5">
                    {{ kos.kamar_count || 0 }} Kamar &bull; {{ formatRupiah(kos.harga) }}/bln
                  </div>
                </div>
                <router-link
                  :to="`/kos/${kos.id}`"
                  target="_blank"
                  class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 px-3 py-1 bg-white rounded-xl border border-slate-200 transition"
                >
                  Lihat Kos &rarr;
                </router-link>
              </div>
            </div>
          </div>

          <!-- Section: If Pencari Kos -> Riwayat Reservasi -->
          <div v-else-if="selectedUserDetail?.role === 'pencari'">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Riwayat Transaksi Pemesanan ({{ (selectedUserDetail?.riwayat_pemesanan || []).length }})
              </h4>
            </div>
            <div v-if="(selectedUserDetail?.riwayat_pemesanan || []).length === 0" class="p-4 rounded-2xl bg-slate-50 text-center text-xs text-slate-400">
              Belum ada riwayat transaksi pemesanan untuk pencari ini.
            </div>
            <div v-else class="space-y-2 max-h-56 overflow-y-auto pr-1">
              <div
                v-for="pesan in selectedUserDetail?.riwayat_pemesanan"
                :key="pesan.id"
                class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between"
              >
                <div>
                  <div class="font-bold text-xs text-slate-900">{{ pesan.kos?.nama || 'Properti Kos' }}</div>
                  <div class="text-[11px] text-slate-500">
                    Kamar: {{ pesan.kamar?.nama_kamar || '-' }} &bull; Tanggal: {{ formatDateOnly(pesan.tanggal_pemesanan) }}
                  </div>
                </div>
                <div class="text-right">
                  <span
                    :class="[
                      'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border block mb-1',
                      statusBadgeClass(pesan.status)
                    ]"
                  >
                    {{ pesan.status }}
                  </span>
                  <span class="text-[11px] font-mono font-bold text-slate-700">
                    {{ pesan.kamar?.harga ? formatRupiah(pesan.kamar.harga) : '-' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Action Footer -->
          <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <button
              @click="toggleUserActive(selectedUserDetail)"
              :class="[
                'px-4 py-2 text-xs font-bold rounded-xl border transition',
                selectedUserDetail?.aktif
                  ? 'border-rose-200 text-rose-600 hover:bg-rose-50'
                  : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50'
              ]"
            >
              {{ selectedUserDetail?.aktif ? 'Tangguhkan Akun Ini' : 'Aktifkan Akun Ini' }}
            </button>
            <button
              type="button"
              @click="showUserDetailModal = false"
              class="px-5 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import { openWhatsAppChat } from '../utils/whatsapp'

const route = useRoute()
const router = useRouter()

const initialTab = ['ringkasan', 'pencari', 'pemilik', 'kos', 'fasilitas', 'pemesanan'].includes(route.query.tab)
  ? route.query.tab
  : 'ringkasan'
const activeTab = ref(initialTab)

const switchTab = (tabName) => {
  activeTab.value = tabName
  router.replace({ query: { ...route.query, tab: tabName } })
}

watch(
  () => route.query.tab,
  (newTab) => {
    if (newTab && ['ringkasan', 'pencari', 'pemilik', 'kos', 'fasilitas', 'pemesanan'].includes(newTab)) {
      activeTab.value = newTab
    }
  }
)

const stats = ref({})
const loadingStats = ref(false)

const pencariList = ref([])
const loadingPencari = ref(false)
const pencariSearchQuery = ref('')
const pencariStatusFilter = ref('all')

const pemilikList = ref([])
const loadingPemilik = ref(false)
const pemilikSearchQuery = ref('')
const pemilikStatusFilter = ref('all')
const pemilikSort = ref('latest')

const kosList = ref([])
const loadingKos = ref(false)
const kosSearchQuery = ref('')
const kosJenisFilter = ref('all')
const kosSort = ref('latest')
const selectedKosPreview = ref(null)
const deletingKosId = ref(null)

const kosPutriCount = computed(() => kosList.value.filter(k => k.jenis === 'putri').length)
const kosPutraCount = computed(() => kosList.value.filter(k => k.jenis === 'putra').length)
const kosCampurCount = computed(() => kosList.value.filter(k => k.jenis === 'campur').length)

const defaultKosImages = [
  'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80',
  'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=600&q=80',
  'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=600&q=80',
  'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=600&q=80',
]

const getKosThumbnail = (kos) => {
  if (!kos) return defaultKosImages[0]
  if (kos.foto_url) return kos.foto_url
  const idx = (kos.id || 0) % defaultKosImages.length
  return defaultKosImages[idx]
}

const fasilitasList = ref([])
const allBookings = ref([])

const loadingFasilitas = ref(false)
const loadingBookings = ref(false)
const togglingUserId = ref(null)

const showUserDetailModal = ref(false)
const loadingUserDetail = ref(false)
const selectedUserDetail = ref(null)

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

let pencariSearchTimeout = null
const debouncePencariSearch = () => {
  clearTimeout(pencariSearchTimeout)
  pencariSearchTimeout = setTimeout(() => {
    fetchPencari()
  }, 350)
}

const fetchPencari = async () => {
  loadingPencari.value = true
  try {
    const params = { role: 'pencari' }
    if (pencariSearchQuery.value) params.q = pencariSearchQuery.value
    if (pencariStatusFilter.value !== 'all') params.aktif = pencariStatusFilter.value

    const res = await api.get('/admin/pengguna', { params })
    pencariList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat daftar pencari kos.'
  } finally {
    loadingPencari.value = false
  }
}

let pemilikSearchTimeout = null
const debouncePemilikSearch = () => {
  clearTimeout(pemilikSearchTimeout)
  pemilikSearchTimeout = setTimeout(() => {
    fetchPemilik()
  }, 350)
}

const fetchPemilik = async () => {
  loadingPemilik.value = true
  try {
    const params = { role: 'pemilik' }
    if (pemilikSearchQuery.value) params.q = pemilikSearchQuery.value
    if (pemilikStatusFilter.value !== 'all') params.aktif = pemilikStatusFilter.value
    if (pemilikSort.value) params.sort = pemilikSort.value

    const res = await api.get('/admin/pengguna', { params })
    pemilikList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat daftar mitra pemilik kos.'
  } finally {
    loadingPemilik.value = false
  }
}

const viewUserDetail = async (userId) => {
  loadingUserDetail.value = true
  showUserDetailModal.value = true
  try {
    const res = await api.get(`/admin/pengguna/${userId}`)
    selectedUserDetail.value = res.data.data || null
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat rincian informasi pengguna.'
    showUserDetailModal.value = false
  } finally {
    loadingUserDetail.value = false
  }
}

const toggleUserActive = async (user) => {
  if (!user) return
  togglingUserId.value = user.id
  try {
    const nextStatus = !user.aktif
    await api.patch(`/admin/pengguna/${user.id}/status`, { aktif: nextStatus })
    user.aktif = nextStatus
    if (selectedUserDetail.value && selectedUserDetail.value.id === user.id) {
      selectedUserDetail.value.aktif = nextStatus
    }
    successMsg.value = `Status akun ${user.nama} berhasil diperbarui.`
    fetchStats()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memperbarui status akun.'
  } finally {
    togglingUserId.value = null
  }
}

const chatUserWhatsApp = (user) => {
  if (!user) return
  const phone = user.telepon || user.no_hp
  if (!phone) {
    alert('Pengguna ini belum melengkapi nomor telepon / WhatsApp.')
    return
  }

  const message =
    user.role === 'pemilik'
      ? `Halo Bapak/Ibu Mitra KosKu ${user.nama},\n\nKami dari Manajemen Layanan KosKu ingin berkoordinasi mengenai operasional dan data properti kos Anda di platform KosKu.\n\nTerima kasih!`
      : `Halo Kak ${user.nama},\n\nKami dari tim Layanan Pelanggan KosKu ingin mengonfirmasi terkait akun dan aktivitas pemesanan sewa Anda di KosKu. Apakah ada hal yang membutuhkan bantuan kami?\n\nTerima kasih!`

  openWhatsAppChat(phone, message)
}

let kosSearchTimeout = null
const debounceKosSearch = () => {
  clearTimeout(kosSearchTimeout)
  kosSearchTimeout = setTimeout(() => {
    fetchKos()
  }, 350)
}

const resetKosFilters = () => {
  kosSearchQuery.value = ''
  kosJenisFilter.value = 'all'
  kosSort.value = 'latest'
  fetchKos()
}

const fetchKos = async () => {
  loadingKos.value = true
  try {
    const params = {}
    if (kosSearchQuery.value) params.q = kosSearchQuery.value
    if (kosJenisFilter.value !== 'all') params.jenis = kosJenisFilter.value
    if (kosSort.value) params.sort = kosSort.value

    const res = await api.get('/admin/kos', { params })
    kosList.value = res.data.data || []

    if (kosList.value.length > 0) {
      if (!selectedKosPreview.value || !kosList.value.some(k => k.id === selectedKosPreview.value.id)) {
        selectedKosPreview.value = kosList.value[0]
      }
    } else {
      selectedKosPreview.value = null
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat data kos untuk moderasi.'
  } finally {
    loadingKos.value = false
  }
}

const selectKosPreview = (kos) => {
  selectedKosPreview.value = kos
}

const deleteKosModerasi = async (kos) => {
  if (!kos) return
  const confirmDelete = window.confirm(
    `Konfirmasi Moderasi Admin:\n\nApakah Anda yakin ingin MENGHAPUS properti "${kos.nama}" (#KOS-${kos.id})?\n\nTindakan ini bersifat permanen dan akan menghapus seluruh data kamar, fasilitas terkait, dan riwayat pesanan kos ini.`
  )
  if (!confirmDelete) return

  deletingKosId.value = kos.id
  try {
    const res = await api.delete(`/admin/kos/${kos.id}`)
    successMsg.value = res.data?.message || `Properti "${kos.nama}" berhasil dihapus dari sistem.`
    if (selectedKosPreview.value?.id === kos.id) {
      selectedKosPreview.value = null
    }
    await fetchKos()
    fetchStats()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menghapus properti kos.'
  } finally {
    deletingKosId.value = null
  }
}

const chatKosPemilik = (kos) => {
  if (!kos || !kos.pemilik) return
  const phone = kos.pemilik.telepon || kos.pemilik.no_hp
  if (!phone) {
    alert(`Mitra pemilik kos "${kos.nama}" belum mencantumkan nomor telepon / WhatsApp.`)
    return
  }

  const message = `Halo Bapak/Ibu Mitra KosKu ${kos.pemilik.nama},\n\nKami dari tim Moderasi & Administrator KosKu ingin berkoordinasi mengenai listing properti Anda: "${kos.nama}" (ID: #KOS-${kos.id}) yang berlokasi di ${kos.alamat}.\n\nMohon konfirmasi ketersediaan dan status operasional terbaru kos Anda.\n\nTerima kasih!`
  openWhatsAppChat(phone, message)
}

const getInitials = (name) => {
  if (!name) return 'U'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return name.slice(0, 2).toUpperCase()
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
  fetchPencari()
  fetchPemilik()
  fetchKos()
  fetchFasilitas()
  fetchAllBookings()
})
</script>
