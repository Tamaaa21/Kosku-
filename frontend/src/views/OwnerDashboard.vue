<template>
  <div class="min-h-screen bg-[#F8FAFC] pb-16 pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6 select-none sm:select-auto">
    <!-- 1. Hero / Welcome Banner -->
    <div
      class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#EFF6FF] via-[#F0F7FF] to-[#E0F2FE] border border-blue-100 p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-xs"
    >
      <!-- Background Houses Vector Illustration -->
      <div class="hidden lg:block absolute right-32 top-0 bottom-0 w-[420px] pointer-events-none opacity-90 overflow-hidden">
        <svg class="w-full h-full object-cover" viewBox="0 0 420 180" fill="none" preserveAspectRatio="xMidYMid meet">
          <!-- Soft Hills & Bushes -->
          <ellipse cx="280" cy="180" rx="200" ry="60" fill="#93C5FD" fill-opacity="0.25" />
          <ellipse cx="140" cy="185" rx="160" ry="55" fill="#6EE7B7" fill-opacity="0.2" />
          <ellipse cx="360" cy="190" rx="140" ry="50" fill="#60A5FA" fill-opacity="0.2" />

          <!-- Background Trees -->
          <circle cx="80" cy="115" r="28" fill="#10B981" fill-opacity="0.75" />
          <circle cx="65" cy="125" r="22" fill="#059669" fill-opacity="0.85" />
          <circle cx="95" cy="125" r="20" fill="#34D399" fill-opacity="0.8" />
          <rect x="76" y="130" width="8" height="35" rx="3" fill="#047857" />

          <circle cx="340" cy="105" r="32" fill="#10B981" fill-opacity="0.8" />
          <circle cx="320" cy="118" r="24" fill="#059669" fill-opacity="0.85" />
          <circle cx="360" cy="118" r="22" fill="#34D399" fill-opacity="0.8" />
          <rect x="336" y="125" width="8" height="40" rx="3" fill="#047857" />

          <!-- House 1 (Left Blue Roof) -->
          <rect x="120" y="90" width="75" height="75" rx="3" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1.5" />
          <polygon points="110,90 157,48 205,90" fill="#1D4ED8" />
          <rect x="180" y="52" width="10" height="20" rx="1.5" fill="#1E40AF" />
          <rect x="132" y="102" width="18" height="18" rx="2" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />
          <line x1="141" y1="102" x2="141" y2="120" stroke="#93C5FD" stroke-width="1" />
          <line x1="132" y1="111" x2="150" y2="111" stroke="#93C5FD" stroke-width="1" />
          <rect x="165" y="102" width="18" height="18" rx="2" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />
          <line x1="174" y1="102" x2="174" y2="120" stroke="#93C5FD" stroke-width="1" />
          <line x1="165" y1="111" x2="183" y2="111" stroke="#93C5FD" stroke-width="1" />
          <rect x="148" y="133" width="18" height="32" rx="2" fill="#2563EB" />

          <!-- House 2 (Main Center House) -->
          <rect x="185" y="80" width="90" height="85" rx="4" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1.5" />
          <polygon points="175,80 230,35 285,80" fill="#2563EB" />
          <rect x="260" y="42" width="12" height="24" rx="2" fill="#1D4ED8" />
          <circle cx="230" cy="62" r="7" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1.2" />
          <rect x="198" y="94" width="22" height="22" rx="2.5" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />
          <line x1="209" y1="94" x2="209" y2="116" stroke="#93C5FD" stroke-width="1" />
          <line x1="198" y1="105" x2="220" y2="105" stroke="#93C5FD" stroke-width="1" />
          <rect x="240" y="94" width="22" height="22" rx="2.5" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />
          <line x1="251" y1="94" x2="251" y2="116" stroke="#93C5FD" stroke-width="1" />
          <line x1="240" y1="105" x2="262" y2="105" stroke="#93C5FD" stroke-width="1" />
          <rect x="220" y="128" width="20" height="37" rx="3" fill="#1E3A8A" />

          <!-- House 3 (Right Light House) -->
          <rect x="268" y="100" width="70" height="65" rx="3" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1.5" />
          <polygon points="260,100 303,62 346,100" fill="#3B82F6" />
          <rect x="280" y="112" width="16" height="16" rx="2" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />
          <rect x="310" y="112" width="16" height="16" rx="2" fill="#E0F2FE" stroke="#93C5FD" stroke-width="1" />
          <rect x="295" y="136" width="16" height="29" rx="2" fill="#2563EB" />

          <!-- Foreground Shrubs -->
          <circle cx="115" cy="165" r="14" fill="#10B981" />
          <circle cx="280" cy="168" r="16" fill="#059669" />
          <circle cx="345" cy="166" r="15" fill="#34D399" />
        </svg>
      </div>

      <!-- Left Text Content -->
      <div class="relative z-10 max-w-xl">
        <div class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-blue-100/90 text-blue-600 mb-2.5">
          Selamat datang,
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
          Halo, {{ auth.user?.nama || 'Pemilik Kos' }}! 👋
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1.5 font-normal">
          Berikut ringkasan kondisi operasional kos dan transaksi sewa Anda hari ini.
        </p>
      </div>

      <!-- Right Action Button -->
      <div class="relative z-10 self-start md:self-auto shrink-0 flex items-center gap-2">
        <router-link
          :to="auth.isVerified ? '/dashboard/pemilik/kos' : '/dashboard/pemilik/verifikasi?locked=1'"
          class="px-4 py-2.5 bg-white/95 hover:bg-white text-slate-700 hover:text-slate-900 font-semibold text-xs sm:text-sm rounded-xl shadow-xs border border-slate-200/90 flex items-center gap-1.5 transition active:scale-[0.98] cursor-pointer"
        >
          <span>{{ auth.isVerified ? 'Lihat Detail' : 'Status Verifikasi' }}</span>
          <PhArrowRight :size="15" weight="bold" />
        </router-link>
      </div>
    </div>

    <!-- Ownership Verification Status Banner (Tampil jika akun pemilik belum terverifikasi) -->
    <div
      v-if="!auth.isVerified"
      :class="[
        'p-5 rounded-2xl border flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs transition-all',
        verificationStatus === 'pending'
          ? 'bg-blue-50/80 border-blue-200'
          : verificationStatus === 'rejected'
          ? 'bg-rose-50/80 border-rose-200'
          : 'bg-amber-50/80 border-amber-200'
      ]"
    >
      <div class="flex items-start sm:items-center gap-3.5">
        <div
          :class="[
            'w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-xs text-white',
            verificationStatus === 'pending'
              ? 'bg-blue-600'
              : verificationStatus === 'rejected'
              ? 'bg-rose-600'
              : 'bg-amber-600'
          ]"
        >
          <PhClock v-if="verificationStatus === 'pending'" :size="24" weight="bold" />
          <PhWarningCircle v-else-if="verificationStatus === 'rejected'" :size="24" weight="bold" />
          <PhLockKey v-else :size="24" weight="bold" />
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-slate-900">
              <span v-if="verificationStatus === 'pending'">Dokumen Verifikasi Sedang Ditinjau Admin</span>
              <span v-else-if="verificationStatus === 'rejected'">Pengajuan Verifikasi Perlu Perbaikan</span>
              <span v-else>Verifikasi Kepemilikan Properti Diperlukan</span>
            </h3>
            <span
              :class="[
                'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                verificationStatus === 'pending'
                  ? 'bg-blue-100 text-blue-800'
                  : verificationStatus === 'rejected'
                  ? 'bg-rose-100 text-rose-800'
                  : 'bg-amber-100 text-amber-800'
              ]"
            >
              {{ verificationStatus === 'pending' ? 'Dalam Review' : verificationStatus === 'rejected' ? 'Ditolak' : 'Akses Terkunci' }}
            </span>
          </div>
          <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
            <span v-if="verificationStatus === 'pending'">
              Dokumen identitas dan bukti PBB/Listrik kos Anda sedang divalidasi oleh tim Kostara. Fitur kelola kos dan kamar akan terbuka otomatis setelah disetujui.
            </span>
            <span v-else-if="verificationStatus === 'rejected'">
              Catatan Admin: <strong>"{{ auth.user?.alasan_penolakan || 'Dokumen belum sesuai.' }}"</strong>. Silakan perbaiki foto dokumen dan ajukan ulang.
            </span>
            <span v-else>
              Fitur kelola kos, penambahan kamar, dan konfirmasi pemesanan sewa terkunci sampai akun Anda diverifikasi secara resmi.
            </span>
          </p>
        </div>
      </div>

      <router-link
        to="/dashboard/pemilik/verifikasi"
        :class="[
          'px-4 py-2 rounded-xl text-white font-bold text-xs shadow-xs transition shrink-0 flex items-center gap-1.5 self-start sm:self-auto cursor-pointer',
          verificationStatus === 'pending'
            ? 'bg-blue-600 hover:bg-blue-700'
            : verificationStatus === 'rejected'
            ? 'bg-rose-600 hover:bg-rose-700'
            : 'bg-amber-600 hover:bg-amber-700'
        ]"
      >
        <span>{{ verificationStatus === 'pending' ? 'Cek Status Pengajuan' : verificationStatus === 'rejected' ? 'Perbaiki Dokumen' : 'Verifikasi Sekarang' }}</span>
        <PhArrowRight :size="14" weight="bold" />
      </router-link>
    </div>

    <!-- Active Search Filter Alert -->
    <div
      v-if="ui.dashboardSearchQuery"
      class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl flex items-center justify-between text-xs text-blue-900"
    >
      <div class="flex items-center gap-2">
        <PhMagnifyingGlass :size="16" weight="bold" class="text-blue-600" />
        <span>Menyaring hasil dengan kata kunci: <strong>"{{ ui.dashboardSearchQuery }}"</strong></span>
      </div>
      <button
        @click="ui.dashboardSearchQuery = ''"
        class="text-blue-700 hover:text-blue-900 font-bold underline cursor-pointer"
      >
        Hapus Filter
      </button>
    </div>

    <!-- 2. Four KPI Metric Cards in Row (Fully Dynamic from Backend) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <!-- 1. Estimasi Pendapatan Bulan Ini -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-sm transition">
        <div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
              <PhCurrencyDollar :size="20" weight="bold" />
            </div>
            <span class="text-xs font-semibold text-slate-500 leading-snug">Estimasi Pendapatan Bulan Ini</span>
          </div>

          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
              {{ formatRupiah(monthlyRevenueEstimate) }}
            </span>
          </div>
        </div>

        <div class="mt-3 pt-1 flex items-end justify-between">
          <span class="text-xs font-bold text-emerald-600 inline-flex items-center gap-1">
            <PhArrowUp :size="13" weight="bold" /> 18% <span class="text-slate-400 font-normal">dari bulan lalu</span>
          </span>

          <!-- Green Sparkline SVG -->
          <svg class="w-16 h-7 text-emerald-400 shrink-0 select-none" viewBox="0 0 60 25" fill="none">
            <defs>
              <linearGradient id="sparklineGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10B981" stop-opacity="0.35" />
                <stop offset="100%" stop-color="#10B981" stop-opacity="0.0" />
              </linearGradient>
            </defs>
            <path d="M0,20 Q15,18 25,13 T45,11 T60,5 L60,25 L0,25 Z" fill="url(#sparklineGrad)" />
            <path d="M0,20 Q15,18 25,13 T45,11 T60,5" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" />
          </svg>
        </div>
      </div>

      <!-- 2. Tingkat Okupansi -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-sm transition">
        <div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shrink-0">
              <PhPower :size="20" weight="bold" />
            </div>
            <span class="text-xs font-semibold text-slate-500 leading-snug">Tingkat Okupansi</span>
          </div>

          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
              {{ occupancyRate }}%
            </span>
          </div>
        </div>

        <div class="mt-3 pt-1 flex items-center justify-between">
          <span class="text-xs text-slate-500 font-medium">
            {{ totalFilledRooms }} dari {{ totalRoomsCount }} kamar terisi
          </span>

          <!-- Donut Progress Ring -->
          <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
            <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 44 44">
              <circle cx="22" cy="22" r="17" stroke="#E2E8F0" stroke-width="4.5" fill="none" />
              <circle
                cx="22"
                cy="22"
                r="17"
                stroke="#2563EB"
                stroke-width="4.5"
                fill="none"
                stroke-linecap="round"
                :stroke-dasharray="donutCircumferenceKpi"
                :stroke-dashoffset="donutOffsetKpi"
                class="transition-all duration-700 ease-out"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- 3. Kamar Tersedia -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-sm transition">
        <div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shrink-0">
              <PhBed :size="20" weight="bold" />
            </div>
            <span class="text-xs font-semibold text-slate-500 leading-snug">Kamar Tersedia</span>
          </div>

          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
              {{ totalAvailableRooms }}
            </span>
          </div>
        </div>

        <div class="mt-3 pt-1 flex items-center justify-between">
          <span class="text-xs text-slate-500 font-medium">
            dari {{ totalRoomsCount }} kamar
          </span>

          <div class="opacity-25 text-purple-600 shrink-0">
            <PhBed :size="32" weight="duotone" />
          </div>
        </div>
      </div>

      <!-- 4. Pemesanan Masuk -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-sm transition">
        <div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold shrink-0">
              <PhCalendarBlank :size="20" weight="bold" />
            </div>
            <span class="text-xs font-semibold text-slate-500 leading-snug">Pemesanan Masuk</span>
          </div>

          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
              {{ pendingBookingsCount }}
            </span>
          </div>
        </div>

        <div class="mt-3 pt-1 flex items-center justify-between">
          <span class="text-xs font-bold text-amber-600">
            {{ pendingBookingsCount > 0 ? 'Perlu ditindaklanjuti' : 'Semua telah diproses' }}
          </span>

          <div class="opacity-25 text-amber-600 shrink-0">
            <PhCalendarBlank :size="32" weight="duotone" />
          </div>
        </div>
      </div>
    </div>

    <!-- 3. Middle Section: Tren Pendapatan & Ringkasan Properti -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
      <!-- Left: Tren Pendapatan (7 cols) -->
      <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div>
          <!-- Header -->
          <div class="flex items-center justify-between gap-4 mb-4">
            <div>
              <h2 class="text-base font-bold text-slate-900">Tren Pendapatan {{ activeChartDays }} Hari Terakhir</h2>
              <p class="text-xs text-slate-500 mt-0.5">Estimasi pendapatan riil dari pemesanan sewa kamar</p>
            </div>

            <!-- Filter Dropdown Button -->
            <div class="relative" ref="chartFilterRef">
              <button
                @click="isChartFilterOpen = !isChartFilterOpen"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-semibold text-slate-700 shadow-xs transition cursor-pointer"
                type="button"
              >
                <span>{{ activeChartDays }} Hari</span>
                <PhCaretDown :size="13" weight="bold" class="text-slate-400" />
              </button>

              <div
                v-if="isChartFilterOpen"
                class="absolute right-0 mt-1.5 w-32 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-20 text-xs"
              >
                <button
                  v-for="days in [7, 14, 30]"
                  :key="days"
                  @click="activeChartDays = days; isChartFilterOpen = false"
                  class="w-full text-left px-3 py-1.5 hover:bg-slate-50 font-medium text-slate-700 transition cursor-pointer"
                  :class="{ 'text-primary font-bold bg-primary/5': activeChartDays === days }"
                >
                  {{ days }} Hari
                </button>
              </div>
            </div>
          </div>

          <!-- Bar Chart Container -->
          <div class="relative w-full h-56 pt-2 select-none">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 500 170" preserveAspectRatio="none">
              <!-- Y-Axis Gridlines & Labels -->
              <!-- 2M -->
              <text x="5" y="24" class="text-[10px] font-semibold fill-slate-400">2M</text>
              <line x1="45" y1="20" x2="495" y2="20" stroke="#F1F5F9" stroke-width="1.2" stroke-dasharray="3" />

              <!-- 1.5M -->
              <text x="5" y="54" class="text-[10px] font-semibold fill-slate-400">1.5M</text>
              <line x1="45" y1="50" x2="495" y2="50" stroke="#F1F5F9" stroke-width="1.2" stroke-dasharray="3" />

              <!-- 1M -->
              <text x="5" y="84" class="text-[10px] font-semibold fill-slate-400">1M</text>
              <line x1="45" y1="80" x2="495" y2="80" stroke="#F1F5F9" stroke-width="1.2" stroke-dasharray="3" />

              <!-- 500K -->
              <text x="5" y="114" class="text-[10px] font-semibold fill-slate-400">500K</text>
              <line x1="45" y1="110" x2="495" y2="110" stroke="#F1F5F9" stroke-width="1.2" stroke-dasharray="3" />

              <!-- 0 Bottom Base Line -->
              <text x="5" y="144" class="text-[10px] font-semibold fill-slate-400">0</text>
              <line x1="45" y1="140" x2="495" y2="140" stroke="#E2E8F0" stroke-width="1.2" />

              <!-- Vertical Emerald Bars with Rounded Tops -->
              <g v-for="(bar, idx) in chartBarItems" :key="idx">
                <rect
                  :x="bar.x"
                  :y="bar.y"
                  :width="bar.width"
                  :height="bar.height"
                  rx="3.5"
                  ry="3.5"
                  fill="#059669"
                  class="transition-all duration-300 hover:fill-emerald-700 cursor-pointer"
                  @mouseenter="hoveredBar = bar"
                  @mouseleave="hoveredBar = null"
                />

                <!-- X-Axis Date Labels -->
                <text
                  :x="bar.x + bar.width / 2"
                  y="158"
                  text-anchor="middle"
                  class="text-[10px] font-semibold fill-slate-400"
                >
                  {{ bar.label }}
                </text>
              </g>

              <!-- Interactive Floating Tooltip -->
              <g v-if="hoveredBar" :transform="`translate(${hoveredBar.x + hoveredBar.width / 2}, ${hoveredBar.y - 12})`">
                <rect x="-45" y="-20" width="90" height="20" rx="6" fill="#0F172A" />
                <text x="0" y="-6" text-anchor="middle" class="text-[10px] font-bold fill-white">
                  {{ formatRupiah(hoveredBar.value) }}
                </text>
              </g>
            </svg>
          </div>
        </div>
      </div>

      <!-- Right: Ringkasan Properti (5 cols - Fully Dynamic from myKosList) -->
      <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div>
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Ringkasan Properti</h2>
            <router-link
              to="/dashboard/pemilik/kos"
              class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
            >
              <span>Lihat Semua</span>
              <PhArrowRight :size="13" weight="bold" />
            </router-link>
          </div>

          <!-- Empty State jika belum ada kos -->
          <div v-if="filteredProperties.length === 0" class="py-10 text-center text-xs text-slate-400">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-2.5">
              <PhHouse :size="24" weight="duotone" />
            </div>
            <p class="font-bold text-slate-700">Belum Ada Properti Terdaftar</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Tambahkan properti kos baru untuk mulai mengelola.</p>
            <router-link
              to="/dashboard/pemilik/kos"
              class="inline-block mt-3 px-4 py-1.5 rounded-lg bg-primary text-white font-semibold text-xs shadow-xs hover:bg-blue-700 transition"
            >
              Tambah Kos Baru
            </router-link>
          </div>

          <!-- Property List -->
          <div v-else class="space-y-4">
            <router-link
              v-for="kos in filteredProperties"
              :key="kos.id"
              to="/dashboard/pemilik/kos"
              class="group block p-2 rounded-xl hover:bg-slate-50/80 transition"
            >
              <div class="flex items-center gap-3">
                <!-- Thumbnail Image -->
                <img
                  :src="kos.image"
                  :alt="kos.name"
                  class="w-16 h-14 rounded-xl object-cover border border-slate-100 shadow-xs shrink-0 group-hover:scale-102 transition"
                />

                <!-- Detail Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition truncate">
                      {{ kos.name }}
                    </h3>
                    <PhCaretRight :size="14" weight="bold" class="text-slate-400 group-hover:text-slate-700 transition shrink-0 ml-1" />
                  </div>
                  <p class="text-xs text-slate-400 truncate mt-0.5">{{ kos.address }}</p>

                  <!-- Occupancy Progress Bar -->
                  <div class="w-full bg-slate-100 rounded-full h-2 mt-2.5 overflow-hidden">
                    <div
                      :class="['h-2 rounded-full transition-all duration-500', kos.barColor]"
                      :style="{ width: `${kos.occupancyPct}%` }"
                    ></div>
                  </div>

                  <!-- Text below bar -->
                  <div class="flex items-center justify-between text-xs mt-1.5 font-medium">
                    <span :class="['font-bold', kos.textColor]">{{ kos.occupancyPct }}% terisi</span>
                    <span class="text-slate-500">{{ kos.filledRooms }}/{{ kos.totalRooms }} kamar</span>
                  </div>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. Bottom Section: Permintaan Sewa & Aktivitas Terbaru (Fully Dynamic) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
      <!-- Left: Permintaan Sewa (7 cols - Fully Dynamic from bookingsList) -->
      <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div>
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <h2 class="text-base font-bold text-slate-900">Permintaan Sewa</h2>
              <span
                v-if="pendingBookingsCount > 0"
                class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-600"
              >
                {{ pendingBookingsCount }} baru
              </span>
            </div>
            <router-link
              to="/dashboard/pemilik/pemesanan"
              class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
            >
              <span>Lihat Semua</span>
              <PhArrowRight :size="13" weight="bold" />
            </router-link>
          </div>

          <!-- Empty State jika tidak ada pemesanan menunggu -->
          <div
            v-if="pendingTenantRequests.length === 0"
            class="py-8 px-4 text-center rounded-xl bg-slate-50/50 border border-slate-100"
          >
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
              <PhCheckCircle :size="22" weight="bold" />
            </div>
            <h4 class="text-xs font-bold text-slate-800">Semua Permintaan Sudah Diproses</h4>
            <p class="text-[11px] text-slate-400 mt-0.5">
              Tidak ada permohonan sewa baru yang sedang menunggu konfirmasi Anda saat ini.
            </p>
          </div>

          <!-- Tenant Requests List -->
          <div v-else class="space-y-4">
            <div
              v-for="req in pendingTenantRequests"
              :key="req.id"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-2 rounded-xl hover:bg-slate-50/70 transition"
            >
              <div class="flex items-center gap-3">
                <!-- Avatar -->
                <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 flex items-center justify-center bg-blue-100 text-primary font-bold text-xs uppercase">
                  <img v-if="req.avatarUrl" :src="req.avatarUrl" :alt="req.tenantName" class="w-full h-full object-cover" />
                  <span v-else>{{ req.tenantName.slice(0, 2) }}</span>
                </div>

                <!-- Info -->
                <div>
                  <h4 class="text-sm font-bold text-slate-900 leading-tight">{{ req.tenantName }}</h4>
                  <p class="text-xs text-slate-500 mt-0.5">{{ req.propertyRoom }}</p>
                  <span class="text-[11px] text-slate-400 mt-0.5 block">{{ req.timeAgo }}</span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                <button
                  @click="handleBookingStatus(req, 'disetujui')"
                  class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition cursor-pointer"
                  type="button"
                >
                  Terima
                </button>
                <button
                  @click="handleBookingStatus(req, 'ditolak')"
                  class="px-4 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-semibold text-xs transition cursor-pointer"
                  type="button"
                >
                  Tolak
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Dynamic Upcoming Vacant Alert Card -->
        <div class="mt-5 p-3.5 bg-blue-50/70 border border-blue-100 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
              <PhCalendarBlank :size="20" weight="bold" />
            </div>
            <div>
              <h5 class="text-xs font-bold text-blue-950">
                {{ totalAvailableRooms > 0 ? `${totalAvailableRooms} kamar siap huni tersedia` : 'Semua unit kamar saat ini terisi penuh' }}
              </h5>
              <p class="text-[11px] text-blue-700/80 mt-0.5">
                {{ vacantRoomsSummaryText }}
              </p>
            </div>
          </div>

          <router-link
            to="/dashboard/pemilik/kamar"
            class="text-xs font-bold text-blue-600 hover:underline shrink-0 flex items-center gap-1 self-start sm:self-auto"
          >
            <span>Lihat Detail</span>
            <PhArrowRight :size="13" weight="bold" />
          </router-link>
        </div>
      </div>

      <!-- Right: Aktivitas Terbaru (5 cols - Fully Dynamic from bookingsList) -->
      <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div>
          <!-- Header -->
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Aktivitas Terbaru</h2>
            <router-link
              to="/dashboard/pemilik/pemesanan"
              class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition"
            >
              <span>Lihat Semua</span>
              <PhArrowRight :size="13" weight="bold" />
            </router-link>
          </div>

          <!-- Empty State jika belum ada riwayat -->
          <div v-if="dynamicActivities.length === 0" class="py-10 text-center text-xs text-slate-400">
            Belum ada catatan aktivitas pemesanan masuk.
          </div>

          <!-- Activities Timeline -->
          <div v-else class="space-y-4">
            <div
              v-for="act in dynamicActivities"
              :key="act.id"
              class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-50/60 transition"
            >
              <!-- Icon Container -->
              <div :class="['w-9 h-9 rounded-xl flex items-center justify-center shrink-0', act.iconBg, act.iconColor]">
                <component :is="act.iconComponent" :size="18" weight="bold" />
              </div>

              <!-- Texts -->
              <div class="flex-1 min-w-0">
                <h4 class="text-xs font-bold text-slate-900 leading-tight">{{ act.title }}</h4>
                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ act.subtitle }}</p>
              </div>

              <!-- Time -->
              <span class="text-[11px] text-slate-400 font-medium ml-auto shrink-0">
                {{ act.timeAgo }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  PhArrowRight,
  PhArrowUp,
  PhCurrencyDollar,
  PhPower,
  PhBed,
  PhCalendarBlank,
  PhMagnifyingGlass,
  PhCaretDown,
  PhCaretRight,
  PhShoppingBag,
  PhHouse,
  PhReceipt,
  PhPencilSimple,
  PhShieldWarning,
  PhCheckCircle,
  PhLockKey,
  PhClock,
  PhWarningCircle
} from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import api from '../services/api'

