<template>
  <div class="max-w-7xl mx-auto w-full px-margin py-space-lg pt-20">
    <!-- ======================================================== -->
    <!-- UNIFIED CLEAN & MINIMALIST TOP SEARCH & CONTROL BAR -->
    <!-- ======================================================== -->
    <div class="mb-space-md bg-white rounded-3xl p-4 sm:p-5 shadow-xs border border-slate-200/70 space-y-3.5">
      <!-- Row 1: Search Input + Toggle Filter + Sort Dropdown -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <!-- Main Search Input with integrated Status Spinner, Clear button & Action button -->
        <div class="relative flex-1 flex items-center group">
          <div class="absolute left-4 flex items-center pointer-events-none text-[#017E91]">
            <PhMagnifyingGlass :size="20" weight="bold" />
          </div>
          <input
            v-model="filters.q"
            @input="debounceFetch"
            @keydown.enter="fetchKosList"
            @keydown.esc="clearSearchQuery"
            type="text"
            class="w-full h-12 pl-12 pr-28 bg-slate-50/80 hover:bg-slate-50 focus:bg-white rounded-2xl text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#017E91]/25 border border-slate-200/80 focus:border-[#017E91] transition-all shadow-inner"
            placeholder="Cari lokasi kos, nama jalan, area kampus, atau kos..."
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

        <!-- Controls: Desktop Filter Toggle, Mobile Filter Button, Sort Dropdown -->
        <div class="flex items-center gap-2 shrink-0">
          <!-- Desktop Toggle Filter Button (Buka / Tutup Filter) -->
          <button
            @click="isFilterOpenDesktop = !isFilterOpenDesktop"
            type="button"
            class="hidden lg:inline-flex items-center gap-2 h-12 px-4 rounded-2xl text-xs font-bold transition-all border cursor-pointer select-none"
            :class="isFilterOpenDesktop
              ? 'bg-slate-50 border-slate-200/80 text-slate-700 hover:bg-slate-100 hover:border-slate-300'
              : 'bg-[#017E91] border-[#017E91] text-white hover:bg-[#016877] shadow-xs'"
            :title="isFilterOpenDesktop ? 'Sembunyikan panel filter samping' : 'Buka panel filter samping'"
          >
            <PhSlidersHorizontal :size="18" weight="bold" />
            <span>{{ isFilterOpenDesktop ? 'Sembunyikan Filter' : 'Buka Filter' }}</span>
            <span
              v-if="activeFilterCount > 0"
              :class="isFilterOpenDesktop ? 'bg-[#017E91] text-white' : 'bg-white text-[#017E91]'"
              class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
            >
              {{ activeFilterCount }}
            </span>
          </button>

          <!-- Mobile Filter Trigger Button -->
          <button
            @click="showMobileFilter = true"
            type="button"
            class="lg:hidden inline-flex items-center justify-center gap-2 h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-700 text-xs font-bold hover:bg-slate-100 transition shadow-xs cursor-pointer select-none flex-1 sm:flex-initial"
          >
            <PhSlidersHorizontal :size="18" weight="bold" class="text-[#017E91]" />
            <span>Filter</span>
            <span
              v-if="activeFilterCount > 0"
              class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#017E91] text-white"
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
        </div>

        <!-- Realtime Result Summary -->
        <div class="text-[11px] text-slate-500 shrink-0 hidden sm:block">
          Ditemukan <span class="font-extrabold text-[#017E91]">{{ kosList.length }}</span> properti kos
        </div>
      </div>

      <!-- Row 3: Active Filter Chips (Hanya muncul jika ada filter aktif) -->
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
          class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/70 px-2.5 py-1 rounded-xl text-xs font-medium"
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
    <!-- MAIN CONTENT AREA: FILTER SIDEBAR & DYNAMIC LISTINGS GRID -->
    <!-- ======================================================== -->
    <div class="flex flex-col lg:flex-row gap-space-xl items-start relative">
      <!-- ========================================== -->
      <!-- COLUMN 1: FILTER SIDEBAR (DESKTOP COLLAPSIBLE) -->
      <!-- ========================================== -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-x-4 max-w-0"
        enter-to-class="opacity-100 translate-x-0 max-w-[280px]"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-x-0 max-w-[280px]"
        leave-to-class="opacity-0 -translate-x-4 max-w-0"
      >
        <aside
          v-show="isFilterOpenDesktop"
          class="hidden lg:block w-[280px] shrink-0 bg-surface-container-lowest rounded-2xl shadow-xs p-space-md space-y-4 lg:sticky lg:top-20 z-20 border border-slate-100"
        >
          <!-- Sidebar Header -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
              <PhFunnel :size="18" weight="bold" class="text-[#017E91]" />
              <h2 class="text-sm font-bold text-on-surface">Filter Pencarian</h2>
            </div>
            <button
              v-if="activeFilterCount > 0"
              @click="resetFilters"
              class="text-xs text-[#017E91] hover:underline font-bold cursor-pointer"
              type="button"
            >
              Reset
            </button>
          </div>

          <!-- Accordion 1: Rentang Harga -->
          <div class="border-b border-slate-100 pb-3">
            <button
              @click="accordions.harga = !accordions.harga"
              type="button"
              class="w-full flex items-center justify-between py-1 text-xs font-bold text-on-surface hover:text-[#017E91] transition cursor-pointer"
            >
              <span class="flex items-center gap-1.5">
                <span>Rentang Harga</span>
                <span v-if="filters.harga_max" class="w-1.5 h-1.5 rounded-full bg-[#017E91]"></span>
              </span>
              <component :is="accordions.harga ? PhCaretUp : PhCaretDown" :size="14" weight="bold" class="text-slate-400" />
            </button>

            <div v-show="accordions.harga" class="space-y-2 pt-2.5">
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Maksimal:</span>
                <span class="text-[#017E91] font-bold">
                  {{ filters.harga_max ? formatRupiah(filters.harga_max) : 'Semua Harga' }}
                </span>
              </div>
              <input
                v-model.number="filters.harga_max"
                @change="fetchKosList"
                type="range"
                min="300000"
                max="3000000"
                step="50000"
                class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-[#017E91]"
              />
              <div class="flex justify-between items-center text-[11px] text-slate-400">
                <span>Rp 300rb</span>
                <span>Rp 3.000.000</span>
              </div>
            </div>
          </div>

          <!-- Accordion 2: Tipe / Jenis Kos -->
          <div class="border-b border-slate-100 pb-3">
            <button
              @click="accordions.jenis = !accordions.jenis"
              type="button"
              class="w-full flex items-center justify-between py-1 text-xs font-bold text-on-surface hover:text-[#017E91] transition cursor-pointer"
            >
              <span class="flex items-center gap-1.5">
                <span>Tipe / Jenis Kos</span>
                <span v-if="filters.jenis" class="w-1.5 h-1.5 rounded-full bg-[#E68202]"></span>
              </span>
              <component :is="accordions.jenis ? PhCaretUp : PhCaretDown" :size="14" weight="bold" class="text-slate-400" />
            </button>

            <div v-show="accordions.jenis" class="space-y-1.5 pt-2">
              <label
                v-for="item in jenisOptions"
                :key="item.value"
                class="flex items-center justify-between p-2 rounded-xl cursor-pointer hover:bg-slate-50 text-xs transition"
                :class="filters.jenis === item.value ? 'bg-[#017E91]/5 font-bold text-[#017E91]' : 'text-slate-600'"
              >
                <div class="flex items-center gap-2.5">
                  <input
                    type="radio"
                    name="jenis_desktop"
                    :value="item.value"
                    v-model="filters.jenis"
                    @change="fetchKosList"
                    class="w-4 h-4 text-[#017E91] focus:ring-[#017E91] border-slate-300"
                  />
                  <span>{{ item.label }}</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Accordion 3: Fasilitas Kos -->
          <div class="border-b border-slate-100 pb-3">
            <button
              @click="accordions.fasilitas = !accordions.fasilitas"
              type="button"
              class="w-full flex items-center justify-between py-1 text-xs font-bold text-on-surface hover:text-[#017E91] transition cursor-pointer"
            >
              <span class="flex items-center gap-1.5">
                <span>Fasilitas</span>
                <span v-if="filters.fasilitas.length > 0" class="px-1.5 py-0.2 rounded-full text-[10px] bg-[#017E91] text-white">
                  {{ filters.fasilitas.length }}
                </span>
              </span>
              <component :is="accordions.fasilitas ? PhCaretUp : PhCaretDown" :size="14" weight="bold" class="text-slate-400" />
            </button>

            <div v-show="accordions.fasilitas" class="pt-2">
              <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1">
                <label
                  v-for="fas in availableFasilitas"
                  :key="fas.id"
                  class="flex items-center gap-2.5 p-1.5 rounded-lg cursor-pointer hover:bg-slate-50 text-xs text-slate-600 hover:text-slate-900 transition"
                >
                  <input
                    type="checkbox"
                    :value="fas.id"
                    v-model="filters.fasilitas"
                    @change="fetchKosList"
                    class="w-4 h-4 rounded text-[#017E91] focus:ring-[#017E91] border-slate-300"
                  />
                  <span>{{ fas.nama }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Accordion 4: Ketersediaan Kamar -->
          <div class="pt-1">
            <label class="flex items-center gap-2.5 p-2 rounded-xl cursor-pointer hover:bg-slate-50 text-xs font-semibold text-slate-700 transition">
              <input
                type="checkbox"
                v-model="filters.tersedia"
                @change="fetchKosList"
                class="w-4 h-4 rounded text-[#017E91] focus:ring-[#017E91] border-slate-300"
              />
              <span>Hanya Kamar Tersedia</span>
            </label>
          </div>
        </aside>
      </transition>

      <!-- ========================================== -->
      <!-- MOBILE FILTER SLIDE-OVER DRAWER (LG:HIDDEN) -->
      <!-- ========================================== -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showMobileFilter"
          class="lg:hidden fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-xs flex justify-end"
          @click.self="showMobileFilter = false"
        >
          <div class="w-full max-w-sm bg-white h-full shadow-2xl flex flex-col overflow-hidden animate-slide-left">
            <!-- Mobile Header -->
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
              <div class="flex items-center gap-2">
                <PhFunnel :size="18" weight="bold" class="text-[#017E91]" />
                <h3 class="text-sm font-bold text-on-surface">Filter Kos</h3>
                <span v-if="activeFilterCount > 0" class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-[#017E91] text-white">
                  {{ activeFilterCount }}
                </span>
              </div>
              <button
                @click="showMobileFilter = false"
                type="button"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
              >
                <PhX :size="18" weight="bold" />
              </button>
            </div>

            <!-- Mobile Scrollable Body -->
            <div class="flex-1 overflow-y-auto p-4 space-y-5">
              <!-- Rentang Harga -->
              <div>
                <div class="flex items-center justify-between text-xs font-bold text-on-surface mb-2">
                  <span>Maks. Harga Sewa</span>
                  <span class="text-[#017E91]">{{ filters.harga_max ? formatRupiah(filters.harga_max) : 'Semua' }}</span>
                </div>
                <input
                  v-model.number="filters.harga_max"
                  @change="fetchKosList"
                  type="range"
                  min="300000"
                  max="3000000"
                  step="50000"
                  class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-[#017E91]"
                />
                <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                  <span>Rp 300rb</span>
                  <span>Rp 3.000.000</span>
                </div>
              </div>

              <!-- Jenis Kos -->
              <div>
                <label class="block text-xs font-bold text-on-surface mb-2">Tipe / Jenis Kos</label>
                <div class="grid grid-cols-2 gap-2">
                  <button
                    v-for="item in jenisOptions"
                    :key="item.value"
                    type="button"
                    @click="filters.jenis = item.value; fetchKosList()"
                    :class="[
                      'p-2.5 rounded-xl text-xs font-semibold border text-center transition cursor-pointer',
                      filters.jenis === item.value
                        ? 'bg-[#017E91] text-white border-[#017E91]'
                        : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'
                    ]"
                  >
                    {{ item.label }}
                  </button>
                </div>
              </div>

              <!-- Fasilitas -->
              <div>
                <label class="block text-xs font-bold text-on-surface mb-2">Fasilitas Kos</label>
                <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto pr-1">
                  <label
                    v-for="fas in availableFasilitas"
                    :key="fas.id"
                    class="flex items-center gap-2 p-2 rounded-xl border border-slate-200 cursor-pointer text-xs"
                    :class="filters.fasilitas.includes(fas.id) ? 'bg-[#017E91]/10 border-[#017E91] text-[#017E91] font-bold' : 'text-slate-600'"
                  >
                    <input
                      type="checkbox"
                      :value="fas.id"
                      v-model="filters.fasilitas"
                      @change="fetchKosList"
                      class="w-3.5 h-3.5 rounded text-[#017E91] focus:ring-[#017E91] border-slate-300"
                    />
                    <span class="truncate">{{ fas.nama }}</span>
                  </label>
                </div>
              </div>

              <!-- Ketersediaan -->
              <div class="pt-2 border-t border-slate-100">
                <label class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 cursor-pointer text-xs font-bold text-slate-700">
                  <input
                    type="checkbox"
                    v-model="filters.tersedia"
                    @change="fetchKosList"
                    class="w-4 h-4 rounded text-[#017E91] focus:ring-[#017E91] border-slate-300"
                  />
                  <span>Hanya Tampilkan Kamar Tersedia</span>
                </label>
              </div>
            </div>

            <!-- Mobile Sticky Footer -->
            <div class="p-4 border-t border-slate-100 bg-white flex items-center gap-3">
              <button
                @click="resetFilters"
                type="button"
                class="flex-1 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition text-center"
              >
                Reset
              </button>
              <button
                @click="showMobileFilter = false"
                type="button"
                class="flex-[2] py-2.5 px-4 rounded-xl bg-[#017E91] hover:bg-[#016877] text-white text-xs font-bold transition text-center shadow-xs"
              >
                Terapkan ({{ kosList.length }} Kos)
              </button>
            </div>
          </div>
        </div>
      </transition>

      <!-- ========================================== -->
      <!-- COLUMN 2: RESPONSIVE LISTINGS CONTENT -->
      <!-- ========================================== -->
      <section class="flex-1 w-full space-y-space-md min-w-0">
        <!-- Loading State Skeletons -->
        <div
          v-if="loading"
          :class="[
            'grid gap-space-md transition-all duration-300',
            isFilterOpenDesktop
              ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
              : 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
          ]"
        >
          <div
            v-for="i in (isFilterOpenDesktop ? 6 : 8)"
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

        <!-- Empty State with Clean Suggestions -->
        <div
          v-else-if="kosList.length === 0"
          class="text-center py-16 px-4 bg-surface-container-lowest rounded-3xl border border-slate-100 shadow-xs"
        >
          <div class="w-16 h-16 rounded-2xl bg-[#017E91]/10 flex items-center justify-center mx-auto text-[#017E91] mb-4">
            <PhMagnifyingGlass :size="32" weight="bold" />
          </div>
          <h3 class="text-base font-bold text-on-surface">Tidak ada kos yang cocok dengan kriteria Anda</h3>
          <p class="text-xs text-on-surface-variant mt-1.5 max-w-sm mx-auto leading-relaxed">
            Coba gunakan kata kunci populer di bawah atau sesuaikan filter untuk menemukan pilihan hunian lainnya:
          </p>
          <!-- Popular Search Suggestions -->
          <div class="flex flex-wrap items-center justify-center gap-2 mt-4 max-w-md mx-auto">
            <button
              v-for="sug in popularSuggestions"
              :key="sug"
              @click="applySuggestion(sug)"
              type="button"
              class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-slate-100 hover:bg-[#017E91]/10 hover:text-[#017E91] text-slate-700 border border-slate-200/60 transition cursor-pointer"
            >
              {{ sug }}
            </button>
          </div>
          <button
            @click="resetFilters"
            class="mt-6 px-5 py-2.5 bg-[#017E91] hover:bg-[#016877] text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer inline-flex items-center gap-1.5"
          >
            <PhArrowClockwise :size="14" weight="bold" />
            <span>Reset Semua Filter</span>
          </button>
        </div>

        <!-- Cards Grid: Dynamic Columns based on Filter Open/Closed -->
        <div
          v-else
          :class="[
            'grid gap-space-md transition-all duration-300',
            isFilterOpenDesktop
              ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
              : 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
          ]"
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
              <div class="absolute top-3 left-3 flex items-center gap-1.5">
                <GenderBadge :jenis="kos.jenis" />
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
                  class="px-3.5 py-1.5 bg-[#017E91]/10 hover:bg-[#017E91] text-[#017E91] hover:text-white text-xs font-bold rounded-xl transition duration-200"
                >
                  Lihat Detail
                </router-link>
              </div>
            </div>
          </article>
        </div>
      </section>
    </div>
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
  PhSlidersHorizontal,
  PhFunnel,
  PhMapPin,
  PhMagnifyingGlass,
  PhHeart,
  PhStar,
  PhCheckCircle,
  PhX,
  PhCaretDown,
  PhCaretUp,
  PhArrowClockwise,
  PhSpinnerGap,
  PhSparkle,
  PhCheck
} from '@phosphor-icons/vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const ui = useUiStore()

const loading = ref(false)
const showMobileFilter = ref(false)
const isFilterOpenDesktop = ref(true) // Collapsible sidebar state (Desktop)

const kosList = ref([])
const availableFasilitas = ref([])

// Section accordion toggle states
const accordions = reactive({
  harga: true,
  jenis: true,
  fasilitas: true,
})

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
