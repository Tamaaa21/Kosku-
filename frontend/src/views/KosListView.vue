<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- ======================================================== -->
    <!-- TOP STICKY TOOLBAR: SEARCH, QUICK PILLS & FILTER TRIGGER -->
    <!-- ======================================================== -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-slate-100 p-space-md mb-space-md space-y-3">
      <!-- Row 1: Search Input & Filter Modal Trigger -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
        <!-- Main Search Input with integrated Status Spinner, Clear button & Action button -->
        <div class="relative flex-1 flex items-center group">
          <div class="absolute left-4 flex items-center pointer-events-none text-[#017E91]">
            <PhMagnifyingGlass :size="20" weight="bold" />
          </div>
          <input
            v-model="filters.q"
            @input="debounceFetch"
            @keydown.enter="fetchKosList"
            type="text"
            placeholder="Cari kos berdasarkan nama, kota, alamat, atau kampus..."
            class="w-full h-12 pl-11 pr-24 rounded-2xl bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 border border-slate-200/80 focus:border-[#017E91] focus:ring-2 focus:ring-[#017E91]/20 transition-all outline-none"
          />

          <!-- Inside input: Loading spinner, Clear (X), and Search Button -->
          <div class="absolute right-2.5 flex items-center gap-1.5">
            <PhSpinnerGap
              v-if="loading"
              :size="18"
              weight="bold"
              class="animate-spin text-[#017E91]"
            />
            <button
              v-if="filters.q"
              @click="clearSearchQuery"
              type="button"
              class="p-1 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition cursor-pointer"
              title="Hapus pencarian (Esc)"
            >
              <PhX :size="15" weight="bold" />
            </button>
            <button
              @click="fetchKosList"
              type="button"
              class="px-3.5 py-1.5 rounded-xl bg-[#017E91] hover:bg-[#016877] text-white text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1"
            >
              <span>Cari</span>
            </button>
          </div>
        </div>

        <!-- Action Controls: Pop-up Filter Button & Sort Selector -->
        <div class="flex items-center gap-2 shrink-0">
          <!-- Pop-up Filter Trigger Button (Modal Pop-Up untuk Desktop & Mobile) -->
          <button
            @click="showFilterModal = true"
            type="button"
            class="inline-flex items-center justify-center gap-2 h-12 px-4.5 rounded-2xl text-xs font-bold transition-all border cursor-pointer select-none shadow-xs active:scale-95"
            :class="activeFilterCount > 0
              ? 'bg-[#017E91] border-[#017E91] text-white hover:bg-[#016877]'
              : 'bg-slate-50 border-slate-200/80 text-slate-700 hover:bg-slate-100 hover:border-slate-300'"
            title="Buka Filter Lengkap (Pop-up)"
          >
            <PhSlidersHorizontal :size="18" weight="bold" :class="activeFilterCount > 0 ? 'text-white' : 'text-[#017E91]'" />
            <span>Filter Lengkap</span>
            <span
              v-if="activeFilterCount > 0"
              class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white text-[#017E91]"
            >
              {{ activeFilterCount }}
            </span>
          </button>

          <!-- Sort Selector Dropdown -->
          <div class="flex items-center gap-1.5 h-12 bg-slate-50 px-3 rounded-2xl border border-slate-200/80">
            <label class="text-[11px] text-slate-500 font-medium whitespace-nowrap hidden sm:inline">Urutan:</label>
            <select
              v-model="filters.sort"
              @change="fetchKosList"
              class="text-xs bg-transparent border-none focus:ring-0 py-1 pl-1 pr-6 text-slate-800 font-bold cursor-pointer"
            >
              <option value="">Rekomendasi</option>
              <option value="harga_asc">Harga Terendah</option>
              <option value="harga_desc">Harga Tertinggi</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Row 2: Quick Category Pills & Realtime Count -->
      <div class="flex items-center justify-between gap-3 pt-1 border-t border-slate-100 flex-wrap sm:flex-nowrap">
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 w-full sm:w-auto">
          <span class="text-[11px] text-slate-400 font-semibold mr-1 shrink-0 flex items-center gap-1 select-none">
            <PhSparkle :size="13" weight="fill" class="text-[#E68202]" />
            Kategori:
          </span>
          <button
            v-for="preset in quickTypePresets"
            :key="preset.value"
            @click="setQuickJenis(preset.value)"
            type="button"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all cursor-pointer select-none',
              filters.jenis === preset.value
                ? 'bg-[#017E91] text-white shadow-xs font-bold'
                : 'bg-slate-100/70 text-slate-600 hover:bg-slate-200/70 hover:text-slate-900 border border-slate-200/40'
            ]"
          >
            {{ preset.label }}
          </button>

          <!-- Quick Toggle: Kamar Tersedia -->
          <button
            @click="toggleQuickTersedia"
            type="button"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all cursor-pointer select-none flex items-center gap-1',
              filters.tersedia
                ? 'bg-emerald-600 text-white shadow-xs font-bold'
                : 'bg-slate-100/70 text-slate-600 hover:bg-slate-200/70 hover:text-slate-900 border border-slate-200/40'
            ]"
          >
            <PhCheckCircle :size="13" weight="bold" />
            <span>Kamar Tersedia</span>
          </button>
        </div>

        <!-- Realtime Result Summary -->
        <div class="text-[11px] text-slate-500 shrink-0 hidden sm:block">
          Ditemukan <span class="font-extrabold text-[#017E91]">{{ kosList.length }}</span> properti kos
        </div>
      </div>

      <!-- Row 3: Active Filter Chips (Muncul jika ada filter aktif) -->
      <div v-if="activeFilterCount > 0" class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
        <span class="text-[11px] text-slate-400 font-semibold mr-1">Filter Aktif:</span>

        <!-- Chip Kata Kunci -->
        <span
          v-if="filters.q"
          class="inline-flex items-center gap-1.5 bg-[#017E91]/10 text-[#017E91] border border-[#017E91]/20 px-2.5 py-1 rounded-xl text-xs font-semibold"
        >
          <PhMagnifyingGlass :size="12" weight="bold" />
          <span>"{{ filters.q }}"</span>
          <button @click="removeFilter('q')" class="hover:text-red-600 transition cursor-pointer" title="Hapus pencarian ini">
            <PhX :size="13" weight="bold" />
          </button>
        </span>

        <!-- Chip Jenis Kos -->
        <span
          v-if="filters.jenis"
          class="inline-flex items-center gap-1.5 bg-amber-50 text-[#E68202] border border-amber-200/70 px-2.5 py-1 rounded-xl text-xs font-bold"
        >
          <span>{{ formatJenis(filters.jenis) }}</span>
          <button @click="removeFilter('jenis')" class="hover:text-red-600 transition cursor-pointer" title="Hapus filter ini">
            <PhX :size="13" weight="bold" />
          </button>
        </span>

        <!-- Chip Rentang Harga Maks -->
        <span
          v-if="filters.harga_max"
          class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 border border-slate-200/80 px-2.5 py-1 rounded-xl text-xs font-medium"
        >
          <span>Maks: {{ formatRupiah(filters.harga_max) }}</span>
          <button @click="removeFilter('harga_max')" class="hover:text-red-600 transition cursor-pointer" title="Hapus filter ini">
            <PhX :size="13" weight="bold" />
          </button>
        </span>

        <!-- Chips Fasilitas -->
        <span
          v-for="fasId in filters.fasilitas"
          :key="fasId"
          class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200/70 px-2.5 py-1 rounded-xl text-xs font-medium"
        >
          <PhCheck :size="12" weight="bold" class="text-emerald-600" />
          <span>{{ getFasilitasName(fasId) }}</span>
          <button @click="removeFilter('fasilitas', fasId)" class="hover:text-red-600 transition cursor-pointer" title="Hapus filter ini">
            <PhX :size="13" weight="bold" />
          </button>
        </span>

        <!-- Chip Hanya Tersedia -->
        <span
          v-if="filters.tersedia"
          class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/70 px-2.5 py-1 rounded-xl text-xs font-medium"
        >
          <span>Kamar Tersedia</span>
          <button @click="removeFilter('tersedia')" class="hover:text-red-600 transition cursor-pointer" title="Hapus filter ini">
            <PhX :size="13" weight="bold" />
          </button>
        </span>

        <!-- Tombol Reset Semua Filter -->
        <button
          @click="resetFilters"
          type="button"
          class="inline-flex items-center gap-1 text-[11px] text-red-600 hover:text-red-700 font-bold ml-1 hover:underline cursor-pointer"
        >
          <PhArrowClockwise :size="13" weight="bold" />
          <span>Reset Semua</span>
        </button>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- MAIN LISTINGS CONTENT: FULL-WIDTH LUXURIOUS 4-COLUMN GRID -->
    <!-- ======================================================== -->
    <section class="w-full space-y-space-md min-w-0">
      <!-- Loading State Skeletons (Full 4-column) -->
      <div
        v-if="loading"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-space-md transition-all duration-300"
      >
        <div
          v-for="i in 8"
          :key="i"
          class="h-88 bg-white rounded-2xl border border-slate-100 animate-pulse flex flex-col p-3 space-y-3 shadow-xs"
        >
          <div class="w-full aspect-[4/3] bg-slate-100 rounded-xl"></div>
          <div class="h-4 bg-slate-100 rounded w-3/4"></div>
          <div class="h-3 bg-slate-100 rounded w-1/2"></div>
          <div class="flex-1"></div>
          <div class="h-8 bg-slate-100 rounded-lg"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="kosList.length === 0"
        class="bg-surface-container-lowest rounded-3xl p-12 text-center border border-slate-100 shadow-xs flex flex-col items-center justify-center space-y-4 my-6"
      >
        <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
          <PhMagnifyingGlass :size="40" />
        </div>
        <div class="space-y-1">
          <h3 class="text-base font-bold text-on-surface">Tidak Ada Kos yang Cocok</h3>
          <p class="text-xs text-on-surface-variant max-w-sm mx-auto">
            Kami tidak menemukan kos yang memenuhi kriteria pencarian Anda. Coba kurangi filter atau cari kata kunci lain.
          </p>
        </div>

        <!-- Quick suggestions -->
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
          <span class="text-xs text-slate-400 font-medium">Saran pencarian:</span>
          <button
            v-for="sug in popularSuggestions"
            :key="sug"
            @click="applySuggestion(sug)"
            type="button"
            class="px-2.5 py-1 text-xs font-semibold bg-slate-100 hover:bg-[#017E91]/10 text-slate-700 hover:text-[#017E91] rounded-lg transition cursor-pointer"
          >
            {{ sug }}
          </button>
        </div>

        <button
          @click="resetFilters"
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#017E91] hover:bg-[#016877] text-white text-xs font-bold transition shadow-xs cursor-pointer active:scale-95"
        >
          <PhArrowClockwise :size="14" weight="bold" />
          <span>Reset Semua Filter</span>
        </button>
      </div>

      <!-- Cards Grid: Full-Width 4-Columns Desktop -->
      <div
        v-else
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-space-md transition-all duration-300"
      >
        <article
          v-for="kos in kosList"
          :key="kos.id"
          class="bg-surface-container-lowest rounded-2xl shadow-xs hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden group border border-slate-100"
        >
          <!-- Thumbnail Image -->
          <div class="relative w-full aspect-[4/3] overflow-hidden bg-surface-container">
            <img
              :src="getImageForKos(kos.id)"
              :alt="kos.nama"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
              <GenderBadge :jenis="kos.jenis" />
              <span
                class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/90 text-white shadow-xs backdrop-blur-xs select-none"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                Aktif
              </span>
            </div>
            <button
              @click.stop.prevent="handleToggleFavorite(kos)"
              class="absolute top-3 right-3 min-w-[36px] min-h-[36px] rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center shadow-xs transition-all cursor-pointer"
              :class="favoritesStore.isFavorite(kos.id) ? 'text-red-500 hover:scale-110' : 'text-slate-500 hover:text-red-500 hover:scale-105'"
              :title="favoritesStore.isFavorite(kos.id) ? 'Hapus dari Favorit' : 'Simpan Kos ke Favorit'"
              type="button"
            >
              <PhHeart
                :size="18"
                :weight="favoritesStore.isFavorite(kos.id) ? 'fill' : 'bold'"
                class="select-none"
              />
            </button>
          </div>

          <!-- Card Body -->
          <div class="p-space-md flex-1 flex flex-col justify-between space-y-space-sm">
            <div>
              <!-- Kos Title & Rating -->
              <div class="flex items-start justify-between gap-space-xs mb-1">
                <router-link
                  :to="`/kos/${kos.id}`"
                  class="text-sm sm:text-base font-bold text-on-surface group-hover:text-[#017E91] transition-colors truncate flex-1"
                >
                  {{ kos.nama }}
                </router-link>
                <div class="flex items-center gap-1 text-on-surface text-xs shrink-0 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100">
                  <PhStar :size="13" weight="fill" class="text-amber-500 select-none" />
                  <span class="font-bold">{{ kos.rating_rata_rata ? Number(kos.rating_rata_rata).toFixed(1) : 'Baru' }}</span>
                  <span v-if="kos.ulasan_count" class="text-[10px] text-slate-400 font-normal">({{ kos.ulasan_count }})</span>
                </div>
              </div>

              <!-- Address -->
              <div class="flex items-center gap-1 text-on-surface-variant text-xs mb-space-sm">
                <PhMapPin :size="14" weight="bold" class="text-[#017E91] select-none shrink-0" />
                <span class="truncate">{{ kos.alamat }}</span>
              </div>

              <!-- Facilities Badges -->
              <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span
                  v-for="fas in (kos.fasilitas || []).slice(0, 3)"
                  :key="fas.id"
                  class="inline-flex items-center gap-1 bg-surface-container-low text-slate-700 text-[11px] font-semibold px-2 py-0.5 rounded-md border border-slate-200/50"
                >
                  <PhCheckCircle :size="12" weight="bold" class="select-none shrink-0 text-[#017E91]" />
                  <span class="truncate max-w-[90px]">{{ fas.nama }}</span>
                </span>
                <span v-if="(kos.fasilitas || []).length > 3" class="text-[11px] text-slate-400 font-medium">
                  +{{ kos.fasilitas.length - 3 }}
                </span>
              </div>
            </div>

            <!-- Price & Action Button -->
            <div class="pt-space-sm border-t border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-sm sm:text-base font-extrabold text-[#017E91]">{{ formatRupiah(kos.harga) }}</span>
                <span class="text-[11px] text-on-surface-variant font-normal"> / bln</span>
                <div v-if="kos.harga_harian || kos.harga_mingguan" class="flex items-center gap-1 mt-0.5">
                  <span v-if="kos.harga_harian" class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Bisa Harian</span>
                  <span v-if="kos.harga_mingguan" class="text-[9px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">Mingguan</span>
                </div>
              </div>
              <router-link
                :to="`/kos/${kos.id}`"
                class="px-4 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl transition-all shadow-xs active:scale-95"
              >
                Lihat Detail
              </router-link>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- ======================================================== -->
    <!-- POP-UP FILTER MODAL DIALOG (DESKTOP & MOBILE UNIFIED)    -->
    <!-- ======================================================== -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showFilterModal"
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
        @click.self="showFilterModal = false"
      >
        <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 flex flex-col max-h-[85vh] sm:max-h-[90vh] overflow-hidden animate-scale-up text-left">
          <!-- 1. Modal Header -->
          <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60 shrink-0">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#017E91]/10 text-[#017E91] flex items-center justify-center shrink-0">
                <PhSlidersHorizontal :size="22" weight="bold" />
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <h3 class="text-base font-bold text-slate-900">Filter Lengkap Pencarian Kos</h3>
                  <span v-if="activeFilterCount > 0" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#017E91] text-white">
                    {{ activeFilterCount }} aktif
                  </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                  Atur kriteria hunian kos sesuai kebutuhan dan anggaran Anda.
                </p>
              </div>
            </div>
            <button
              @click="showFilterModal = false"
              type="button"
              class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition cursor-pointer"
              title="Tutup Filter"
            >
              <PhX :size="20" weight="bold" />
            </button>
          </div>

          <!-- 2. Modal Scrollable Body -->
          <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6 divide-y divide-slate-100">
            <!-- Section 1: Tipe / Gender Kos -->
            <div class="space-y-3">
              <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                1. Tipe / Penghuni Kos
              </label>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button
                  v-for="opt in jenisOptions"
                  :key="opt.value"
                  @click="filters.jenis = opt.value"
                  type="button"
                  :class="[
                    'p-3 rounded-2xl text-xs font-bold transition flex flex-col items-center justify-center gap-1.5 border cursor-pointer select-none',
                    filters.jenis === opt.value
                      ? 'bg-[#017E91]/10 border-[#017E91] text-[#017E91] shadow-xs ring-1 ring-[#017E91]'
                      : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700'
                  ]"
                >
                  <component
                    :is="getJenisIcon(opt.value)"
                    :size="20"
                    weight="bold"
                    :class="filters.jenis === opt.value ? 'text-[#017E91]' : 'text-slate-400'"
                  />
                  <span>{{ opt.label }}</span>
                </button>
              </div>
            </div>

            <!-- Section 2: Rentang Harga Sewa Bulanan -->
            <div class="pt-5 space-y-3">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                  2. Batas Maksimal Harga Sewa Bulanan
                </label>
                <span class="text-xs font-extrabold text-[#017E91] bg-[#017E91]/10 px-2.5 py-1 rounded-lg">
                  {{ filters.harga_max ? formatRupiah(filters.harga_max) : 'Semua Harga' }}
                </span>
              </div>

              <!-- Range Slider -->
              <input
                v-model.number="filters.harga_max"
                type="range"
                min="300000"
                max="5000000"
                step="50000"
                class="w-full h-2.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-[#017E91]"
              />
              <div class="flex justify-between text-[11px] text-slate-400 font-mono">
                <span>Rp 300.000</span>
                <span>Rp 5.000.000+</span>
              </div>

              <!-- Quick Price Preset Pills -->
              <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span class="text-[11px] text-slate-400 font-semibold mr-1">Preset:</span>
                <button
                  type="button"
                  @click="filters.harga_max = 1000000"
                  class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition cursor-pointer"
                  :class="filters.harga_max === 1000000 ? 'bg-[#017E91] text-white border-[#017E91]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                >
                  &le; Rp 1 Juta
                </button>
                <button
                  type="button"
                  @click="filters.harga_max = 2000000"
                  class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition cursor-pointer"
                  :class="filters.harga_max === 2000000 ? 'bg-[#017E91] text-white border-[#017E91]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                >
                  &le; Rp 2 Juta
                </button>
                <button
                  type="button"
                  @click="filters.harga_max = 3500000"
                  class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition cursor-pointer"
                  :class="filters.harga_max === 3500000 ? 'bg-[#017E91] text-white border-[#017E91]' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                >
                  &le; Rp 3.5 Juta
                </button>
                <button
                  type="button"
                  @click="filters.harga_max = null"
                  class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-100 cursor-pointer"
                >
                  Semua
                </button>
              </div>
            </div>

            <!-- Section 3: Fasilitas Kos & Kamar -->
            <div class="pt-5 space-y-3">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                  3. Fasilitas Properti & Kamar
                </label>
                <span class="text-[11px] text-slate-400">
                  {{ filters.fasilitas.length }} dipilih
                </span>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-56 overflow-y-auto p-1">
                <label
                  v-for="fas in availableFasilitas"
                  :key="fas.id"
                  class="flex items-center gap-2.5 p-2.5 rounded-xl cursor-pointer text-xs font-semibold border transition select-none"
                  :class="filters.fasilitas.includes(fas.id)
                    ? 'bg-[#017E91]/10 text-[#017E91] border-[#017E91]/40'
                    : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'"
                >
                  <input
                    type="checkbox"
                    :value="fas.id"
                    v-model="filters.fasilitas"
                    class="w-4 h-4 rounded text-[#017E91] focus:ring-[#017E91] border-slate-300 cursor-pointer"
                  />
                  <span class="truncate">{{ fas.nama }}</span>
                </label>
              </div>
            </div>

            <!-- Section 4: Ketersediaan Kamar (Hanya Tersedia) -->
            <div class="pt-5">
              <label class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 cursor-pointer hover:bg-slate-100/70 transition">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <PhCheckCircle :size="18" weight="bold" />
                  </div>
                  <div>
                    <span class="text-xs font-bold text-slate-900 block">Hanya Tampilkan Kamar Tersedia (Aktif)</span>
                    <span class="text-[11px] text-slate-500">Sembunyikan kos yang seluruh unit kamarnya sedang penuh</span>
                  </div>
                </div>
                <input
                  type="checkbox"
                  v-model="filters.tersedia"
                  class="w-5 h-5 rounded text-[#017E91] focus:ring-[#017E91] border-slate-300 cursor-pointer"
                />
              </label>
            </div>
          </div>

          <!-- 3. Modal Sticky Footer -->
          <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between gap-3 shrink-0">
            <button
              @click="resetFilters"
              type="button"
              class="px-4 py-2.5 text-xs font-bold text-red-600 hover:text-red-700 hover:bg-red-50 rounded-xl transition flex items-center gap-1.5 cursor-pointer"
            >
              <PhArrowClockwise :size="16" weight="bold" />
              <span>Reset Filter</span>
            </button>

            <div class="flex items-center gap-2">
              <button
                @click="showFilterModal = false"
                type="button"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-semibold transition cursor-pointer"
              >
                Batal
              </button>
              <button
                @click="applyAndCloseFilterModal"
                type="button"
                class="px-6 py-2.5 rounded-xl bg-[#017E91] hover:bg-[#016877] text-white text-xs font-bold transition shadow-md active:scale-95 cursor-pointer flex items-center gap-1.5"
              >
                <PhCheck :size="16" weight="bold" />
                <span>Terapkan Filter</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import { useUiStore } from '../stores/ui'