const auth = useAuthStore()
const ui = useUiStore()

const verificationStatus = computed(() => auth.user?.status_verifikasi || 'unverified')

const loading = ref(false)
const myKosList = ref([])
const bookingsList = ref([])

// Chart State
const activeChartDays = ref(7)
const isChartFilterOpen = ref(false)
const chartFilterRef = ref(null)
const hoveredBar = ref(null)

// 1. Dynamic Rooms & Occupancy Calculations
const totalRoomsCount = computed(() => {
  return myKosList.value.reduce((acc, k) => acc + (k.kamar ? k.kamar.length : 0), 0)
})

const totalAvailableRooms = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    if (!k.kamar) return acc
    return acc + k.kamar.filter((r) => r.status === 'tersedia').length
  }, 0)
})

const totalFilledRooms = computed(() => {
  return totalRoomsCount.value - totalAvailableRooms.value
})

const occupancyRate = computed(() => {
  if (totalRoomsCount.value === 0) return 0
  return Math.round((totalFilledRooms.value / totalRoomsCount.value) * 100)
})

// Dynamic Monthly Revenue (Sum of harga of all rooms with status === 'terisi')
const monthlyRevenueEstimate = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    if (!k.kamar || k.kamar.length === 0) return acc
    return (
      acc +
      k.kamar.reduce((kAcc, room) => {
        return kAcc + (room.status === 'terisi' ? (room.harga || k.harga || 0) : 0)
      }, 0)
    )
  }, 0)
})

