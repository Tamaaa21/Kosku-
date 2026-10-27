<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
    <!-- Context Subheader & Quick Action Toolbar (Shown on other tabs) -->
    <div v-if="activeTab !== 'ringkasan'" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-xs shrink-0">
          <component :is="activeTabMeta.icon" :size="20" weight="bold" />
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ activeTabMeta.title }}</h2>
            <span v-if="activeTabMeta.badge !== null" class="bg-slate-100 text-slate-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-slate-200">
              {{ activeTabMeta.badge }}
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            {{ activeTabMeta.description }}
          </p>
        </div>
      </div>

      <!-- Action Toolbar (Dynamic per active tab) -->
      <div class="flex items-center gap-2.5">
        <button
          v-if="activeTab === 'ringkasan'"
          @click="fetchStats"
          :disabled="loadingStats"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loadingStats }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data
        </button>

        <button
          v-if="activeTab === 'pengguna'"
          @click="fetchUsers"
          :disabled="loadingUsers"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loadingUsers }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Pengguna
        </button>

        <button
          v-if="activeTab === 'verifikasi'"
          @click="fetchVerifications"
          :disabled="loadingVerifications"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loadingVerifications }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data Verifikasi
        </button>

        <button
          v-if="activeTab === 'kos'"
          @click="fetchKos"
          :disabled="loadingKos"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loadingKos }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data Kos
        </button>

        <button
          v-if="activeTab === 'pemesanan'"
          @click="exportBookingsToCSV"
          :disabled="allBookings.length === 0"
          class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <PhTable :size="16" class="text-emerald-600" />
          Ekspor CSV
        </button>
        <button
          v-if="activeTab === 'pemesanan'"
          @click="fetchAllBookings"
          :disabled="loadingBookings"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loadingBookings }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data
        </button>

        <button
          v-if="activeTab === 'banner' || activeTab === 'hero-slider'"
          @click="openAddBannerModal"
          class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-container text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm active:scale-95"
        >
          <PhPlus :size="16" weight="bold" />
          <span>Tambah Banner Slider</span>
        </button>
        <button
          v-if="activeTab === 'banner' || activeTab === 'hero-slider'"
          @click="fetchAdminBanners"
          :disabled="loadingBanners"
          class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition border border-slate-200 shadow-sm disabled:opacity-50"
        >
          <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': loadingBanners }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Segarkan Data
        </button>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div v-if="successMsg" class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-medium border border-emerald-200 flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = null" class="text-emerald-700 font-bold ml-4 text-base">&times;</button>
    </div>
    <div v-if="errorMsg" class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 text-xs font-medium border border-rose-200 flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = null" class="text-rose-700 font-bold ml-4 text-base">&times;</button>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 0: RINGKASAN & ANALITIK (SESUAI public/ui-dashboard-admin.png) -->
    <!-- ================================================================= -->
    <div v-if="activeTab === 'ringkasan'" class="space-y-6 sm:space-y-7">
      <!-- 1. Hero Welcome Banner -->
      <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-50/90 via-indigo-50/50 to-blue-50/40 border border-blue-100/80 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-2xs"
      >
        <!-- Background Houses Vector Illustration -->
        <div class="hidden lg:block absolute right-44 top-0 bottom-0 w-[420px] pointer-events-none opacity-95 overflow-hidden select-none">
          <svg class="w-full h-full object-cover" viewBox="0 0 420 180" fill="none" preserveAspectRatio="xMidYMid meet">
            <!-- Soft Hills & Bushes -->
            <ellipse cx="280" cy="180" rx="200" ry="60" fill="#93C5FD" fill-opacity="0.25" />
            <ellipse cx="140" cy="185" rx="160" ry="55" fill="#6EE7B7" fill-opacity="0.2" />
            <ellipse cx="360" cy="190" rx="140" ry="50" fill="#60A5FA" fill-opacity="0.2" />

            <!-- Background Trees -->
            <circle cx="90" cy="140" r="18" fill="#3B82F6" fill-opacity="0.7" />
            <circle cx="105" cy="145" r="14" fill="#60A5FA" fill-opacity="0.8" />
            <circle cx="340" cy="135" r="22" fill="#10B981" fill-opacity="0.6" />
            <circle cx="365" cy="142" r="16" fill="#34D399" fill-opacity="0.7" />

            <!-- Main Center Boarding House (Modern Kos Building) -->
            <rect x="180" y="70" width="110" height="95" rx="4" fill="#FFFFFF" stroke="#93C5FD" stroke-width="2" />
            <!-- Roof -->
            <path d="M170 75 L235 25 L300 75 Z" fill="#2563EB" />
            <path d="M190 75 L235 38 L280 75 Z" fill="#1D4ED8" />

            <!-- Balcony & Windows -->
            <rect x="195" y="85" width="22" height="26" rx="3" fill="#DBEAFE" stroke="#60A5FA" stroke-width="1.5" />
            <line x1="206" y1="85" x2="206" y2="111" stroke="#60A5FA" stroke-width="1.5" />
            <rect x="253" y="85" width="22" height="26" rx="3" fill="#DBEAFE" stroke="#60A5FA" stroke-width="1.5" />
            <line x1="264" y1="85" x2="264" y2="111" stroke="#60A5FA" stroke-width="1.5" />

            <!-- Entrance Door & Steps -->
            <rect x="223" y="125" width="24" height="40" rx="3" fill="#1E40AF" />
            <circle cx="241" cy="146" r="2" fill="#FCD34D" />
            <rect x="217" y="163" width="36" height="3" rx="1.5" fill="#CBD5E1" />

            <!-- Left Secondary Building -->
            <rect x="120" y="95" width="70" height="70" rx="3" fill="#F8FAFC" stroke="#93C5FD" stroke-width="2" />
            <path d="M115 98 L155 60 L195 98 Z" fill="#3B82F6" />
            <rect x="135" y="112" width="18" height="20" rx="2" fill="#E0F2FE" stroke="#60A5FA" stroke-width="1" />
            <rect x="162" y="112" width="18" height="20" rx="2" fill="#E0F2FE" stroke="#60A5FA" stroke-width="1" />

            <!-- Right Secondary Building -->
            <rect x="280" y="105" width="60" height="60" rx="3" fill="#F8FAFC" stroke="#93C5FD" stroke-width="2" />
            <path d="M275 108 L310 75 L345 108 Z" fill="#60A5FA" />
            <rect x="295" y="120" width="16" height="18" rx="2" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />

            <!-- Foreground Shrubs -->
            <circle cx="115" cy="165" r="14" fill="#10B981" />
            <circle cx="280" cy="168" r="16" fill="#059669" />
            <circle cx="345" cy="166" r="15" fill="#34D399" />
          </svg>
        </div>

        <!-- Left Text Content -->
        <div class="relative z-10 max-w-xl">
          <div class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100/90 text-blue-700 mb-2.5">
            Dashboard Administrator
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <span>Selamat datang, Admin!</span>
            <span class="inline-block animate-wave origin-bottom-right">👋</span>
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
            Berikut ringkasan aktivitas dan performa platform Kostara hari ini.
          </p>
        </div>

        <!-- Right Action Button: Segarkan Data -->
        <div class="relative z-10 shrink-0 flex items-center">
          <button
            @click="fetchStats"
            :disabled="loadingStats"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200/90 shadow-xs hover:shadow transition active:scale-95 cursor-pointer disabled:opacity-50"
          >
            <svg class="w-4 h-4 text-slate-500" :class="{ 'animate-spin': loadingStats }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Segarkan Data</span>
          </button>
        </div>
      </div>

      <!-- 2. Row 1: 4 KPI Cards Grid -->
      <div v-if="loadingStats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 animate-pulse">
        <div v-for="i in 4" :key="i" class="h-32 bg-slate-100 rounded-3xl"></div>
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Card 1: Total Pengguna -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition">
          <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
              <PhUsers :size="22" weight="bold" />
            </div>
            <span class="text-xs font-bold text-slate-500">Total Pengguna</span>
          </div>
          <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-black text-slate-900 tracking-tight">
              {{ stats.total_pengguna ?? 0 }}
            </span>
            <span
              class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border"
              :class="stats.growth_pengguna?.is_positive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-rose-600 bg-rose-50 border-rose-200'"
            >
              <component :is="stats.growth_pengguna?.is_positive ? PhArrowUp : PhArrowDown" :size="12" weight="bold" />
              {{ stats.growth_pengguna?.formatted || '+0%' }}
            </span>
          </div>
          <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
            <span><strong>{{ stats.total_pencari ?? 0 }}</strong> Pencari</span>
            <span>&bull;</span>
            <span><strong>{{ stats.total_pemilik ?? 0 }}</strong> Pemilik</span>
          </div>
        </div>

        <!-- Card 2: Properti Kos -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition">
          <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
              <PhBuildings :size="22" weight="bold" />
            </div>
            <span class="text-xs font-bold text-slate-500">Properti Kos</span>
          </div>
          <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-black text-slate-900 tracking-tight">
              {{ stats.total_kos ?? 0 }}
            </span>
            <span
              class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border"
              :class="stats.growth_kos?.is_positive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-rose-600 bg-rose-50 border-rose-200'"
            >
              <component :is="stats.growth_kos?.is_positive ? PhArrowUp : PhArrowDown" :size="12" weight="bold" />
              {{ stats.growth_kos?.formatted || '+0%' }}
            </span>
          </div>
          <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
            <span><strong>{{ stats.kos_aktif ?? 0 }}</strong> Aktif</span>
            <span>&bull;</span>
            <span><strong>{{ stats.kos_menunggu ?? 0 }}</strong> Menunggu</span>
          </div>
        </div>

        <!-- Card 3: Reservasi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition">
          <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <PhCalendarCheck :size="22" weight="bold" />
            </div>
            <span class="text-xs font-bold text-slate-500">Reservasi</span>
          </div>
          <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-black text-slate-900 tracking-tight">
              {{ stats.total_pemesanan ?? 0 }}
            </span>
            <span
              class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border"
              :class="stats.growth_pemesanan?.is_positive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-rose-600 bg-rose-50 border-rose-200'"
            >
              <component :is="stats.growth_pemesanan?.is_positive ? PhArrowUp : PhArrowDown" :size="12" weight="bold" />
              {{ stats.growth_pemesanan?.formatted || '+0%' }}
            </span>
          </div>
          <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
            <span><strong>{{ stats.pemesanan_menunggu ?? 0 }}</strong> Menunggu</span>
            <span>&bull;</span>
            <span><strong>{{ stats.pemesanan_disetujui ?? 0 }}</strong> Setuju</span>
            <span>&bull;</span>
            <span><strong>{{ stats.pemesanan_ditolak ?? 0 }}</strong> Tolak</span>
          </div>
        </div>

        <!-- Card 4: Estimasi GMV (Sewa) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs hover:shadow-md transition">
          <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
              <PhCurrencyCircleDollar :size="22" weight="bold" />
            </div>
            <span class="text-xs font-bold text-slate-500">Estimasi GMV (Sewa)</span>
          </div>
          <div class="flex items-baseline justify-between mt-2">
            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
              {{ formatRupiah(stats.total_nilai_transaksi ?? 0) }}
            </span>
            <span
              class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border"
              :class="stats.growth_gmv?.is_positive ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-rose-600 bg-rose-50 border-rose-200'"
            >
              <component :is="stats.growth_gmv?.is_positive ? PhArrowUp : PhArrowDown" :size="12" weight="bold" />
              {{ stats.growth_gmv?.formatted || '+0%' }}
            </span>
          </div>
          <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-400">
            Transaksi {{ stats.current_period_name || 'Bulan Ini' }}
          </div>
        </div>
      </div>

      <!-- 3. Row 2: Tren Pemesanan Disetujui (Full Width - Single Clean Metric) -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <div>
            <h3 class="text-base font-bold text-slate-900">Tren Pemesanan Disetujui</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pertumbuhan transaksi reservasi kamar kos yang berhasil disetujui ({{ stats.range_label || '7 hari' }})</p>
          </div>
          <div class="flex items-center gap-3 sm:gap-4">
            <!-- Single Legend Pill -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 bg-emerald-50 border border-emerald-200/80 px-3 py-1.5 rounded-full">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              <span>Pemesanan Disetujui</span>
            </div>

            <!-- Filter Date Range Pill Dropdown -->
            <div class="relative">
              <button
                @click="showRangeDropdown = !showRangeDropdown"
                type="button"
                class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 select-none cursor-pointer transition"
              >
                <PhCalendarBlank :size="14" class="text-slate-500" />
                <span>{{ stats.range_label || 'Periode Aktif' }}</span>
                <PhCaretDown :size="12" class="text-slate-400" />
              </button>

              <div
                v-if="showRangeDropdown"
                class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-20 overflow-hidden"
              >
                <button
                  @click="selectTrendRange('auto')"
                  class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 flex items-center justify-between cursor-pointer"
                  :class="{ 'text-primary font-bold bg-blue-50/50': currentTrendRange === 'auto' }"
                >
                  <span>Periode Transaksi Aktif</span>
                  <PhCheck v-if="currentTrendRange === 'auto'" :size="14" />
                </button>
                <button
                  @click="selectTrendRange('7d')"
                  class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-slate-50 flex items-center justify-between cursor-pointer"
                  :class="{ 'text-primary font-bold bg-blue-50/50': currentTrendRange === '7d' }"
                >
                  <span>7 Hari Terakhir (Hari Ini)</span>
                  <PhCheck v-if="currentTrendRange === '7d'" :size="14" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- SVG Single Bar Chart -->
        <div class="relative w-full h-56 pt-2 select-none">
          <svg class="w-full h-full overflow-visible" viewBox="0 0 700 170" preserveAspectRatio="none">
            <!-- Defs for Gradient -->
            <defs>
              <linearGradient id="approvedBarGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10B981" />
                <stop offset="100%" stop-color="#059669" />
              </linearGradient>
            </defs>

            <!-- Y-Axis Grid Lines & Labels (Dynamic Ticks based on maxDisetujuiVal) -->
            <g class="text-[10px] fill-slate-400 font-medium">
              <template v-for="(tick, tIdx) in yAxisTicks" :key="tIdx">
                <text x="20" :y="tick.y" text-anchor="end">{{ tick.val }}</text>
                <line
                  x1="30"
                  :y1="tick.lineY"
                  x2="690"
                  :y2="tick.lineY"
                  :stroke="tIdx === yAxisTicks.length - 1 ? '#e2e8f0' : '#f1f5f9'"
                  :stroke-width="1"
                  :stroke-dasharray="tIdx === yAxisTicks.length - 1 ? undefined : '3'"
                />
              </template>
            </g>

            <!-- Dynamic Bars for "Pemesanan Disetujui" -->
            <g v-for="(day, idx) in bookingTrends7Days" :key="idx">
              <!-- Single Bar (Emerald Gradient) -->
              <rect
                :x="idx * 90 + 65"
                :y="154 - (day.disetujui / maxDisetujuiVal) * 142"
                width="36"
                :height="Math.max((day.disetujui / maxDisetujuiVal) * 142, day.disetujui > 0 ? 6 : 2)"
                rx="6"
                fill="url(#approvedBarGrad)"
                class="transition-all duration-300 hover:brightness-110 cursor-pointer"
              />
              <!-- Value Label on top of Bar -->
              <text
                :x="idx * 90 + 83"
                :y="154 - (day.disetujui / maxDisetujuiVal) * 142 - 6"
                text-anchor="middle"
                class="text-xs font-bold fill-slate-800"
              >
                {{ day.disetujui }}
              </text>

              <!-- Day Label below baseline -->
              <text
                :x="idx * 90 + 83"
                y="169"
                text-anchor="middle"
                class="text-xs font-semibold fill-slate-400"
              >
                {{ day.label }}
              </text>
            </g>
          </svg>
        </div>
      </div>

      <!-- 4. Row 3: Bottom Row (2 Equal Columns: Distribusi Jenis Kos & Tren Waktu Sewa DONUT) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        <!-- Card 1: Distribusi Jenis Kos (Donut Chart) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-1">
              <h3 class="text-base font-bold text-slate-900">Distribusi Jenis Kos</h3>
              <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">Kategori</span>
            </div>
            <p class="text-xs text-slate-400 mb-6">Proporsi hunian kos berdasarkan segmentasi gender penghuni</p>

            <div class="flex flex-col sm:flex-row items-center justify-around gap-6 py-2">
              <!-- Donut Chart -->
              <div class="relative w-36 h-36 shrink-0">
                <svg class="w-full h-full transform -rotate-90 select-none" viewBox="0 0 140 140">
                  <!-- Background Track -->
                  <circle cx="70" cy="70" r="50" fill="none" stroke="#f1f5f9" stroke-width="16" />

                  <!-- Putri Segment (Pink #F43F5E) -->
                  <circle
                    cx="70"
                    cy="70"
                    r="50"
                    fill="none"
                    stroke="#F43F5E"
                    stroke-width="16"
                    stroke-linecap="round"
                    :stroke-dasharray="`${(kosJenisStats.putriPct / 100) * 314.16} 314.16`"
                    stroke-dashoffset="0"
                    class="transition-all duration-700"
                  />

                  <!-- Putra Segment (Cyan #0EA5E9) -->
                  <circle
                    cx="70"
                    cy="70"
                    r="50"
                    fill="none"
                    stroke="#0EA5E9"
                    stroke-width="16"
                    stroke-linecap="round"
                    :stroke-dasharray="`${(kosJenisStats.putraPct / 100) * 314.16} 314.16`"
                    :stroke-dashoffset="`-${(kosJenisStats.putriPct / 100) * 314.16}`"
                    class="transition-all duration-700"
                  />

                  <!-- Campur Segment (Purple #8B5CF6) -->
                  <circle
                    v-if="kosJenisStats.campurPct > 0"
                    cx="70"
                    cy="70"
                    r="50"
                    fill="none"
                    stroke="#8B5CF6"
                    stroke-width="16"
                    stroke-linecap="round"
                    :stroke-dasharray="`${(kosJenisStats.campurPct / 100) * 314.16} 314.16`"
                    :stroke-dashoffset="`-${((kosJenisStats.putriPct + kosJenisStats.putraPct) / 100) * 314.16}`"
                    class="transition-all duration-700"
                  />
                </svg>

                <!-- Center Text -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                  <span class="text-3xl font-black text-slate-900 leading-none">{{ kosJenisStats.total }}</span>
                  <span class="text-[11px] font-bold text-slate-400 mt-1">Properti</span>
                </div>
              </div>

              <!-- Legend List -->
              <div class="w-full sm:w-auto flex-1 max-w-xs space-y-3 text-xs">
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="font-semibold text-slate-700">Kos Putri</span>
                  </div>
                  <span class="font-bold text-slate-900">{{ kosJenisStats.putriPct }}% ({{ kosJenisStats.putri }})</span>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-sky-500 shrink-0"></span>
                    <span class="font-semibold text-slate-700">Kos Putra</span>
                  </div>
                  <span class="font-bold text-slate-900">{{ kosJenisStats.putraPct }}% ({{ kosJenisStats.putra }})</span>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-purple-500 shrink-0"></span>
                    <span class="font-semibold text-slate-700">Kos Campur</span>
                  </div>
                  <span class="font-bold text-slate-900">{{ kosJenisStats.campurPct }}% ({{ kosJenisStats.campur }})</span>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total Kos Terdaftar Aktif</span>
            <span class="font-bold text-slate-900">{{ kosJenisStats.total }} Properti</span>
          </div>
        </div>

        <!-- Card 2: Tren Waktu Sewa (Donut Chart) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-1">
              <h3 class="text-base font-bold text-slate-900">Tren Waktu Sewa</h3>
              <!-- Filter Pill -->
              <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 select-none">
                <span>Semua Transaksi</span>
              </div>
            </div>
            <p class="text-xs text-slate-400 mb-6">Proporsi durasi sewa yang paling diminati penyewa kos</p>

            <div class="flex flex-col sm:flex-row items-center justify-around gap-6 py-2">
              <!-- Donut Chart for Booking Duration -->
              <div class="relative w-36 h-36 shrink-0">
                <svg class="w-full h-full transform -rotate-90 select-none" viewBox="0 0 140 140">
                  <!-- Background Track -->
                  <circle cx="70" cy="70" r="50" fill="none" stroke="#f1f5f9" stroke-width="16" />

                  <!-- Bulanan Segment (Green #10B981) -->
                  <circle
                    v-if="rentDurationStats.bulananPct > 0"
                    cx="70"
                    cy="70"
                    r="50"
                    fill="none"
                    stroke="#10B981"
                    stroke-width="16"
                    stroke-linecap="round"
                    :stroke-dasharray="`${(rentDurationStats.bulananPct / 100) * 314.16} 314.16`"
                    stroke-dashoffset="0"
                    class="transition-all duration-700"
                  />

                  <!-- Mingguan Segment (Cyan #06B6D4) -->
                  <circle
                    v-if="rentDurationStats.mingguanPct > 0"
                    cx="70"
                    cy="70"
                    r="50"
                    fill="none"
                    stroke="#06B6D4"
                    stroke-width="16"
                    stroke-linecap="round"
                    :stroke-dasharray="`${(rentDurationStats.mingguanPct / 100) * 314.16} 314.16`"
                    :stroke-dashoffset="`-${(rentDurationStats.bulananPct / 100) * 314.16}`"
                    class="transition-all duration-700"
                  />

                  <!-- Harian Segment (Purple #8B5CF6) -->
                  <circle
                    v-if="rentDurationStats.harianPct > 0"
                    cx="70"
                    cy="70"
                    r="50"
                    fill="none"
                    stroke="#8B5CF6"
                    stroke-width="16"
                    stroke-linecap="round"
                    :stroke-dasharray="`${(rentDurationStats.harianPct / 100) * 314.16} 314.16`"
                    :stroke-dashoffset="`-${((rentDurationStats.bulananPct + rentDurationStats.mingguanPct) / 100) * 314.16}`"
                    class="transition-all duration-700"
                  />
                </svg>

                <!-- Center Text -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                  <span class="text-base font-black text-emerald-600 leading-tight">{{ rentDurationStats.dominant }}</span>
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Favorit</span>
                </div>
              </div>

              <!-- Legend Breakdown -->
              <div class="w-full sm:w-auto flex-1 max-w-xs space-y-3 text-xs">
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-purple-500 shrink-0"></span>
                    <span class="font-semibold text-slate-700">Harian</span>
                  </div>
                  <span class="font-bold text-slate-900">{{ rentDurationStats.harianPct }}% ({{ rentDurationStats.harian }})</span>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-cyan-500 shrink-0"></span>
                    <span class="font-semibold text-slate-700">Mingguan</span>
                  </div>
                  <span class="font-bold text-slate-900">{{ rentDurationStats.mingguanPct }}% ({{ rentDurationStats.mingguan }})</span>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="font-semibold text-slate-700">Bulanan</span>
                  </div>
                  <span class="font-bold text-slate-900">{{ rentDurationStats.bulananPct }}% ({{ rentDurationStats.bulanan }})</span>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Total Transaksi Terdata</span>
            <span class="font-bold text-slate-900">{{ stats.total_pemesanan ?? 0 }} Pesanan</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 1: MANAJEMEN PENGGUNA TERPADU (MODUL 2) -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'pengguna'" class="space-y-6">
      <!-- 3 Quick Metric Highlights Strip -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Terdaftar</p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ userList.length }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Seluruh Pengguna
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
            <PhUsersThree :size="24" weight="duotone" />
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mitra Pemilik Kos</p>
            <h4 class="text-2xl font-black text-amber-600 mt-1">{{ pemilikCount }}</h4>
            <span class="inline-flex items-center text-amber-700 text-[11px] font-semibold mt-1">
              Mitra Penyedia Properti
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
            <PhUserCheck :size="24" weight="duotone" />
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pencari Kos</p>
            <h4 class="text-2xl font-black text-sky-600 mt-1">{{ pencariCount }}</h4>
            <span class="inline-flex items-center text-sky-700 text-[11px] font-semibold mt-1">
              Calon / Penghuni Kos
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
            <PhUser :size="24" weight="duotone" />
          </div>
        </div>
      </div>

      <!-- Search & Filters Toolbar -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="relative w-full sm:w-88">
            <input
              type="text"
              v-model="userSearchQuery"
              placeholder="Cari nama, email, atau nomor HP pengguna..."
              class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5"
              @input="debounceUserSearch"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <div class="flex items-center gap-2.5 w-full sm:w-auto flex-wrap sm:flex-nowrap">
            <!-- Filter Role -->
            <select
              v-model="userRoleFilter"
              @change="fetchUsers"
              class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white font-medium text-slate-700"
            >
              <option value="all">Semua Peran / Role</option>
              <option value="pemilik">Mitra Pemilik Kos</option>
              <option value="pencari">Pencari Kos</option>
            </select>

            <!-- Filter Status -->
            <select
              v-model="userStatusFilter"
              @change="fetchUsers"
              class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white font-medium text-slate-700"
            >
              <option value="all">Semua Status Akun</option>
              <option value="1">Akun Aktif</option>
              <option value="0">Akun Nonaktif</option>
            </select>

            <!-- Sort -->
            <select
              v-model="userSort"
              @change="fetchUsers"
              class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white font-medium text-slate-700"
            >
              <option value="id_asc">No 1 / ID Terdaftar Pertama</option>
              <option value="latest">Terbaru</option>
              <option value="unit_terbanyak">Properti Terbanyak</option>
              <option value="nama_asc">Nama A-Z</option>
            </select>

            <button
              @click="fetchUsers"
              class="p-2.5 text-slate-600 hover:bg-slate-100 rounded-xl border border-slate-200 transition"
              title="Segarkan Data Pengguna"
            >
              <svg class="w-4 h-4" :class="{ 'animate-spin': loadingUsers }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Users Table Card -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
              <tr>
                <th class="py-3.5 px-4 w-12 text-center">No</th>
                <th class="py-3.5 px-4">Pengguna</th>
                <th class="py-3.5 px-4 text-center">Peran / Role</th>
                <th class="py-3.5 px-4">Kontak WhatsApp</th>
                <th class="py-3.5 px-4 text-center">Aktivitas / Entitas</th>
                <th class="py-3.5 px-4 text-center">Status Akun</th>
                <th class="py-3.5 px-4">Terdaftar</th>
                <th class="py-3.5 px-4 text-right pr-6">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loadingUsers">
                <td colspan="8" class="py-10 text-center text-slate-400">Memuat data pengguna...</td>
              </tr>
              <tr v-else-if="userList.length === 0">
                <td colspan="8" class="py-10 text-center text-slate-400">Tidak ada data pengguna yang sesuai dengan filter.</td>
              </tr>
              <tr v-for="(user, idx) in userList" :key="user.id" class="hover:bg-slate-50/70 transition">
                <td class="py-4 px-4 text-center font-mono text-slate-400">{{ idx + 1 }}</td>
                <td class="py-4 px-4">
                  <div class="flex items-center gap-3">
                    <div
                      :class="[
                        'w-9 h-9 rounded-full font-extrabold flex items-center justify-center text-xs shrink-0',
                        user.role === 'pemilik' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-700'
                      ]"
                    >
                      {{ getInitials(user.nama) }}
                    </div>
                    <div>
                      <div class="font-bold text-slate-900">{{ user.nama }}</div>
                      <div class="text-[11px] text-slate-400 font-mono">
                        {{ user.role === 'pemilik' ? '#PMK-' : '#USR-' }}{{ user.id }} &bull; {{ user.email }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 text-center">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border',
                      user.role === 'pemilik' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-sky-50 text-sky-800 border-sky-200'
                    ]"
                  >
                    {{ user.role === 'pemilik' ? 'Mitra Pemilik' : 'Pencari Kos' }}
                  </span>
                </td>
                <td class="py-4 px-4 font-mono font-medium text-slate-700">
                  <span v-if="user.telepon">{{ user.telepon }}</span>
                  <span v-else class="text-slate-400 italic text-[11px]">Belum diisi</span>
                </td>
                <td class="py-4 px-4 text-center">
                  <span v-if="user.role === 'pemilik'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ user.kos_count || 0 }} Properti
                  </span>
                  <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 font-bold text-[11px]">
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
                    {{ user.aktif ? (user.role === 'pemilik' ? 'Terverifikasi' : 'Aktif') : 'Nonaktif' }}
                  </span>
                </td>
                <td class="py-4 px-4 text-slate-500 text-[11px]">{{ formatDate(user.created_at) }}</td>
                <td class="py-4 px-4 text-right pr-6 space-x-1.5 whitespace-nowrap">
                  <button
                    @click="viewUserDetail(user.id)"
                    class="px-2.5 py-1.5 text-[11px] font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    title="Lihat Detail Profil"
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
            <PhBuildings :size="24" weight="duotone" />
          </div>
        </div>

        <!-- Kos Putri -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Khusus Putri</p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ kosPutriCount }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Kategori Penghuni Putri
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold">
            <PhGenderFemale :size="24" weight="bold" />
          </div>
        </div>

        <!-- Kos Putra -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Khusus Putra</p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ kosPutraCount }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Kategori Penghuni Putra
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
            <PhGenderMale :size="24" weight="bold" />
          </div>
        </div>

        <!-- Kos Campur -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Campur / Pasutri</p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ kosCampurCount }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Kategori Penghuni Campur
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center font-bold">
            <PhUsers :size="24" weight="bold" />
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
            <option value="id_asc">No 1 / ID Terdaftar Pertama</option>
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

      <!-- Full-Width Kos Moderation Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-sm text-slate-900">Daftar Seluruh Properti Kos Nasional</h3>
            <span class="text-xs text-slate-400">({{ kosList.length }} listing)</span>
          </div>
          <p class="text-[11px] text-slate-400 hidden sm:block">Klik baris atau ikon pratinjau untuk melihat rincian properti</p>
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
                <th class="py-3.5 px-4 text-right pr-6 w-32">Aksi</th>
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
                @click="openKosPreview(kos)"
                class="hover:bg-slate-50/80 transition cursor-pointer"
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
                    <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ kos.pemilik.telepon || '-' }}</span>
                  </div>
                  <span v-else class="text-slate-400 italic text-[11px]">-</span>
                </td>
                <td class="py-3.5 px-4 max-w-xs">
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
                <td class="py-3.5 px-4 text-right pr-6 space-x-1 whitespace-nowrap">
                  <button
                    @click.stop="openKosPreview(kos)"
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
    </div>

    <!-- ================================================================= -->
    <!-- TAB 3: AUDIT TRAIL & LOG SELURUH PEMESANAN (MODUL 4) -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'pemesanan'" class="space-y-6">
      <!-- 4 Key Metrics Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pemesanan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</p>
            <h4 class="text-2xl font-black text-slate-900 mt-1">{{ allBookings.length }}</h4>
            <span class="inline-flex items-center text-slate-500 text-[11px] font-semibold mt-1">
              Seluruh Waktu
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
            <PhReceipt :size="24" weight="duotone" />
          </div>
        </div>

        <!-- Pemesanan Disetujui -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Disetujui</p>
            <h4 class="text-2xl font-black text-emerald-600 mt-1">{{ bookingsDisetujuiCount }}</h4>
            <span class="inline-flex items-center text-emerald-600 text-[11px] font-bold mt-1">
              Tervalidasi Masuk
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
            <PhCheckCircle :size="24" weight="duotone" />
          </div>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Konfirmasi</p>
            <h4 class="text-2xl font-black text-amber-600 mt-1">{{ bookingsMenungguCount }}</h4>
            <span class="inline-flex items-center text-amber-600 text-[11px] font-bold mt-1">
              Perlu Tindakan
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
            <PhHourglassHigh :size="24" weight="duotone" />
          </div>
        </div>

        <!-- Nilai Transaksi Disetujui -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nilai Transaksi Sah</p>
            <h4 class="text-lg font-black text-slate-900 mt-1 truncate max-w-[170px]" :title="formatRupiah(totalNilaiTransaksiDisetujui)">
              {{ formatRupiah(totalNilaiTransaksiDisetujui) }}
            </h4>
            <span class="inline-flex items-center text-emerald-600 text-[11px] font-bold mt-1">
              Estimasi Perolehan Sewa
            </span>
          </div>
          <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
            <PhCurrencyCircleDollar :size="24" weight="duotone" />
          </div>
        </div>
      </div>

      <!-- Time Pills & Search Bar -->
      <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
        <!-- Time Filter Pills -->
        <div class="flex items-center gap-1.5 flex-wrap w-full lg:w-auto">
          <button
            type="button"
            @click="setBookingPeriode('all')"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-bold transition',
              bookingPeriodeFilter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'
            ]"
          >
            Semua Waktu
          </button>
          <button
            type="button"
            @click="setBookingPeriode('today')"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-bold transition',
              bookingPeriodeFilter === 'today' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'
            ]"
          >
            Hari Ini
          </button>
          <button
            type="button"
            @click="setBookingPeriode('7d')"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-bold transition',
              bookingPeriodeFilter === '7d' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'
            ]"
          >
            7 Hari Terakhir
          </button>
          <button
            type="button"
            @click="setBookingPeriode('30d')"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-bold transition',
              bookingPeriodeFilter === '30d' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'
            ]"
          >
            30 Hari Terakhir
          </button>
        </div>

        <!-- Search Input & Status Filter -->
        <div class="flex items-center gap-2.5 w-full lg:w-auto flex-1 lg:max-w-md">
          <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="bookingSearchQuery"
              @input="debounceBookingSearch"
              type="text"
              placeholder="Cari ID pesanan, nama penyewa, kos..."
              class="w-full pl-10 pr-4 py-2 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 transition"
            />
          </div>

          <select
            v-model="bookingStatusFilter"
            @change="fetchAllBookings"
            class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 font-semibold cursor-pointer"
          >
            <option value="all">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="disetujui">Disetujui</option>
            <option value="ditolak">Ditolak</option>
            <option value="dibatalkan">Dibatalkan</option>
          </select>

          <button
            @click="resetBookingFilters"
            title="Reset Filter"
            class="p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Nationwide Booking Audit Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600 border-collapse">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
              <tr>
                <th class="py-3.5 px-4 text-center w-16">ID Order</th>
                <th class="py-3.5 px-4">Penyewa (Pencari Kos)</th>
                <th class="py-3.5 px-4">Properti Kos & Pemilik</th>
                <th class="py-3.5 px-4">Kamar & Tarif</th>
                <th class="py-3.5 px-4">Tanggal Masuk</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-right pr-6 w-36">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loadingBookings">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  Memuat log seluruh pemesanan...
                </td>
              </tr>
              <tr v-else-if="allBookings.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  Tidak ada data aktivitas pemesanan yang sesuai dengan filter.
                </td>
              </tr>
              <tr
                v-for="b in allBookings"
                :key="b.id"
                class="hover:bg-slate-50/70 transition"
              >
                <td class="py-3.5 px-4 text-center font-mono font-black text-slate-900">
                  #ORD-{{ b.id }}
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-extrabold flex items-center justify-center text-xs shrink-0">
                      {{ getInitials(b.pengguna?.nama) }}
                    </div>
                    <div>
                      <div class="font-bold text-slate-900">{{ b.pengguna?.nama || '-' }}</div>
                      <div class="text-[10px] text-slate-400">
                        {{ b.pengguna?.email || '-' }} <span v-if="b.pengguna?.telepon">&bull; {{ b.pengguna.telepon }}</span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-slate-800 line-clamp-1">{{ b.kos?.nama || 'Properti Kos' }}</div>
                  <div class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">
                    Mitra: {{ b.kos?.pemilik?.nama || '-' }} &bull; {{ b.kos?.alamat || '-' }}
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-emerald-700 text-xs">{{ b.kamar?.nama_kamar || '-' }}</div>
                  <div class="text-[11px] font-mono font-bold text-slate-800 mt-0.5">
                    {{ formatRupiah(b.total_harga || b.kamar?.harga) }}
                  </div>
                  <div class="text-[10px] text-slate-400">
                    Durasi: {{ formatDurasiOrder(b) }}
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="text-xs font-semibold text-slate-800">{{ formatDateOnly(b.tanggal_pemesanan) }}</div>
                  <div class="text-[10px] text-slate-400 mt-0.5">Diajukan: {{ formatDate(b.created_at) }}</div>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border',
                      statusBadgeClass(b.status)
                    ]"
                  >
                    {{ b.status }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right pr-6 space-x-1 whitespace-nowrap">
                  <button
                    @click="openBookingDetail(b)"
                    class="px-2.5 py-1 text-[11px] font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    title="Audit Rincian Pemesanan"
                  >
                    Audit Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 4: KELOLA HERO BANNER & SLIDER -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'banner' || activeTab === 'hero-slider'" class="space-y-8">
      <!-- 1. Interactive Live Slider Preview -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Pratinjau Langsung Beranda (Slider Otomatis 5 Detik)
              </h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Simulasi tampilan hero section beranda dengan background HD dan rotasi tiap 5 detik.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="toggleAdminPreviewPlayback"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-semibold text-slate-700 transition"
              type="button"
            >
              <component :is="adminPreviewPlaying ? PhPause : PhPlay" :size="14" weight="bold" />
              <span>{{ adminPreviewPlaying ? 'Jeda Slider' : 'Putar Slider' }}</span>
            </button>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-xl">
              Slide {{ bannersList.length > 0 ? adminPreviewIndex + 1 : 0 }} / {{ bannersList.length }}
            </span>
          </div>
        </div>

        <!-- Slider Stage -->
        <div class="relative w-full h-64 sm:h-80 rounded-2xl overflow-hidden shadow-inner flex items-center justify-start p-6 sm:p-10 select-none">
          <!-- Background image layer with crossfade -->
          <div
            v-for="(banner, idx) in (bannersList.length > 0 ? bannersList : adminDefaultBanners)"
            :key="banner.id || idx"
            class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out"
            :class="idx === adminPreviewIndex ? 'opacity-100 z-0' : 'opacity-0 -z-10'"
            :style="{ backgroundImage: `url('${banner.gambar_url}')` }"
          ></div>

          <!-- Gradient overlay -->
          <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/55 to-slate-950/20 z-0"></div>

          <!-- Content overlay -->
          <div class="relative z-10 max-w-lg text-left">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/15 text-white text-[11px] font-medium mb-2.5 shadow-sm">
              <PhMapPin :size="14" weight="fill" class="text-amber-400 shrink-0" />
              <span>{{ currentAdminPreviewBanner?.badge_text || 'Cari kos di seluruh Indonesia' }}</span>
            </div>
            <h4 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-sm">
              {{ currentAdminPreviewBanner?.judul || 'Kos Nyaman, Hidup Lebih Mudah' }}
            </h4>
            <p class="text-xs text-slate-200/90 mt-2 line-clamp-2 max-w-md">
              {{ currentAdminPreviewBanner?.subjudul || 'Temukan kos terbaik sesuai kebutuhanmu.' }}
            </p>
            <div class="flex items-center gap-1.5 mt-4">
              <button
                v-for="(b, idx) in (bannersList.length > 0 ? bannersList : adminDefaultBanners)"
                :key="idx"
                @click="adminPreviewIndex = idx"
                class="h-1.5 rounded-full transition-all duration-300"
                :class="idx === adminPreviewIndex ? 'w-6 bg-amber-400' : 'w-1.5 bg-white/40 hover:bg-white/70'"
                type="button"
              ></button>
              <span class="text-[10px] text-white/70 ml-2 font-mono">Interval: 5s</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Slide Hero</span>
          <div class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ bannersList.length }} Slide</div>
          <p class="text-xs text-slate-400 mt-1">Total banner terdaftar di sistem</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Slide Aktif di Beranda</span>
          <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">
            {{ bannersList.filter(b => b.is_active).length }} Slide
          </div>
          <p class="text-xs text-slate-400 mt-1">Ditampilkan pada slider pengunjung publik</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Durasi Interval Slider</span>
          <div class="text-2xl font-extrabold text-primary tracking-tight">5 Detik</div>
          <p class="text-xs text-slate-400 mt-1">Rotasi otomatis latar belakang beranda</p>
        </div>
      </div>

      <!-- 3. Banners List Grid -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-bold text-slate-900">Daftar Banner Latar Belakang HD</h3>
            <p class="text-xs text-slate-500 mt-0.5">Kelola gambar HD, judul, teks badge, urutan, dan status aktif slide beranda.</p>
          </div>
          <button
            @click="openAddBannerModal"
            class="inline-flex items-center gap-1.5 bg-primary hover:bg-primary-container text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm active:scale-95"
            type="button"
          >
            <PhPlus :size="16" weight="bold" />
            <span>Tambah Banner Baru</span>
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loadingBanners" class="p-12 text-center">
          <svg class="w-8 h-8 mx-auto animate-spin text-primary mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <p class="text-xs text-slate-500 font-medium">Memuat data banner slider...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="bannersList.length === 0" class="p-12 text-center">
          <PhImage :size="48" weight="duotone" class="mx-auto text-slate-300 mb-3" />
          <p class="text-sm font-bold text-slate-700">Belum ada banner slider tersimpan</p>
          <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Klik tombol "Tambah Banner Baru" untuk memasukkan foto background HD kamar kos.</p>
        </div>

        <!-- Cards Grid -->
        <div v-else class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            v-for="(banner, idx) in bannersList"
            :key="banner.id"
            class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50/50 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between"
          >
            <!-- Image Header -->
            <div class="relative w-full aspect-[16/9] bg-slate-200 overflow-hidden group">
              <img
                :src="banner.gambar_url"
                :alt="banner.judul || 'Banner HD'"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30"></div>

              <!-- Top badges -->
              <div class="absolute top-3 left-3 flex items-center gap-1.5">
                <span class="bg-black/60 backdrop-blur-md text-amber-400 font-bold text-[10px] px-2.5 py-0.5 rounded-full border border-white/10">
                  HD 1080p
                </span>
                <span class="bg-black/60 backdrop-blur-md text-white font-bold text-[10px] px-2 py-0.5 rounded-full border border-white/10">
                  Urutan #{{ banner.urutan }}
                </span>
              </div>

              <!-- Active Toggle on Image -->
              <div class="absolute top-3 right-3">
                <button
                  @click="toggleBannerActive(banner)"
                  class="px-2.5 py-1 rounded-full text-[10px] font-extrabold transition shadow-sm flex items-center gap-1"
                  :class="banner.is_active ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-slate-700/80 text-slate-300 hover:bg-slate-800'"
                  :title="banner.is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                  type="button"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="banner.is_active ? 'bg-white' : 'bg-rose-400'"></span>
                  <span>{{ banner.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </button>
              </div>

              <!-- Bottom Content Overlay -->
              <div class="absolute bottom-3 left-3 right-3 text-white">
                <span v-if="banner.badge_text" class="text-[10px] font-semibold text-amber-300 block mb-0.5">
                  📍 {{ banner.badge_text }}
                </span>
                <h4 class="text-sm font-bold truncate">{{ banner.judul || 'Tanpa Judul' }}</h4>
              </div>
            </div>

            <!-- Body Details -->
            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
              <p class="text-xs text-slate-600 line-clamp-2">
                {{ banner.subjudul || 'Tidak ada deskripsi subjudul.' }}
              </p>

              <!-- Image URL & Actions -->
              <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between text-xs">
                <span class="text-[11px] text-slate-400 font-mono truncate max-w-[180px]" :title="banner.gambar_url">
                  {{ banner.gambar_url }}
                </span>
                <div class="flex items-center gap-1.5 shrink-0">
                  <button
                    @click="openEditBannerModal(banner)"
                    class="p-2 rounded-xl text-slate-600 hover:text-primary hover:bg-slate-100 transition cursor-pointer"
                    title="Edit Banner"
                    type="button"
                  >
                    <PhPencilSimple :size="16" weight="bold" />
                  </button>
                  <button
                    @click="confirmDeleteBanner(banner)"
                    class="p-2 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer"
                    title="Hapus Banner"
                    type="button"
                  >
                    <PhTrash :size="16" weight="bold" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 5: VERIFIKASI KEPEMILIKAN PROPERTI MITRA PEMILIK -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'verifikasi'" class="space-y-6">
      <!-- 4 Stat Summary Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Menunggu Tinjauan -->
        <div
          @click="verificationStatusFilter = 'pending'; fetchVerifications()"
          class="bg-white rounded-3xl border p-5 shadow-xs transition cursor-pointer hover:border-amber-400"
          :class="verificationStatusFilter === 'pending' ? 'ring-2 ring-amber-500 border-amber-300' : 'border-slate-200/80'"
        >
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Tinjauan</span>
            <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
              <PhHourglassHigh :size="18" weight="bold" />
            </div>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-2xl font-black text-slate-900">{{ pendingVerifCount }}</span>
            <span v-if="pendingVerifCount > 0" class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 animate-pulse">
              Perlu Ditinjau
            </span>
          </div>
          <p class="text-[11px] text-slate-400 mt-2">Pengajuan baru yang menunggu persetujuan</p>
        </div>

        <!-- Card 2: Terverifikasi / Disetujui -->
        <div
          @click="verificationStatusFilter = 'verified'; fetchVerifications()"
          class="bg-white rounded-3xl border p-5 shadow-xs transition cursor-pointer hover:border-emerald-400"
          :class="verificationStatusFilter === 'verified' ? 'ring-2 ring-emerald-500 border-emerald-300' : 'border-slate-200/80'"
        >
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mitra Terverifikasi</span>
            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
              <PhCheckCircle :size="18" weight="bold" />
            </div>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-2xl font-black text-slate-900">{{ verifiedVerifCount }}</span>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
              Aktif Mengelola
            </span>
          </div>
          <p class="text-[11px] text-slate-400 mt-2">Pemilik sah yang memiliki akses penuh kelola kos</p>
        </div>

        <!-- Card 3: Ditolak / Perlu Perbaikan -->
        <div
          @click="verificationStatusFilter = 'rejected'; fetchVerifications()"
          class="bg-white rounded-3xl border p-5 shadow-xs transition cursor-pointer hover:border-rose-400"
          :class="verificationStatusFilter === 'rejected' ? 'ring-2 ring-rose-500 border-rose-300' : 'border-slate-200/80'"
        >
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ditolak / Revisi</span>
            <div class="w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
              <PhXCircle :size="18" weight="bold" />
            </div>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-2xl font-black text-slate-900">{{ rejectedVerifCount }}</span>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
              Perlu Koreksi
            </span>
          </div>
          <p class="text-[11px] text-slate-400 mt-2">Dokumen tidak valid atau buram</p>
        </div>

        <!-- Card 4: Belum Mengajukan -->
        <div
          @click="verificationStatusFilter = 'unverified'; fetchVerifications()"
          class="bg-white rounded-3xl border p-5 shadow-xs transition cursor-pointer hover:border-slate-400"
          :class="verificationStatusFilter === 'unverified' ? 'ring-2 ring-slate-800 border-slate-300' : 'border-slate-200/80'"
        >
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Mengajukan</span>
            <div class="w-9 h-9 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
              <PhShieldCheck :size="18" weight="bold" />
            </div>
          </div>
          <div class="flex items-baseline gap-2 mt-2">
            <span class="text-2xl font-black text-slate-900">{{ unverifiedVerifCount }}</span>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
              Fitur Terkunci
            </span>
          </div>
          <p class="text-[11px] text-slate-400 mt-2">Akun pemilik terdaftar belum submit berkas</p>
        </div>
      </div>

      <!-- Filter, Search, and Status Tabs Bar -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <!-- Search input -->
          <div class="relative flex-1 max-w-md">
            <input
              v-model="verificationSearchQuery"
              @input="handleVerificationSearchDebounced"
              type="text"
              placeholder="Cari nama pemilik, email, atau NIK..."
              class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button
              v-if="verificationSearchQuery"
              @click="verificationSearchQuery = ''; fetchVerifications()"
              class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold"
            >
              &times;
            </button>
          </div>

          <!-- Status Filter Tabs / Pills -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            <button
              v-for="st in [
                { key: 'all', label: 'Semua Status' },
                { key: 'pending', label: 'Menunggu', badge: pendingVerifCount },
                { key: 'verified', label: 'Disetujui', badge: verifiedVerifCount },
                { key: 'rejected', label: 'Ditolak', badge: rejectedVerifCount },
                { key: 'unverified', label: 'Belum Ajukan' },
              ]"
              :key="st.key"
              @click="verificationStatusFilter = st.key; fetchVerifications()"
              type="button"
              class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 cursor-pointer"
              :class="verificationStatusFilter === st.key ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'"
            >
              <span>{{ st.label }}</span>
              <span v-if="st.badge !== undefined && st.badge > 0" class="text-[10px] px-1.5 py-0.2 rounded-full" :class="st.key === 'pending' ? 'bg-amber-400 text-slate-950 font-black' : 'bg-white/20 text-white'">
                {{ st.badge }}
              </span>
            </button>
          </div>
        </div>
      </div>

      <!-- Verification List Table -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Loading Skeleton -->
        <div v-if="loadingVerifications" class="p-8 space-y-4">
          <div v-for="i in 4" :key="i" class="h-16 bg-slate-100 rounded-2xl animate-pulse"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="verificationList.length === 0" class="p-12 text-center">
          <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
            <PhCertificate :size="32" weight="bold" />
          </div>
          <h4 class="text-sm font-bold text-slate-800">Tidak ada data verifikasi</h4>
          <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
            {{ verificationSearchQuery ? 'Tidak ditemukan data pengajuan dengan kata kunci tersebut.' : 'Belum ada data pengajuan verifikasi kepemilikan pemilik yang cocok dengan filter saat ini.' }}
          </p>
        </div>

        <!-- Data Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                <th class="py-3.5 px-5">Mitra Pemilik</th>
                <th class="py-3.5 px-4">Nomor NIK & Status</th>
                <th class="py-3.5 px-4">Lampiran Berkas</th>
                <th class="py-3.5 px-4">Status & Catatan</th>
                <th class="py-3.5 px-5 text-right">Tindakan Admin</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="user in verificationList"
                :key="user.id"
                class="hover:bg-slate-50/60 transition group"
              >
                <!-- Mitra Pemilik Column -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs uppercase overflow-hidden shrink-0 shadow-xs">
                      <img v-if="user.foto" :src="user.foto" :alt="user.nama" class="w-full h-full object-cover" />
                      <span v-else>{{ (user.nama || 'P').charAt(0) }}</span>
                    </div>
                    <div class="min-w-0">
                      <div class="flex items-center gap-1.5">
                        <span class="font-bold text-slate-900 text-xs truncate">{{ user.nama }}</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-1.5 py-0.2 rounded">
                          ID: {{ user.id }}
                        </span>
                      </div>
                      <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ user.email }}</div>
                      <div v-if="user.nomor_telepon" class="flex items-center gap-1 mt-1 text-[11px] text-emerald-600 font-medium">
                        <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <a :href="`https://wa.me/${user.nomor_telepon.replace(/[^0-9]/g, '')}`" target="_blank" class="hover:underline">
                          {{ user.nomor_telepon }}
                        </a>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- NIK & Ringkasan Dokumen -->
                <td class="py-4 px-4">
                  <div class="space-y-1">
                    <div class="flex items-center gap-1.5">
                      <span class="text-[10px] text-slate-400 font-bold uppercase">NIK:</span>
                      <span v-if="user.nik" class="font-mono font-bold text-slate-800 text-[11px] bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">
                        {{ user.nik }}
                      </span>
                      <span v-else class="text-[11px] text-slate-400 italic">Belum diisi</span>
                    </div>
                    <div class="text-[10px] text-slate-400">
                      {{ user.verified_at ? `Diverifikasi: ${new Date(user.verified_at).toLocaleDateString('id-ID')}` : 'Status verifikasi kepemilikan kos' }}
                    </div>
                  </div>
                </td>

                <!-- Lampiran Berkas Thumbnail (KTP, PBB, Properti) -->
                <td class="py-4 px-4">
                  <div class="flex items-center gap-2">
                    <!-- KTP Thumbnail -->
                    <button
                      v-if="user.foto_ktp"
                      type="button"
                      @click="openDocPreview(user, 'ktp')"
                      class="relative w-12 h-9 rounded-lg border border-slate-200 overflow-hidden group/thumb cursor-pointer hover:border-primary transition"
                      title="Klik untuk melihat e-KTP"
                    >
                      <img :src="user.foto_ktp" alt="KTP" class="w-full h-full object-cover group-hover/thumb:scale-105 transition" />
                      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/thumb:opacity-100 transition flex items-center justify-center text-white">
                        <PhEye :size="14" weight="bold" />
                      </div>
                      <span class="absolute bottom-0 left-0 right-0 bg-slate-900/80 text-[8px] font-bold text-white text-center py-0.2">KTP</span>
                    </button>
                    <div v-else class="w-12 h-9 rounded-lg border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-[9px] text-slate-400">
                      No KTP
                    </div>

                    <!-- PBB / Listrik Thumbnail -->
                    <button
                      v-if="user.bukti_properti"
                      type="button"
                      @click="openDocPreview(user, 'properti')"
                      class="relative w-12 h-9 rounded-lg border border-slate-200 overflow-hidden group/thumb cursor-pointer hover:border-primary transition"
                      title="Klik untuk melihat Bukti PBB / Tagihan Listrik"
                    >
                      <img :src="user.bukti_properti" alt="PBB" class="w-full h-full object-cover group-hover/thumb:scale-105 transition" />
                      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/thumb:opacity-100 transition flex items-center justify-center text-white">
                        <PhEye :size="14" weight="bold" />
                      </div>
                      <span class="absolute bottom-0 left-0 right-0 bg-slate-900/80 text-[8px] font-bold text-white text-center py-0.2">PBB</span>
                    </button>
                    <div v-else class="w-12 h-9 rounded-lg border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-[9px] text-slate-400">
                      No PBB
                    </div>

                    <!-- Foto Bangunan Properti Thumbnail -->
                    <button
                      v-if="user.foto_properti"
                      type="button"
                      @click="openDocPreview(user, 'gedung')"
                      class="relative w-12 h-9 rounded-lg border border-slate-200 overflow-hidden group/thumb cursor-pointer hover:border-primary transition"
                      title="Klik untuk melihat Foto Bangunan Kos"
                    >
                      <img :src="user.foto_properti" alt="Fisik Kos" class="w-full h-full object-cover group-hover/thumb:scale-105 transition" />
                      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/thumb:opacity-100 transition flex items-center justify-center text-white">
                        <PhEye :size="14" weight="bold" />
                      </div>
                      <span class="absolute bottom-0 left-0 right-0 bg-slate-900/80 text-[8px] font-bold text-white text-center py-0.2">Fisik</span>
                    </button>
                    <div v-else class="w-12 h-9 rounded-lg border border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-[9px] text-slate-400">
                      No Foto
                    </div>
                  </div>
                </td>

                <!-- Status & Catatan Verifikasi -->
                <td class="py-4 px-4 max-w-xs">
                  <div class="space-y-1.5">
                    <!-- Status Badge -->
                    <div class="flex items-center gap-1.5">
                      <span
                        v-if="user.status_verifikasi === 'verified'"
                        class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2.5 py-0.5 rounded-full border border-emerald-200 text-[10px]"
                      >
                        <PhCheckCircle :size="12" weight="bold" />
                        Terverifikasi
                      </span>
                      <span
                        v-else-if="user.status_verifikasi === 'pending'"
                        class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 font-bold px-2.5 py-0.5 rounded-full border border-amber-200 text-[10px] animate-pulse"
                      >
                        <PhHourglassHigh :size="12" weight="bold" />
                        Menunggu Tinjauan
                      </span>
                      <span
                        v-else-if="user.status_verifikasi === 'rejected'"
                        class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 font-bold px-2.5 py-0.5 rounded-full border border-rose-200 text-[10px]"
                      >
                        <PhXCircle :size="12" weight="bold" />
                        Ditolak
                      </span>
                      <span
                        v-else
                        class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 font-bold px-2.5 py-0.5 rounded-full border border-slate-200 text-[10px]"
                      >
                        Belum Mengajukan
                      </span>
                    </div>

                    <!-- Owner Note if any -->
                    <div v-if="user.catatan_verifikasi" class="text-[11px] text-slate-600 bg-slate-50 p-1.5 rounded-lg border border-slate-100 line-clamp-2" :title="user.catatan_verifikasi">
                      <span class="font-bold text-slate-700">Catatan Pemilik:</span> "{{ user.catatan_verifikasi }}"
                    </div>

                    <!-- Rejection Reason if any -->
                    <div v-if="user.status_verifikasi === 'rejected' && user.alasan_penolakan" class="text-[11px] text-rose-700 bg-rose-50/70 p-1.5 rounded-lg border border-rose-100 line-clamp-2" :title="user.alasan_penolakan">
                      <span class="font-bold">Alasan Ditolak:</span> {{ user.alasan_penolakan }}
                    </div>
                  </div>
                </td>

                <!-- Tindakan Admin Column -->
                <td class="py-4 px-5 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Inspect Button -->
                    <button
                      type="button"
                      @click="openDocPreview(user, 'ktp')"
                      class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                      title="Periksa Dokumen Lengkap"
                    >
                      <PhEye :size="14" weight="bold" />
                      <span>Periksa</span>
                    </button>

                    <!-- Setujui Button -->
                    <button
                      v-if="user.status_verifikasi !== 'verified'"
                      type="button"
                      @click="handleApproveVerification(user)"
                      :disabled="reviewingUserId === user.id"
                      class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 shadow-2xs disabled:opacity-50 cursor-pointer"
                      title="Setujui Verifikasi & Buka Akses Kelola Kos"
                    >
                      <svg v-if="reviewingUserId === user.id" class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      <PhCheck v-else :size="14" weight="bold" />
                      <span>Setujui</span>
                    </button>

                    <!-- Tolak Button -->
                    <button
                      v-if="user.status_verifikasi !== 'rejected'"
                      type="button"
                      @click="openRejectModal(user)"
                      class="px-2.5 py-1.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                      title="Tolak Dokumen & Minta Koreksi"
                    >
                      <PhX :size="14" weight="bold" />
                      <span>Tolak</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: TAMBAH / EDIT HERO BANNER SLIDER -->
    <!-- ================================================================= -->
    <div
      v-if="showBannerModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
          <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold">
              <PhSlideshow :size="22" weight="bold" />
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900">
                {{ editingBannerId ? 'Edit Banner Slider' : 'Tambah Banner Slider Baru' }}
              </h3>
              <p class="text-xs text-slate-400">
                Pengaturan gambar HD dan konten slide latar belakang 5 detik.
              </p>
            </div>
          </div>
          <button @click="showBannerModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none cursor-pointer">&times;</button>
        </div>

        <!-- Body Form -->
        <form @submit.prevent="handleSaveBanner" class="flex-1 overflow-y-auto p-6 space-y-4">
          <!-- Judul Banner -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Judul Banner (Pisahkan koma untuk aksen kuning di beranda)
            </label>
            <input
              v-model="bannerForm.judul"
              type="text"
              class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary/20 transition"
              placeholder="Contoh: Kos Nyaman, Hidup Lebih Mudah"
            />
            <span class="text-[10px] text-slate-400 mt-1 block">
              Kata setelah tanda koma otomatis diwarnai kuning aksen seperti pada desain.
            </span>
          </div>

          <!-- Subjudul -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Subjudul Deskripsi
            </label>
            <textarea
              v-model="bannerForm.subjudul"
              rows="2"
              class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary/20 transition"
              placeholder="Temukan kos terbaik sesuai kebutuhanmu. Lokasi strategis, fasilitas lengkap, harga transparan."
            ></textarea>
          </div>

          <!-- Badge Text & Urutan Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Teks Badge / Tagline
              </label>
              <input
                v-model="bannerForm.badge_text"
                type="text"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary/20 transition"
                placeholder="Cari kos di seluruh Indonesia"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Urutan Tampilan
              </label>
              <input
                v-model.number="bannerForm.urutan"
                type="number"
                min="0"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary/20 transition"
              />
            </div>
          </div>

          <!-- Status Aktif -->
          <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
            <input
              id="banner-is-active"
              v-model="bannerForm.is_active"
              type="checkbox"
              class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary cursor-pointer"
            />
            <label for="banner-is-active" class="text-xs font-bold text-slate-800 cursor-pointer">
              Aktifkan slide ini langsung di slider beranda
            </label>
          </div>

          <!-- Gambar Background HD -->
          <div class="space-y-3 pt-2">
            <label class="block text-xs font-bold text-slate-700">
              Pilihan Gambar Background HD <span class="text-rose-500">*</span>
            </label>

            <!-- URL Input -->
            <div class="space-y-1.5">
              <input
                v-model="bannerForm.gambar_url"
                type="text"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary/20 transition"
                placeholder="https://images.unsplash.com/... atau URL gambar HD"
                required
              />
            </div>

            <!-- Upload File Option -->
            <div class="p-3 border border-dashed border-slate-300 rounded-2xl bg-slate-50 text-center">
              <input
                ref="bannerFileInput"
                type="file"
                accept="image/png,image/jpeg,image/webp"
                class="hidden"
                @change="handleBannerFileUpload"
              />
              <button
                type="button"
                @click="$refs.bannerFileInput?.click()"
                :disabled="uploadingBannerImg"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-xs cursor-pointer"
              >
                <PhUploadSimple :size="16" weight="bold" />
                <span>{{ uploadingBannerImg ? 'Mengunggah Berkas...' : 'Unggah File Gambar dari Komputer' }}</span>
              </button>
              <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB)</p>
            </div>

            <!-- Preset HD Room Buttons -->
            <div>
              <span class="text-[11px] font-bold text-slate-500 block mb-1.5">Pilihan Cepat Foto HD Rekomendasi:</span>
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="(preset, pIdx) in hdPresetPhotos"
                  :key="pIdx"
                  type="button"
                  @click="bannerForm.gambar_url = preset.url"
                  class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border transition cursor-pointer"
                  :class="bannerForm.gambar_url === preset.url ? 'bg-primary text-white border-primary' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'"
                >
                  {{ preset.label }}
                </button>
              </div>
            </div>

            <!-- Live Image Preview -->
            <div v-if="bannerForm.gambar_url" class="mt-3">
              <span class="text-[11px] font-bold text-slate-500 block mb-1">Pratinjau Gambar:</span>
              <div class="relative w-full aspect-[16/9] rounded-2xl overflow-hidden border border-slate-200 bg-slate-100">
                <img
                  :src="bannerForm.gambar_url"
                  alt="Pratinjau"
                  class="w-full h-full object-cover"
                />
                <span class="absolute top-2 left-2 bg-black/60 backdrop-blur-md text-amber-400 font-bold text-[10px] px-2 py-0.5 rounded-full">
                  HD Preview
                </span>
              </div>
            </div>
          </div>
        </form>

        <!-- Footer -->
        <div class="shrink-0 px-6 py-3.5 border-t border-slate-100 bg-slate-50/70 flex items-center justify-end gap-2.5">
          <button
            type="button"
            @click="showBannerModal = false"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200/60 rounded-xl transition cursor-pointer"
          >
            Batal
          </button>
          <button
            type="button"
            @click="handleSaveBanner"
            :disabled="savingBanner || !bannerForm.gambar_url"
            class="px-5 py-2 text-xs font-bold bg-primary hover:bg-primary-container text-white rounded-xl transition shadow-sm disabled:opacity-50 flex items-center gap-2 cursor-pointer"
          >
            <svg v-if="savingBanner" class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>{{ savingBanner ? 'Menyimpan...' : 'Simpan Banner' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL: KONFIRMASI HAPUS BANNER -->
    <div
      v-if="showDeleteBannerConfirmModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3">
          <PhTrash :size="24" weight="bold" />
        </div>
        <h4 class="text-sm font-bold text-slate-900 mb-1">Hapus Banner Slider Ini?</h4>
        <p class="text-xs text-slate-500 mb-5 leading-relaxed">
          Banner "{{ bannerToDelete?.judul || 'HD Slider' }}" akan dihapus secara permanen dari rotasi slider beranda.
        </p>
        <div class="flex items-center justify-center gap-2">
          <button
            type="button"
            @click="showDeleteBannerConfirmModal = false"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer"
          >
            Batal
          </button>
          <button
            type="button"
            @click="executeDeleteBanner"
            :disabled="deletingBanner"
            class="px-4 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition shadow-sm disabled:opacity-50 cursor-pointer"
          >
            {{ deletingBanner ? 'Menghapus...' : 'Ya, Hapus' }}
          </button>
        </div>
      </div>
    </div>
    <div
      v-if="showKosPreviewModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header (Fixed) -->
        <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
          <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <PhBuildings :size="22" weight="bold" />
            </div>
            <div>
              <h3 class="text-base font-black text-slate-900">
                Pratinjau Listing Kos
              </h3>
              <p class="text-[11px] text-slate-400 font-mono">
                ID: #KOS-{{ selectedKosPreview?.id }}
              </p>
            </div>
          </div>
          <button @click="showKosPreviewModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
        </div>

        <!-- Body (Scrollable) -->
        <div v-if="selectedKosPreview" class="flex-1 overflow-y-auto p-6 space-y-4">
          <!-- Hero Image with Gender Badge & Title Overlay -->
          <div class="relative w-full h-48 rounded-2xl overflow-hidden shadow-inner bg-slate-100 group">
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
          <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
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

          <!-- Address & Geocoding -->
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
            <div class="flex items-start gap-2">
              <PhMapPin :size="18" weight="bold" class="text-primary shrink-0 mt-0.5" />
              <div>
                <p class="font-bold text-slate-800 text-[11px]">Alamat Lengkap</p>
                <p class="text-slate-500 leading-relaxed text-[11px] mt-0.5">{{ selectedKosPreview.alamat }}</p>
              </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
              <span class="text-[10px] font-mono text-slate-500">
                Lat: {{ selectedKosPreview.latitude || '-' }} &bull; Lng: {{ selectedKosPreview.longitude || '-' }}
              </span>
              <a
                v-if="selectedKosPreview.latitude && selectedKosPreview.longitude"
                :href="`https://www.google.com/maps/search/?api=1&query=${selectedKosPreview.latitude},${selectedKosPreview.longitude}`"
                target="_blank"
                class="text-[10px] font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1"
              >
                Buka Peta Google Maps &rarr;
              </a>
            </div>
          </div>

          <!-- Owner Information Card -->
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 font-extrabold flex items-center justify-center text-xs shrink-0">
                {{ getInitials(selectedKosPreview.pemilik?.nama) }}
              </div>
              <div>
                <p class="font-bold text-slate-800 text-xs">{{ selectedKosPreview.pemilik?.nama || 'Tanpa Pemilik' }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ selectedKosPreview.pemilik?.email }} &bull; {{ selectedKosPreview.pemilik?.telepon || '-' }}</p>
              </div>
            </div>
            <button
              v-if="selectedKosPreview.pemilik?.telepon"
              @click="chatKosPemilik(selectedKosPreview)"
              class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition flex items-center gap-1 shadow-sm shrink-0"
            >
              Chat WA Pemilik
            </button>
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
                <PhCheck :size="13" weight="bold" class="text-primary" />
                {{ fas.nama }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <div v-if="selectedKosPreview.deskripsi">
            <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Deskripsi Properti</span>
            <p class="text-[11px] text-slate-500 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
              {{ selectedKosPreview.deskripsi }}
            </p>
          </div>
        </div>

        <!-- Footer (Fixed) -->
        <div v-if="selectedKosPreview" class="shrink-0 px-6 py-3.5 border-t border-slate-100 bg-slate-50/70 grid grid-cols-2 gap-2">
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
            @click="deleteKosModerasi(selectedKosPreview); showKosPreviewModal = false;"
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
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: AUDIT DETAIL PEMESANAN (MODUL 4) -->
    <!-- ================================================================= -->
    <div
      v-if="showBookingDetailModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header (Fixed) -->
        <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
              <PhReceipt :size="22" weight="bold" />
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-black text-slate-900">
                  Audit Pemesanan #ORD-{{ selectedBookingDetail?.id }}
                </h3>
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border',
                    statusBadgeClass(selectedBookingDetail?.status)
                  ]"
                >
                  {{ selectedBookingDetail?.status }}
                </span>
              </div>
              <p class="text-[11px] text-slate-400 mt-0.5">
                Dibuat pada: {{ formatDate(selectedBookingDetail?.created_at) }}
              </p>
            </div>
          </div>
          <button @click="showBookingDetailModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
          <!-- Seeker Info Card -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase text-slate-400">Penyewa (Pencari Kos)</span>
              <button
                v-if="selectedBookingDetail?.pengguna?.telepon"
                @click="chatPenyewaWhatsApp(selectedBookingDetail)"
                class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition flex items-center gap-1 shadow-sm"
              >
                Chat WA Penyewa
              </button>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-800 font-extrabold flex items-center justify-center text-xs shrink-0">
                {{ getInitials(selectedBookingDetail?.pengguna?.nama) }}
              </div>
              <div>
                <p class="font-bold text-slate-900 text-xs">{{ selectedBookingDetail?.pengguna?.nama || '-' }}</p>
                <p class="text-[11px] text-slate-500 font-mono">{{ selectedBookingDetail?.pengguna?.email }} &bull; {{ selectedBookingDetail?.pengguna?.telepon || 'Tanpa No. HP' }}</p>
              </div>
            </div>
          </div>

          <!-- Kos & Room Info -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Properti Kos</span>
              <p class="font-bold text-slate-900 text-xs line-clamp-1">{{ selectedBookingDetail?.kos?.nama || '-' }}</p>
              <p class="text-[10px] text-slate-500 line-clamp-2 mt-0.5">{{ selectedBookingDetail?.kos?.alamat || '-' }}</p>
              <router-link
                v-if="selectedBookingDetail?.kos_id"
                :to="`/kos/${selectedBookingDetail.kos_id}`"
                target="_blank"
                class="inline-block text-[10px] font-bold text-blue-600 hover:text-blue-700 mt-2"
              >
                Buka Listing Kos &rarr;
              </router-link>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
              <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Kamar & Rincian Transaksi</span>
              <p class="font-bold text-emerald-700 text-xs">{{ selectedBookingDetail?.kamar?.nama_kamar || '-' }}</p>
              <div class="flex items-center justify-between mt-1 text-xs">
                <span class="text-slate-500">Durasi Sewa:</span>
                <span class="font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200">
                  {{ formatDurasiOrder(selectedBookingDetail) }}
                </span>
              </div>
              <div class="flex items-center justify-between mt-1 text-xs">
                <span class="text-slate-500">Total Biaya:</span>
                <span class="font-extrabold text-primary">
                  {{ formatRupiah(selectedBookingDetail?.total_harga || selectedBookingDetail?.kamar?.harga) }}
                </span>
              </div>
              <div class="text-[10px] text-slate-500 mt-2 border-t border-slate-200/60 pt-1.5 space-y-0.5">
                <div>Rencana Masuk: <span class="font-semibold text-slate-700">{{ formatDateOnly(selectedBookingDetail?.tanggal_pemesanan || selectedBookingDetail?.tanggal_mulai) }}</span></div>
                <div v-if="selectedBookingDetail?.tanggal_selesai">Selesai: <span class="font-semibold text-slate-700">{{ formatDateOnly(selectedBookingDetail?.tanggal_selesai) }}</span></div>
              </div>
            </div>
          </div>

          <!-- Audit Governance Information -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <PhShieldCheck :size="20" weight="bold" class="text-primary shrink-0 mt-0.5" />
            <div class="text-xs text-slate-600 leading-relaxed">
              <span class="font-bold text-slate-800 block mb-0.5">Tata Kelola Persetujuan Sewa:</span>
              Status konfirmasi sewa dikelola secara mandiri oleh <span class="font-semibold text-slate-800">Pemilik Kos</span> melalui Dashboard Mitra Pemilik. Administrator memantau seluruh transaksi untuk kebutuhan audit trail, verifikasi, dan ekspor pelaporan nasional.
            </div>
          </div>
        </div>

        <!-- Footer (Fixed) -->
        <div class="shrink-0 px-6 py-3.5 border-t border-slate-100 bg-slate-50/70 flex justify-end">
          <button
            type="button"
            @click="showBookingDetailModal = false"
            class="px-5 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: DETAIL PENGGUNA (PENCARI / MITRA PEMILIK) -->
    <!-- ================================================================= -->
    <div
      v-if="showUserDetailModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[90vh] overflow-hidden">
        <!-- Header (Fixed) -->
        <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
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
          <button @click="showUserDetailModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-6">
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
          </div>
        </div>

        <!-- Footer (Fixed) -->
        <div class="shrink-0 px-6 py-3.5 border-t border-slate-100 bg-slate-50/70 flex items-center justify-between">
          <button
            v-if="!loadingUserDetail"
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
          <div v-else></div>
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

    <!-- ================================================================= -->
    <!-- MODAL: PREVIEW DOKUMEN VERIFIKASI PEMILIK -->
    <!-- ================================================================= -->
    <div
      v-if="showDocPreviewModal && previewDocUser"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
      @click.self="showDocPreviewModal = false"
    >
      <div class="bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[92vh] overflow-hidden">
        <!-- Header -->
        <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
              <PhCertificate :size="22" weight="bold" />
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base font-bold text-slate-900">
                  Dokumen Verifikasi: {{ previewDocUser.nama }}
                </h3>
                <span
                  v-if="previewDocUser.status_verifikasi === 'verified'"
                  class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200"
                >
                  Verified
                </span>
                <span
                  v-else-if="previewDocUser.status_verifikasi === 'pending'"
                  class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200"
                >
                  Pending
                </span>
                <span
                  v-else-if="previewDocUser.status_verifikasi === 'rejected'"
                  class="bg-rose-50 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-200"
                >
                  Rejected
                </span>
              </div>
              <p class="text-xs text-slate-400 mt-0.5">
                NIK: <span class="font-mono font-bold text-slate-700">{{ previewDocUser.nik || 'Belum diisi' }}</span> &bull; {{ previewDocUser.email }}
              </p>
            </div>
          </div>
          <button @click="showDocPreviewModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none cursor-pointer">&times;</button>
        </div>

        <!-- Document Tab Switcher -->
        <div class="px-6 pt-3 pb-2 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
          <button
            type="button"
            @click="previewDocTab = 'ktp'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
            :class="previewDocTab === 'ktp' ? 'bg-primary text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
          >
            <span>Foto e-KTP</span>
            <span v-if="previewDocUser.foto_ktp" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
          </button>
          <button
            type="button"
            @click="previewDocTab = 'properti'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
            :class="previewDocTab === 'properti' ? 'bg-primary text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
          >
            <span>Bukti PBB / PLN</span>
            <span v-if="previewDocUser.bukti_properti" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
          </button>
          <button
            type="button"
            @click="previewDocTab = 'gedung'"
            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
            :class="previewDocTab === 'gedung' ? 'bg-primary text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
          >
            <span>Foto Bangunan Properti</span>
            <span v-if="previewDocUser.foto_properti" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
          </button>
        </div>

        <!-- Body / Image Viewer -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
          <!-- Active Document Viewer -->
          <div class="bg-slate-950 rounded-2xl overflow-hidden flex items-center justify-center min-h-[300px] max-h-[440px] relative border border-slate-800">
            <!-- If KTP -->
            <template v-if="previewDocTab === 'ktp'">
              <img
                v-if="previewDocUser.foto_ktp"
                :src="previewDocUser.foto_ktp"
                alt="e-KTP Pemilik"
                class="w-full h-full object-contain max-h-[440px]"
              />
              <div v-else class="text-center p-8 text-slate-400">
                <PhCertificate :size="40" class="mx-auto mb-2 opacity-50" />
                <p class="text-xs">Foto e-KTP belum diunggah.</p>
              </div>
              <a
                v-if="previewDocUser.foto_ktp"
                :href="previewDocUser.foto_ktp"
                target="_blank"
                class="absolute bottom-3 right-3 bg-black/70 hover:bg-black text-white text-[10px] font-bold px-3 py-1.5 rounded-xl backdrop-blur-md transition flex items-center gap-1.5"
              >
                <PhEye :size="12" weight="bold" />
                <span>Buka Gambar Ukuran Asli</span>
              </a>
            </template>

            <!-- If Properti / PBB -->
            <template v-else-if="previewDocTab === 'properti'">
              <img
                v-if="previewDocUser.bukti_properti"
                :src="previewDocUser.bukti_properti"
                alt="Bukti Tagihan Properti PBB / PLN"
                class="w-full h-full object-contain max-h-[440px]"
              />
              <div v-else class="text-center p-8 text-slate-400">
                <PhFileText :size="40" class="mx-auto mb-2 opacity-50" />
                <p class="text-xs">Bukti PBB atau rekening listrik belum diunggah.</p>
              </div>
              <a
                v-if="previewDocUser.bukti_properti"
                :href="previewDocUser.bukti_properti"
                target="_blank"
                class="absolute bottom-3 right-3 bg-black/70 hover:bg-black text-white text-[10px] font-bold px-3 py-1.5 rounded-xl backdrop-blur-md transition flex items-center gap-1.5"
              >
                <PhEye :size="12" weight="bold" />
                <span>Buka Gambar Ukuran Asli</span>
              </a>
            </template>

            <!-- If Gedung / Bangunan -->
            <template v-else-if="previewDocTab === 'gedung'">
              <img
                v-if="previewDocUser.foto_properti"
                :src="previewDocUser.foto_properti"
                alt="Foto Bangunan Kos"
                class="w-full h-full object-contain max-h-[440px]"
              />
              <div v-else class="text-center p-8 text-slate-400">
                <PhBuildings :size="40" class="mx-auto mb-2 opacity-50" />
                <p class="text-xs">Foto fisik tampak bangunan kos belum diunggah.</p>
              </div>
              <a
                v-if="previewDocUser.foto_properti"
                :href="previewDocUser.foto_properti"
                target="_blank"
                class="absolute bottom-3 right-3 bg-black/70 hover:bg-black text-white text-[10px] font-bold px-3 py-1.5 rounded-xl backdrop-blur-md transition flex items-center gap-1.5"
              >
                <PhEye :size="12" weight="bold" />
                <span>Buka Gambar Ukuran Asli</span>
              </a>
            </template>
          </div>

          <!-- Metadata Box -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
            <div>
              <span class="text-slate-400 font-bold block text-[10px] uppercase">Catatan Pemilik:</span>
              <p class="text-slate-700 mt-0.5 font-medium">{{ previewDocUser.catatan_verifikasi || 'Tidak ada catatan tambahan.' }}</p>
            </div>
            <div>
              <span class="text-slate-400 font-bold block text-[10px] uppercase">Kontak Pemilik:</span>
              <p class="text-slate-700 mt-0.5 font-medium">{{ previewDocUser.nomor_telepon || '-' }} ({{ previewDocUser.email }})</p>
            </div>
            <div v-if="previewDocUser.alasan_penolakan" class="sm:col-span-2 bg-rose-50 border border-rose-200 p-2.5 rounded-xl text-rose-800">
              <span class="font-bold block text-[10px] uppercase text-rose-600">Alasan Penolakan Sebelumnya:</span>
              <p class="mt-0.5 text-xs">{{ previewDocUser.alasan_penolakan }}</p>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="shrink-0 px-6 py-3.5 border-t border-slate-100 bg-slate-50/70 flex items-center justify-between">
          <button
            type="button"
            @click="showDocPreviewModal = false"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200/60 rounded-xl transition cursor-pointer"
          >
            Tutup
          </button>
          <div class="flex items-center gap-2">
            <button
              v-if="previewDocUser.status_verifikasi !== 'rejected'"
              type="button"
              @click="showDocPreviewModal = false; openRejectModal(previewDocUser)"
              class="px-4 py-2 text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl transition flex items-center gap-1.5 cursor-pointer"
            >
              <PhX :size="14" weight="bold" />
              <span>Tolak Pengajuan</span>
            </button>
            <button
              v-if="previewDocUser.status_verifikasi !== 'verified'"
              type="button"
              @click="handleApproveVerification(previewDocUser); showDocPreviewModal = false"
              class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer"
            >
              <PhCheck :size="14" weight="bold" />
              <span>Setujui Verifikasi</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: TOLAK PENGAJUAN VERIFIKASI -->
    <!-- ================================================================= -->
    <div
      v-if="showRejectModal && rejectingUser"
      class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
      @click.self="showRejectModal = false"
    >
      <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-100 p-6 space-y-4">
        <!-- Header -->
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold shrink-0">
            <PhXCircle :size="24" weight="bold" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">
              Tolak Pengajuan Verifikasi
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
              Pemilik: <span class="font-bold text-slate-700">{{ rejectingUser.nama }}</span> ({{ rejectingUser.email }})
            </p>
          </div>
        </div>

        <!-- Presets Buttons -->
        <div>
          <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
            Pilihan Alasan Cepat:
          </label>
          <div class="flex flex-wrap gap-1.5">
            <button
              v-for="preset in [
                'Foto KTP buram / tidak terbaca dengan jelas.',
                'Nama pada KTP tidak cocok dengan bukti kepemilikan PBB/PLN.',
                'Bukti tagihan PBB / PLN sudah kadaluarsa (lebih dari 1 tahun).',
                'Foto fisik bangunan kos tidak memperlihatkan tampak depan.',
                'Nomor NIK tidak valid atau salah format.'
              ]"
              :key="preset"
              type="button"
              @click="rejectReason = preset"
              class="px-2.5 py-1 text-[11px] rounded-lg border font-medium transition cursor-pointer text-left"
              :class="rejectReason === preset ? 'bg-rose-500 text-white border-rose-500' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'"
            >
              {{ preset }}
            </button>
          </div>
        </div>

        <!-- Detailed Reason Textarea -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">
            Catatan Perbaikan untuk Pemilik <span class="text-rose-500">*</span>
          </label>
          <textarea
            v-model="rejectReason"
            rows="3"
            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-500/20 transition resize-none"
            placeholder="Tuliskan petunjuk jelas dokumen apa yang harus diunggah ulang..."
            required
          ></textarea>
          <p class="text-[10px] text-slate-400 mt-1">Catatan ini akan tampil langsung di halaman verifikasi mitra pemilik.</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
          <button
            type="button"
            @click="showRejectModal = false"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer"
          >
            Batal
          </button>
          <button
            type="button"
            @click="handleConfirmReject"
            :disabled="submittingReject || !rejectReason.trim()"
            class="px-5 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition shadow-xs disabled:opacity-50 flex items-center gap-1.5 cursor-pointer"
          >
            <svg v-if="submittingReject" class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <PhX v-else :size="14" weight="bold" />
            <span>{{ submittingReject ? 'Memproses...' : 'Tolak Pengajuan' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  PhSquaresFour,
  PhUsersThree,
  PhBuildings,
  PhReceipt,
  PhTable,
  PhUserCheck,
  PhUser,
  PhGenderFemale,
  PhGenderMale,
  PhUsers,
  PhCheckCircle,
  PhHourglassHigh,
  PhCurrencyCircleDollar,
  PhMapPin,
  PhCheck,
  PhShieldCheck,
  PhSlideshow,
  PhPlus,
  PhPencilSimple,
  PhTrash,
  PhImage,
  PhUploadSimple,
  PhPlay,
  PhPause,
  PhCertificate,
  PhXCircle,
  PhEye,
  PhX,
  PhArrowUp,
  PhArrowDown,
  PhArrowRight,
  PhCalendarBlank,
  PhCalendarCheck,
  PhCaretDown,
  PhFileText,
} from '@phosphor-icons/vue'
import api from '../services/api'
import { useUiStore } from '../stores/ui'
import { openWhatsAppChat } from '../utils/whatsapp'

const route = useRoute()
const router = useRouter()
const ui = useUiStore()

const validTabs = ['ringkasan', 'pengguna', 'verifikasi', 'kos', 'pemesanan', 'banner', 'hero-slider']
let startTab = 'ringkasan'
if (route.query.tab === 'pencari' || route.query.tab === 'pemilik') {
  startTab = 'pengguna'
} else if (route.query.tab === 'banner' || route.query.tab === 'hero-slider') {
  startTab = 'banner'
} else if (validTabs.includes(route.query.tab)) {
  startTab = route.query.tab
}
const activeTab = ref(startTab)

const switchTab = (tabName) => {
  activeTab.value = tabName
  router.replace({ query: { ...route.query, tab: tabName } })
}

watch(
  () => route.query.tab,
  (newTab) => {
    if (newTab === 'pencari' || newTab === 'pemilik') {
      activeTab.value = 'pengguna'
      if (newTab === 'pencari') userRoleFilter.value = 'pencari'
      if (newTab === 'pemilik') userRoleFilter.value = 'pemilik'
      fetchUsers()
    } else if (newTab === 'banner' || newTab === 'hero-slider') {
      activeTab.value = 'banner'
    } else if (newTab && validTabs.includes(newTab)) {
      activeTab.value = newTab
    }
  }
)

const stats = ref({})
const loadingStats = ref(false)

const activeTabMeta = computed(() => {
  switch (activeTab.value) {
    case 'ringkasan':
      return {
        icon: PhSquaresFour,
        title: 'Ringkasan & Analitik',
        badge: null,
        description: 'Statistik performa real-time dan ringkasan eksekutif platform kos.',
      }
    case 'pengguna':
      return {
        icon: PhUsersThree,
        title: 'Manajemen Pengguna Terpadu',
        badge: `${userList.value.length} Pengguna`,
        description: 'Kelola akun mitra pemilik kos, pencari kos, dan verifikasi kepatuhan sistem.',
      }
    case 'verifikasi':
      return {
        icon: PhCertificate,
        title: 'Verifikasi Kepemilikan Properti',
        badge: `${pendingVerifCount.value} Menunggu`,
        description: 'Tinjau bukti identitas (KTP), tagihan PBB/PLN, dan foto fisik properti kos calon mitra.',
      }
    case 'kos':
      return {
        icon: PhBuildings,
        title: 'Data & Moderasi Properti Kos',
        badge: `${kosList.value.length} Listing`,
        description: 'Katalog data seluruh kos nasional dan pengawasan listing properti.',
      }
    case 'pemesanan':
      return {
        icon: PhReceipt,
        title: 'Audit Log Pemesanan (Audit Trail)',
        badge: `${allBookings.value.length} Transaksi`,
        description: 'Rekapitulasi dan audit transaksi reservasi sewa kos seluruh Indonesia.',
      }
    case 'banner':
    case 'hero-slider':
      return {
        icon: PhSlideshow,
        title: 'Kelola Banner & Hero Slider 5 Detik',
        badge: `${bannersList.value.length} Slide`,
        description: 'Kelola gambar background HD, judul, badge, dan urutan slider beranda utama.',
      }
    default:
      return {
        icon: PhSquaresFour,
        title: 'Dashboard Administrator',
        badge: null,
        description: 'Panel tata kelola sistem platform kos.',
      }
  }
})

const userList = ref([])
const loadingUsers = ref(false)
const userSearchQuery = ref('')
const userRoleFilter = ref(route.query.tab === 'pemilik' ? 'pemilik' : (route.query.tab === 'pencari' ? 'pencari' : 'all'))
const userStatusFilter = ref('all')
const userSort = ref('id_asc')

const pemilikCount = computed(() => userList.value.filter(u => u.role === 'pemilik').length)
const pencariCount = computed(() => userList.value.filter(u => u.role === 'pencari').length)

const kosList = ref([])
const loadingKos = ref(false)
const kosSearchQuery = ref('')
const kosJenisFilter = ref('all')
const kosSort = ref('id_asc')
const selectedKosPreview = ref(null)
const showKosPreviewModal = ref(false)
const deletingKosId = ref(null)

const openKosPreview = (kos) => {
  selectedKosPreview.value = kos
  showKosPreviewModal.value = true
}

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


const allBookings = ref([])
const loadingBookings = ref(false)
const bookingSearchQuery = ref('')
const bookingStatusFilter = ref('all')
const bookingPeriodeFilter = ref('all')
const showBookingDetailModal = ref(false)
const selectedBookingDetail = ref(null)
const updatingBookingId = ref(null)
const togglingUserId = ref(null)


const bookingsDisetujuiCount = computed(() => {
  return allBookings.value.filter(b => b.status === 'disetujui').length
})

const bookingsMenungguCount = computed(() => {
  return allBookings.value.filter(b => b.status === 'menunggu').length
})

const totalNilaiTransaksiDisetujui = computed(() => {
  return allBookings.value
    .filter(b => b.status === 'disetujui')
    .reduce((acc, b) => acc + (b.kamar?.harga || 0), 0)
})


const showUserDetailModal = ref(false)
const loadingUserDetail = ref(false)
const selectedUserDetail = ref(null)

const successMsg = ref(null)
const errorMsg = ref(null)

const currentTrendRange = ref('auto')
const showRangeDropdown = ref(false)

const selectTrendRange = (range) => {
  currentTrendRange.value = range
  showRangeDropdown.value = false
  fetchStats(range)
}

const fetchStats = async (range = currentTrendRange.value) => {
  loadingStats.value = true
  try {
    const res = await api.get('/admin/stats', {
      params: { range }
    })
    stats.value = res.data.data || {}
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat analitik dashboard.'
  } finally {
    loadingStats.value = false
  }
}

const bookingTrends7Days = computed(() => {
  const backendTrends = stats.value.tren_harian || []
  return backendTrends.map(d => ({
    label: d.label,
    total: d.total ?? d.count ?? 0,
    disetujui: d.disetujui ?? 0,
    tanggal: d.tanggal,
  }))
})

const maxDisetujuiVal = computed(() => {
  const max = Math.max(0, ...bookingTrends7Days.value.map(d => d.disetujui || 0))
  if (max <= 4) return 5
  if (max <= 8) return 10
  return Math.ceil(max * 1.25)
})

const yAxisTicks = computed(() => {
  const max = maxDisetujuiVal.value
  const step = max / 5
  return [
    { val: Math.round(step * 5), y: 15, lineY: 12 },
    { val: Math.round(step * 4), y: 45, lineY: 42 },
    { val: Math.round(step * 3), y: 75, lineY: 72 },
    { val: Math.round(step * 2), y: 105, lineY: 102 },
    { val: Math.round(step * 1), y: 135, lineY: 132 },
    { val: 0, y: 158, lineY: 154 },
  ]
})

const kosJenisStats = computed(() => {
  const dist = stats.value.distribusi_jenis || { putri: 0, putra: 0, campur: 0 }
  const putri = dist.putri ?? 0
  const putra = dist.putra ?? 0
  const campur = dist.campur ?? 0
  const total = putri + putra + campur

  if (total === 0) {
    return {
      total: 0,
      putri: 0,
      putra: 0,
      campur: 0,
      putriPct: 0,
      putraPct: 0,
      campurPct: 0,
    }
  }

  const putriPct = Math.round((putri / total) * 100)
  const putraPct = Math.round((putra / total) * 100)
  const campurPct = Math.max(0, 100 - putriPct - putraPct)

  return {
    total,
    putri,
    putra,
    campur,
    putriPct,
    putraPct,
    campurPct,
  }
})

const rentDurationStats = computed(() => {
  const dist = stats.value.distribusi_durasi || { harian: 0, mingguan: 0, bulanan: 0 }
  const harian = dist.harian ?? 0
  const mingguan = dist.mingguan ?? 0
  const bulanan = dist.bulanan ?? 0
  const total = harian + mingguan + bulanan

  if (total === 0) {
    return {
      total: 0,
      harian: 0,
      mingguan: 0,
      bulanan: 0,
      harianPct: 0,
      mingguanPct: 0,
      bulananPct: 0,
      dominant: '-',
    }
  }

  const harianPct = Math.round((harian / total) * 100)
  const mingguanPct = Math.round((mingguan / total) * 100)
  const bulananPct = Math.max(0, 100 - harianPct - mingguanPct)

  let dominant = 'Bulanan'
  if (harian > mingguan && harian > bulanan) {
    dominant = 'Harian'
  } else if (mingguan > harian && mingguan > bulanan) {
    dominant = 'Mingguan'
  }

  return {
    total,
    harian,
    mingguan,
    bulanan,
    harianPct,
    mingguanPct,
    bulananPct,
    dominant,
  }
})

const recentActivities = computed(() => {
  if (stats.value.aktivitas_terbaru && stats.value.aktivitas_terbaru.length > 0) {
    return stats.value.aktivitas_terbaru
  }
  return [
    {
      tipe: 'user',
      judul: 'User baru mendaftar',
      deskripsi: 'Siti Nurhaliza • 2 jam lalu',
    },
    {
      tipe: 'booking',
      judul: 'Reservasi baru',
      deskripsi: 'Kamar A1 • 4 jam lalu',
    },
    {
      tipe: 'kos',
      judul: 'Pengajuan kos',
      deskripsi: 'Kos Melati • 5 jam lalu',
    },
    {
      tipe: 'admin',
      judul: 'Login admin',
      deskripsi: 'admin • 6 jam lalu',
    },
  ]
})

const formatRupiah = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val || 0)
}