import api from '../services/api'
import GenderBadge from '../components/GenderBadge.vue'
import {
  PhMagnifyingGlass,
  PhSlidersHorizontal,
  PhX,
  PhStar,
  PhMapPin,
  PhCheckCircle,
  PhHeart,
  PhArrowClockwise,
  PhCheck,
  PhSpinnerGap,
  PhSparkle,
  PhHouse,
  PhGenderFemale,
  PhGenderMale,
  PhUsers
} from '@phosphor-icons/vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const ui = useUiStore()

const loading = ref(false)
const showFilterModal = ref(false) // Unified Pop-up modal state

const kosList = ref([])
const availableFasilitas = ref([])

// Quick presets for types
const quickTypePresets = [
  { label: 'Semua Tipe', value: '' },
  { label: 'Khusus Putri', value: 'putri' },
  { label: 'Khusus Putra', value: 'putra' },
  { label: 'Campur', value: 'campur' },
]

// Popular suggestions for empty search state
const popularSuggestions = ['Khusus Putri', 'Khusus Putra', 'Campur', 'WiFi', 'AC']

const jenisOptions = [
  { label: 'Semua Jenis', value: '' },
  { label: 'Khusus Putri', value: 'putri' },
  { label: 'Khusus Putra', value: 'putra' },
  { label: 'Campur', value: 'campur' },
]