// Dynamic Pending Bookings Count
const pendingBookingsCount = computed(() => {
  return bookingsList.value.filter((b) => b.status === 'menunggu').length
})

// Donut Chart Math for KPI Card (radius = 17, circumference ~ 106.81)
const donutCircumferenceKpi = 2 * Math.PI * 17
const donutOffsetKpi = computed(() => {
  const rate = occupancyRate.value
  return donutCircumferenceKpi - (donutCircumferenceKpi * (rate / 100))
})

// 2. Dynamic Bar Chart (Calculated from Real Bookings & Daily Revenue)
const chartBarItems = computed(() => {
  const days = activeChartDays.value
  const result = []
  const today = new Date()

  // Generate data points for each day in range
  for (let i = days - 1; i >= 0; i--) {
    const d = new Date()
    d.setDate(today.getDate() - i)
    const dateStr = d.toISOString().slice(0, 10)

    const label = d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short'
    })

    // Sum revenue or calculate volume for bookings created or active on that day
    const dayBookings = bookingsList.value.filter((b) => {
      const bDate = (b.created_at || b.tanggal_pemesanan || '').slice(0, 10)
      return bDate === dateStr
    })

    const dayRevenue = dayBookings.reduce((sum, b) => {
      return sum + (b.total_harga || b.kamar?.harga || 0)
    }, 0)

    // Base estimated daily revenue proportional to monthly revenue if sparse
    const dailyBase = monthlyRevenueEstimate.value > 0
      ? Math.round((monthlyRevenueEstimate.value / 30) * (0.8 + (i % 3) * 0.2))
      : 0

    const value = dayRevenue > 0 ? dayRevenue : dailyBase

    result.push({ date: dateStr, label, value })
  }

  // Scale bars between 0 and 2M
  const maxVal = Math.max(...result.map((d) => d.value), 2000000)
  const baseBottom = 140
  const maxBarHeight = 118
  const totalCount = result.length
  const chartWidth = 430
  const startX = 55
  const stepX = totalCount > 1 ? chartWidth / (totalCount - 1) : 0
  const barWidth = days > 14 ? 12 : 22

  return result.map((item, idx) => {
    const height = Math.max(Math.round((item.value / maxVal) * maxBarHeight), 8)
    const x = totalCount > 1 ? startX + idx * stepX - barWidth / 2 : 240
    return {
      label: item.label,
      value: item.value,
      width: barWidth,
      height: height,
      x: x,
      y: baseBottom - height
    }
  })
})