let userSearchTimeout = null
const debounceUserSearch = () => {
  clearTimeout(userSearchTimeout)
  userSearchTimeout = setTimeout(() => {
    fetchUsers()
  }, 350)
}

const fetchUsers = async () => {
  loadingUsers.value = true
  try {
    const params = {}
    if (userRoleFilter.value !== 'all') params.role = userRoleFilter.value
    if (userSearchQuery.value) params.q = userSearchQuery.value
    if (userStatusFilter.value !== 'all') params.aktif = userStatusFilter.value
    if (userSort.value) params.sort = userSort.value

    const res = await api.get('/admin/users', { params })
    let data = res.data.data || []
    if (userSort.value === 'id_asc') {
      data = data.sort((a, b) => a.id - b.id)
    }
    userList.value = data
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat daftar pengguna.'
  } finally {
    loadingUsers.value = false
  }
}

const viewUserDetail = async (userId) => {
  loadingUserDetail.value = true
  showUserDetailModal.value = true
  try {
    const res = await api.get(`/admin/users/${userId}`)
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
    await api.patch(`/admin/users/${user.id}/status`, { aktif: nextStatus })
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
      ? `Halo Bapak/Ibu Mitra Kostara ${user.nama},\n\nKami dari Manajemen Layanan Kostara ingin berkoordinasi mengenai operasional dan data properti kos Anda di platform Kostara.\n\nTerima kasih!`
      : `Halo Kak ${user.nama},\n\nKami dari tim Layanan Pelanggan Kostara ingin mengonfirmasi terkait akun dan aktivitas pemesanan sewa Anda di Kostara. Apakah ada hal yang membutuhkan bantuan kami?\n\nTerima kasih!`

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
  kosSort.value = 'id_asc'
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
    let data = res.data.data || []
    if (kosSort.value === 'id_asc') {
      data = data.sort((a, b) => a.id - b.id)
    }
    kosList.value = data

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
  const confirmDelete = await ui.confirm({
    title: 'Hapus Properti Kos (Moderasi)',
    message: `Apakah Anda yakin ingin MENGHAPUS properti "${kos.nama}" (#KOS-${kos.id})?\n\nTindakan ini bersifat permanen dan akan menghapus seluruh data kamar, fasilitas terkait, dan riwayat pesanan kos ini.`,
    confirmText: 'Ya, Hapus Properti',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
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

  const message = `Halo Bapak/Ibu Mitra Kostara ${kos.pemilik.nama},\n\nKami dari tim Moderasi & Administrator Kostara ingin berkoordinasi mengenai listing properti Anda: "${kos.nama}" (ID: #KOS-${kos.id}) yang berlokasi di ${kos.alamat}.\n\nMohon konfirmasi ketersediaan dan status operasional terbaru kos Anda.\n\nTerima kasih!`
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


let bookingSearchTimeout = null
const debounceBookingSearch = () => {
  clearTimeout(bookingSearchTimeout)
  bookingSearchTimeout = setTimeout(() => {
    fetchAllBookings()
  }, 350)
}

const setBookingPeriode = (periode) => {
  bookingPeriodeFilter.value = periode
  fetchAllBookings()
}

const resetBookingFilters = () => {
  bookingSearchQuery.value = ''
  bookingStatusFilter.value = 'all'
  bookingPeriodeFilter.value = 'all'
  fetchAllBookings()
}

const fetchAllBookings = async () => {
  loadingBookings.value = true
  try {
    const params = { per_page: 100 }
    if (bookingSearchQuery.value) params.q = bookingSearchQuery.value
    if (bookingStatusFilter.value !== 'all') params.status = bookingStatusFilter.value
    if (bookingPeriodeFilter.value !== 'all') params.periode = bookingPeriodeFilter.value

    const res = await api.get('/bookings', { params })
    allBookings.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat log pemesanan.'
  } finally {
    loadingBookings.value = false
  }
}

const openBookingDetail = (booking) => {
  selectedBookingDetail.value = booking
  showBookingDetailModal.value = true
}

const updateBookingStatus = async (booking, newStatus) => {
  if (!booking) return
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Status Pemesanan',
    message: `Ubah status pemesanan #ORD-${booking.id} menjadi "${newStatus.toUpperCase()}"?`,
    confirmText: 'Ya, Ubah Status',
    cancelText: 'Batal',
    confirmType: newStatus === 'ditolak' || newStatus === 'dibatalkan' ? 'danger' : 'primary',
  })
  if (!confirmed) {
    return
  }

  updatingBookingId.value = booking.id
  try {
    const res = await api.patch(`/bookings/${booking.id}/status`, { status: newStatus })
    successMsg.value = res.data?.message || `Status pesanan #ORD-${booking.id} berhasil diubah menjadi ${newStatus}.`
    booking.status = newStatus
    if (selectedBookingDetail.value && selectedBookingDetail.value.id === booking.id) {
      selectedBookingDetail.value.status = newStatus
    }
    fetchStats()
    fetchKos()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memperbarui status pemesanan.'
  } finally {
    updatingBookingId.value = null
  }
}

const chatPenyewaWhatsApp = (booking) => {
  if (!booking || !booking.pengguna) return
  const phone = booking.pengguna.telepon || booking.pengguna.no_hp
  if (!phone) {
    alert(`Penyewa (${booking.pengguna.nama}) belum mencantumkan nomor telepon / WhatsApp.`)
    return
  }

  const message = `Halo Kak ${booking.pengguna.nama},\n\nKami dari Administrator Layanan Kostara ingin mengonfirmasi pesanan sewa Anda (#ORD-${booking.id}) untuk properti "${booking.kos?.nama || 'Kos'}" (Kamar: ${booking.kamar?.nama_kamar || '-'}).\n\nStatus pesanan Anda saat ini: ${booking.status.toUpperCase()}.\n\nApakah ada pertanyaan atau kendala yang bisa kami bantu?\n\nTerima kasih!`
  openWhatsAppChat(phone, message)
}

const formatDurasiOrder = (b) => {
  if (!b) return '-'
  if (b.durasi_tipe && b.durasi_jumlah) {
    const unit = b.durasi_tipe.charAt(0).toUpperCase() + b.durasi_tipe.slice(1)
    return `${b.durasi_jumlah} ${unit}`
  }
  const bln = b.durasi_sewa_bulan || b.durasi_bulan || 1
  return `${bln} Bulan`
}

const exportBookingsToCSV = () => {
  if (allBookings.value.length === 0) {
    alert('Tidak ada data pemesanan untuk diekspor.')
    return
  }

  const headers = ['ID Pesanan', 'Nama Penyewa', 'Email', 'Telepon', 'Nama Kos', 'Nama Kamar', 'Durasi Sewa', 'Total Biaya', 'Tanggal Masuk', 'Tanggal Selesai', 'Status', 'Tanggal Pengajuan']
  const rows = allBookings.value.map(b => [
    `#ORD-${b.id}`,
    `"${(b.pengguna?.nama || '-').replace(/"/g, '""')}"`,
    `"${(b.pengguna?.email || '-').replace(/"/g, '""')}"`,
    `"${(b.pengguna?.telepon || '-').replace(/"/g, '""')}"`,
    `"${(b.kos?.nama || '-').replace(/"/g, '""')}"`,
    `"${(b.kamar?.nama_kamar || '-').replace(/"/g, '""')}"`,
    `"${formatDurasiOrder(b)}"`,
    b.total_harga || b.kamar?.harga || 0,
    b.tanggal_pemesanan || b.tanggal_mulai || '-',
    b.tanggal_selesai || '-',
    b.status || '-',
    b.created_at ? new Date(b.created_at).toLocaleString('id-ID') : '-',
  ])

  const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `audit_pemesanan_kostara_${new Date().toISOString().slice(0, 10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
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

// =========================================================================
// BANNER & HERO SLIDER MANAGEMENT (MODUL 5)
// =========================================================================
const bannersList = ref([])
const loadingBanners = ref(false)
const showBannerModal = ref(false)
const editingBannerId = ref(null)
const savingBanner = ref(false)
const uploadingBannerImg = ref(false)
const bannerFileInput = ref(null)

const bannerForm = reactive({
  judul: '',
  subjudul: '',
  badge_text: '',
  gambar_url: '',
  urutan: 1,
  is_active: true,
})

const showDeleteBannerConfirmModal = ref(false)
const bannerToDelete = ref(null)
const deletingBanner = ref(false)

const adminDefaultBanners = [
  {
    id: 1,
    judul: 'Kos Nyaman, Hidup Lebih Mudah',
    subjudul: 'Temukan kos terbaik sesuai kebutuhanmu. Lokasi strategis, fasilitas lengkap, harga transparan.',
    badge_text: 'Cari kos di seluruh Indonesia',
    gambar_url: 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop',
    urutan: 1,
    is_active: true,
  },
  {
    id: 2,
    judul: 'Hunian Idaman, Dekat Kampus & Kantor',
    subjudul: 'Fasilitas lengkap dengan akses mudah, WiFi super cepat, dan lingkungan nyaman.',
    badge_text: 'Pilihan Hunian Eksklusif & Nyaman',
    gambar_url: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=2071&auto=format&fit=crop',
    urutan: 2,
    is_active: true,
  },
]

const hdPresetPhotos = [
  {
    label: 'Kamar Modern & Balkon (Sesuai Desain)',
    url: 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop',
  },
  {
    label: 'Kamar Minimalis Aesthetic',
    url: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=2071&auto=format&fit=crop',
  },
  {
    label: 'Studio Kos Warm & Kayu',
    url: 'https://images.unsplash.com/photo-1540518614846-7ede433c4ef7?q=80&w=2071&auto=format&fit=crop',
  },
  {
    label: 'Scandinavian Bedroom Cerah',
    url: 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=2070&auto=format&fit=crop',
  },
]

const adminPreviewIndex = ref(0)
const adminPreviewPlaying = ref(true)
let adminPreviewTimer = null

const currentAdminPreviewBanner = computed(() => {
  const list = bannersList.value.length > 0 ? bannersList.value : adminDefaultBanners
  return list[adminPreviewIndex.value % list.length]
})

const startAdminPreviewSlider = () => {
  stopAdminPreviewSlider()
  adminPreviewTimer = setInterval(() => {
    if (adminPreviewPlaying.value) {
      const total = bannersList.value.length > 0 ? bannersList.value.length : adminDefaultBanners.length
      if (total > 1) {
        adminPreviewIndex.value = (adminPreviewIndex.value + 1) % total
      }
    }
  }, 5000)
}

const stopAdminPreviewSlider = () => {
  if (adminPreviewTimer) {
    clearInterval(adminPreviewTimer)
    adminPreviewTimer = null
  }
}

const toggleAdminPreviewPlayback = () => {
  adminPreviewPlaying.value = !adminPreviewPlaying.value
}

const fetchAdminBanners = async () => {
  loadingBanners.value = true
  try {
    const res = await api.get('/admin/hero-banners')
    bannersList.value = res.data?.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat data banner slider.'
  } finally {
    loadingBanners.value = false
  }
}

const openAddBannerModal = () => {
  editingBannerId.value = null
  bannerForm.judul = 'Kos Nyaman, Hidup Lebih Mudah'
  bannerForm.subjudul = 'Temukan kos terbaik sesuai kebutuhanmu. Lokasi strategis, fasilitas lengkap, harga transparan.'
  bannerForm.badge_text = 'Cari kos di seluruh Indonesia'
  bannerForm.gambar_url = 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop'
  bannerForm.urutan = bannersList.value.length + 1
  bannerForm.is_active = true
  showBannerModal.value = true
}

const openEditBannerModal = (banner) => {
  editingBannerId.value = banner.id
  bannerForm.judul = banner.judul || ''
  bannerForm.subjudul = banner.subjudul || ''
  bannerForm.badge_text = banner.badge_text || ''
  bannerForm.gambar_url = banner.gambar_url || ''
  bannerForm.urutan = banner.urutan ?? 0
  bannerForm.is_active = !!banner.is_active
  showBannerModal.value = true
}

const handleSaveBanner = async () => {
  if (!bannerForm.gambar_url) {
    errorMsg.value = 'URL atau berkas gambar background HD wajib ditentukan.'
    return
  }

  savingBanner.value = true
  try {
    if (editingBannerId.value) {
      await api.put(`/admin/hero-banners/${editingBannerId.value}`, bannerForm)
      successMsg.value = 'Banner slider berhasil diperbarui.'
    } else {
      await api.post('/admin/hero-banners', bannerForm)
      successMsg.value = 'Banner slider baru berhasil ditambahkan.'
    }
    showBannerModal.value = false
    await fetchAdminBanners()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan banner slider.'
  } finally {
    savingBanner.value = false
  }
}

const toggleBannerActive = async (banner) => {
  const newStatus = !banner.is_active
  banner.is_active = newStatus
  try {
    await api.patch(`/admin/hero-banners/${banner.id}/status`, {
      is_active: newStatus,
    })
    successMsg.value = `Status banner #${banner.id} berhasil diubah menjadi ${newStatus ? 'Aktif' : 'Nonaktif'}.`
  } catch (err) {
    banner.is_active = !newStatus
    errorMsg.value = err.response?.data?.message || 'Gagal memperbarui status banner.'
  }
}

const confirmDeleteBanner = (banner) => {
  bannerToDelete.value = banner
  showDeleteBannerConfirmModal.value = true
}

const executeDeleteBanner = async () => {
  if (!bannerToDelete.value) return
  deletingBanner.value = true
  try {
    await api.delete(`/admin/hero-banners/${bannerToDelete.value.id}`)
    successMsg.value = 'Banner slider berhasil dihapus.'
    showDeleteBannerConfirmModal.value = false
    bannerToDelete.value = null
    await fetchAdminBanners()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menghapus banner slider.'
  } finally {
    deletingBanner.value = false
  }
}

const handleBannerFileUpload = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return

  if (file.size > 2 * 1024 * 1024) {
    errorMsg.value = 'Ukuran berkas gambar melebihi 2MB. Silakan pilih gambar lain.'
    return
  }

  uploadingBannerImg.value = true
  const formData = new FormData()
  formData.append('images[]', file)

  try {
    const res = await api.post('/uploads/images', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (res.data?.urls?.[0]) {
      bannerForm.gambar_url = res.data.urls[0]
      successMsg.value = 'Gambar berhasil diunggah!'
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal mengunggah berkas gambar.'
  } finally {
    uploadingBannerImg.value = false
    if (e.target) e.target.value = ''
  }
}

// ==========================================
// MODUL VERIFIKASI KEPEMILIKAN PROPERTI PEMILIK
// ==========================================
const verificationList = ref([])
const loadingVerifications = ref(false)
const verificationSearchQuery = ref('')
const verificationStatusFilter = ref('all')
const reviewingUserId = ref(null)

const showRejectModal = ref(false)
const rejectingUser = ref(null)
const rejectReason = ref('')
const submittingReject = ref(false)

const showDocPreviewModal = ref(false)
const previewDocUser = ref(null)
const previewDocTab = ref('ktp')

const pendingVerifCount = computed(() => verificationList.value.filter(v => v.status_verifikasi === 'pending').length)
const verifiedVerifCount = computed(() => verificationList.value.filter(v => v.status_verifikasi === 'verified').length)
const rejectedVerifCount = computed(() => verificationList.value.filter(v => v.status_verifikasi === 'rejected').length)
const unverifiedVerifCount = computed(() => verificationList.value.filter(v => v.status_verifikasi === 'unverified').length)

let searchDebounceTimeout = null
const handleVerificationSearchDebounced = () => {
  clearTimeout(searchDebounceTimeout)
  searchDebounceTimeout = setTimeout(() => {
    fetchVerifications()
  }, 350)
}

const fetchVerifications = async () => {
  loadingVerifications.value = true
  try {
    const params = {}
    if (verificationStatusFilter.value !== 'all') {
      params.status = verificationStatusFilter.value
    }
    if (verificationSearchQuery.value) {
      params.q = verificationSearchQuery.value
    }
    const res = await api.get('/admin/verifications', { params })
    verificationList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat data verifikasi pemilik.'
  } finally {
    loadingVerifications.value = false
  }
}

const handleApproveVerification = async (user) => {
  const confirmed = await ui.confirm({
    title: 'Setujui Verifikasi Properti',
    message: `Apakah Anda yakin ingin menyetujui verifikasi kepemilikan untuk mitra "${user.nama}"? Setelah disetujui, akun pemilik ini dapat mengakses kelola kos dan kamar.`,
    confirmText: 'Ya, Setujui',
    cancelText: 'Batal',
    confirmType: 'primary',
  })
  if (!confirmed) return

  reviewingUserId.value = user.id
  try {
    const res = await api.patch(`/admin/verifications/${user.id}/status`, {
      status: 'verified',
    })
    ui.success(res.data.message || 'Verifikasi kepemilikan berhasil disetujui.')
    await fetchVerifications()
    fetchUsers()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal menyetujui verifikasi.')
  } finally {
    reviewingUserId.value = null
  }
}

const openRejectModal = (user) => {
  rejectingUser.value = user
  rejectReason.value = ''
  showRejectModal.value = true
}

const handleConfirmReject = async () => {
  if (!rejectingUser.value || !rejectReason.value.trim()) return
  submittingReject.value = true
  try {
    const res = await api.patch(`/admin/verifications/${rejectingUser.value.id}/status`, {
      status: 'rejected',
      alasan_penolakan: rejectReason.value.trim(),
    })
    ui.success(res.data.message || 'Pengajuan verifikasi berhasil ditolak dengan catatan.')
    showRejectModal.value = false
    rejectingUser.value = null
    rejectReason.value = ''
    await fetchVerifications()
    fetchUsers()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal memproses penolakan verifikasi.')
  } finally {
    submittingReject.value = false
  }
}

const openDocPreview = (user, tab = 'ktp') => {
  previewDocUser.value = user
  previewDocTab.value = tab
  showDocPreviewModal.value = true
}

onMounted(() => {
  fetchStats()
  fetchUsers()
  fetchVerifications()
  fetchKos()
  fetchAllBookings()
  fetchAdminBanners()
  startAdminPreviewSlider()
})

onUnmounted(() => {
  stopAdminPreviewSlider()
})
</script>