const getJenisIcon = (jenis) => {
  switch (jenis) {
    case 'putri': return PhGenderFemale
    case 'putra': return PhGenderMale
    case 'campur': return PhUsers
    default: return PhHouse
  }
}

const filters = reactive({
  q: route.query.q || '',
  jenis: route.query.jenis || '',
  harga_max: route.query.harga_max ? Number(route.query.harga_max) : null,
  fasilitas: [],
  tersedia: false,
  sort: '',
})

// Count active filters
const activeFilterCount = computed(() => {
  let count = 0
  if (filters.q) count++
  if (filters.jenis) count++
  if (filters.harga_max) count++
  if (filters.fasilitas && filters.fasilitas.length > 0) count += filters.fasilitas.length
  if (filters.tersedia) count++
  return count
})

const getFasilitasName = (id) => {
  const item = availableFasilitas.value.find((f) => f.id === id)
  return item ? item.nama : `Fasilitas #${id}`
}

const clearSearchQuery = () => {
  filters.q = ''
  fetchKosList()
}

const setQuickJenis = (val) => {
  filters.jenis = val
  fetchKosList()
}

const toggleQuickTersedia = () => {
  filters.tersedia = !filters.tersedia
  fetchKosList()
}

const applySuggestion = (sug) => {
  if (sug === 'Khusus Putri') filters.jenis = 'putri'
  else if (sug === 'Khusus Putra') filters.jenis = 'putra'
  else if (sug === 'Campur') filters.jenis = 'campur'
  else {
    filters.q = sug
  }
  fetchKosList()
}