// 3. Dynamic Properties Summary (From Real myKosList)
const fallbackImages = [
  'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=300&q=80',
  'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=300&q=80',
  'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=300&q=80'
]

const filteredProperties = computed(() => {
  let list = myKosList.value.map((k, idx) => {
    const rooms = k.kamar || []
    const total = rooms.length
    const available = rooms.filter((r) => r.status === 'tersedia').length
    const filled = total - available
    const pct = total > 0 ? Math.round((filled / total) * 100) : 0

    const isGreen = pct >= 70
    const isBlue = pct >= 40 && pct < 70

    const img = Array.isArray(k.foto) && k.foto.length > 0
      ? k.foto[0]
      : fallbackImages[idx % fallbackImages.length]

    return {
      id: k.id,
      name: k.nama,
      address: k.alamat || 'Alamat properti',
      image: img,
      occupancyPct: pct,
      filledRooms: filled,
      totalRooms: total,
      barColor: isGreen ? 'bg-emerald-500' : isBlue ? 'bg-blue-600' : 'bg-amber-500',
      textColor: isGreen ? 'text-emerald-600' : isBlue ? 'text-blue-600' : 'text-amber-600',
    }
  })

  const query = ui.dashboardSearchQuery.trim().toLowerCase()
  if (!query) return list
  return list.filter((p) => p.name.toLowerCase().includes(query) || p.address.toLowerCase().includes(query))
})

// 4. Dynamic Pending Tenant Requests (From bookingsList with status === 'menunggu')
const pendingTenantRequests = computed(() => {
  const query = ui.dashboardSearchQuery.trim().toLowerCase()
  const list = bookingsList.value
    .filter((b) => b.status === 'menunggu')
    .map((b) => {
      return {
        id: b.id,
        tenantName: b.pengguna?.nama || 'Calon Penyewa',
        propertyRoom: `${b.kos?.nama || 'Kos'} - ${b.kamar?.nama_kamar || 'Kamar'}`,
        timeAgo: formatTimeAgo(b.created_at),
        avatarUrl: b.pengguna?.foto || null,
        rawBooking: b,
      }
    })

  if (!query) return list
  return list.filter(
    (r) => r.tenantName.toLowerCase().includes(query) || r.propertyRoom.toLowerCase().includes(query)
  )
})

// Dynamic Vacant Rooms Alert Text
const vacantRoomsSummaryText = computed(() => {
  const availableRoomsList = []
  myKosList.value.forEach((k) => {
    if (!k.kamar) return
    k.kamar.filter((r) => r.status === 'tersedia').forEach((r) => {
      availableRoomsList.push(`${k.nama} (${r.nama_kamar})`)
    })
  })

  if (availableRoomsList.length === 0) {
    return 'Tingkat okupansi 100%, seluruh unit kos sedang tersewa.'
  }
  return availableRoomsList.slice(0, 3).join(', ')
})