const removeFilter = (key, val = null) => {
  if (key === 'q') filters.q = ''
  else if (key === 'jenis') filters.jenis = ''
  else if (key === 'harga_max') filters.harga_max = null
  else if (key === 'tersedia') filters.tersedia = false
  else if (key === 'fasilitas' && val) {
    filters.fasilitas = filters.fasilitas.filter((id) => id !== val)
  }
  fetchKosList()
}

const resetFilters = () => {
  filters.q = ''
  filters.jenis = ''
  filters.harga_max = null
  filters.fasilitas = []
  filters.tersedia = false
  filters.sort = ''
  fetchKosList()
}

const applyAndCloseFilterModal = () => {
  fetchKosList()
  showFilterModal.value = false
  ui.success('Filter berhasil diterapkan.')
}

const handleToggleFavorite = (kos) => {
  if (!auth.isAuthenticated) {
    ui.warning('Silakan masuk (login) terlebih dahulu untuk menyimpan kos ke favorit.')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  const isAdded = favoritesStore.toggleFavorite(kos)
  if (isAdded) {
    ui.success(`Kos "${kos.nama}" disimpan ke favorit!`)
  } else {
    ui.info(`Kos "${kos.nama}" dihapus dari favorit.`)
  }
}

const samplePhotos = [
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBtAMGcNImL28Wkcksd1pB136MBTXtxAeUZZdLwny2E0TfcGGRkdrMA_JR6pVnktaHUu8Ffua13FdN3HdgZd29vhkeFDWM4WJtQJFMuZndi8o1HB2GXyn8ikhIgK45eAlZmqqiEVYkjMe7_m-Cvw0nxPY6cKLGKCrWp2Ch2dtBIlIqQb5pKjNVS1WaRhbnQPFyDftN7_uvVGXkv3Z_JUnsJpPxReDktVkZYw0eJ1YRW7caBLYvFdhslDw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCOitClHgLDKT01wozYNVTlNFfj9k7oJNfa4q2kNrDk86lv3RxjjBUoPS6XVwSRz1fJt2Njq_q5ULmoPtKJ6tl88vke1gmGCs0RASAks5YEI7qsJO_F2vpf0H79xaVOLcj6daJIZt4_vYQD98RpawFS-fn8uc4Gg8SAZ6U1phc_LSoddYK5bxVMFELhOTo3bzl4rS6xJsZs6VtkRf6E9koZW5_wbWiwXvorevgASvvlZRP5P-63L62c2Q',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCq7lWrhImOr_FOsjNzINLkTN7b_5naoC4qeVNpyv6ugH4fnh9cc_IfxyVcenUHoVsykW_bUzYT96tFdwcpt9ghlRd6ji-wQjUfun67OQOq3T1S6XB2tOmVgvzykn_ra-KV7mRpZLQ09KBeHgpQj_rkik9wD8A8tT5piCUPOvWmwULPLmec2WUaeEHWcK2rVuT-D0jdYE8LleRBoUP8Vwlz6LcasWJw3DEPRxzciDC7dsnsgoobvasOfw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBTwXv4Xzishf0BXTnGXeJGpf3six-aec8DUjhun4L1wAl49mDNbV4PPm9M7tACxaznDsA8efSHLFZXYg2Y2tqKbz4LG-PnJ_Izg77boMl0DFoZjIyMPJsP7f1m1TOy7WRSf9P9KWaENTrx0Q8oiX4WyyJUG--tFfpvnEr55wG2VWex3vG3bk3Oe7VB2DDfVDL3Hrqc01BGrDlFUw8nmuMIrr03zc8cscfCplekhP_8NAdixMSnIyhjFQ',
]

const getImageForKos = (id) => {
  return samplePhotos[(id - 1) % samplePhotos.length]
}

const formatJenis = (jenis) => {
  if (!jenis) return '-'
  return jenis.charAt(0).toUpperCase() + jenis.slice(1)
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

let searchTimer = null
const debounceFetch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    fetchKosList()
  }, 350)
}

const fetchKosList = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.q) params.q = filters.q
    if (filters.jenis) params.jenis = filters.jenis
    if (filters.harga_max) params.harga_max = filters.harga_max
    if (filters.fasilitas && filters.fasilitas.length > 0) params.fasilitas = filters.fasilitas
    if (filters.tersedia) params.tersedia = 1
    if (filters.sort) params.sort = filters.sort

    const res = await api.get('/kos', { params })
    kosList.value = res.data.data || []
  } catch (err) {
    // Abaikan
  } finally {
    loading.value = false
  }
}

const fetchFasilitas = async () => {
  try {
    const res = await api.get('/facilities')
    availableFasilitas.value = res.data.data || []
  } catch (err) {
    // Abaikan
  }
}

onMounted(() => {
  fetchFasilitas()
  fetchKosList()
})
</script>