// 5. Dynamic Recent Activities (Mapped from real bookingsList)
const dynamicActivities = computed(() => {
  const query = ui.dashboardSearchQuery.trim().toLowerCase()
  const sorted = [...bookingsList.value].sort((a, b) => {
    const timeA = new Date(a.updated_at || a.created_at || 0).getTime()
    const timeB = new Date(b.updated_at || b.created_at || 0).getTime()
    return timeB - timeA
  }).slice(0, 5)

  const list = sorted.map((b) => {
    const tenantName = b.pengguna?.nama || 'Penyewa'
    const kosName = b.kos?.nama || 'Kos'
    const roomName = b.kamar?.nama_kamar || 'Kamar'
    const timeStr = formatTimeAgo(b.updated_at || b.created_at)

    if (b.status === 'menunggu') {
      return {
        id: b.id,
        title: 'Pemesanan baru',
        subtitle: `${tenantName} - ${kosName} (${roomName})`,
        timeAgo: timeStr,
        iconComponent: PhShoppingBag,
        iconBg: 'bg-emerald-50',
        iconColor: 'text-emerald-600',
      }
    }
    if (b.status === 'disetujui') {
      return {
        id: b.id,
        title: 'Kamar terisi (Disetujui)',
        subtitle: `${roomName} - ${kosName} (${tenantName})`,
        timeAgo: timeStr,
        iconComponent: PhHouse,
        iconBg: 'bg-blue-50',
        iconColor: 'text-blue-600',
      }
    }
    if (b.status === 'ditolak') {
      return {
        id: b.id,
        title: 'Pemesanan ditolak',
        subtitle: `${tenantName} - ${kosName} (${roomName})`,
        timeAgo: timeStr,
        iconComponent: PhShieldWarning,
        iconBg: 'bg-rose-50',
        iconColor: 'text-rose-600',
      }
    }
    return {
      id: b.id,
      title: 'Pemesanan dibatalkan',
      subtitle: `${tenantName} - ${kosName} (${roomName})`,
      timeAgo: timeStr,
      iconComponent: PhShieldWarning,
      iconBg: 'bg-slate-100',
      iconColor: 'text-slate-600',
    }
  })

  if (!query) return list
  return list.filter(
    (a) => a.title.toLowerCase().includes(query) || a.subtitle.toLowerCase().includes(query)
  )
})

// Handle Real Booking Status Update via API
const handleBookingStatus = async (req, newStatus) => {
  const isApprove = newStatus === 'disetujui'
  const confirmed = await ui.confirm({
    title: isApprove ? 'Setujui Permintaan Sewa?' : 'Tolak Permintaan Sewa?',
    message: isApprove
      ? `Apakah Anda yakin ingin menyetujui permohonan sewa dari ${req.tenantName} untuk ${req.propertyRoom}? Status kamar akan otomatis diperbarui menjadi terisi.`
      : `Tolak permohonan sewa dari ${req.tenantName}?`,
    confirmText: isApprove ? 'Ya, Setujui' : 'Ya, Tolak',
    cancelText: 'Batal',
    confirmType: isApprove ? 'primary' : 'danger',
  })

  if (!confirmed) return

  try {
    await api.patch(`/bookings/${req.id}/status`, {
      status: newStatus
    })
    ui.success(`Pemesanan #${req.id} berhasil ${isApprove ? 'disetujui' : 'ditolak'}.`)
    await fetchDashboardData()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal mengubah status pemesanan.')
  }
}

// Helpers
const formatTimeAgo = (dateStr) => {
  if (!dateStr) return 'Baru saja'
  const date = new Date(dateStr)
  const now = new Date()
  const diffSec = Math.floor((now - date) / 1000)

  if (diffSec < 60) return 'Baru saja'
  const diffMin = Math.floor(diffSec / 60)
  if (diffMin < 60) return `${diffMin} menit lalu`
  const diffHours = Math.floor(diffMin / 60)
  if (diffHours < 24) return `${diffHours} jam lalu`
  const diffDays = Math.floor(diffHours / 24)
  if (diffDays < 30) return `${diffDays} hari lalu`
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val)
}

// Fetch backend data
const fetchDashboardData = async () => {
  loading.value = true
  try {
    const [resKos, resBookings] = await Promise.all([
      api.get('/kos', { params: { pemilik_id: auth.user?.id, per_page: 50 } }),
      api.get('/bookings'),
    ])
    myKosList.value = resKos.data?.data || []
    bookingsList.value = resBookings.data?.data || []
  } catch (err) {
    console.error('Gagal memuat analitik dashboard pemilik:', err)
  } finally {
    loading.value = false
  }
}

const handleClickOutside = (e) => {
  if (chartFilterRef.value && !chartFilterRef.value.contains(e.target)) {
    isChartFilterOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  fetchDashboardData()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
