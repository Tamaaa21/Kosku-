<template>
  <div class="max-w-7xl mx-auto w-full px-margin py-space-lg pt-20">
    <!-- Top Context & Quick Switch to Map View -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-space-md gap-space-sm">
      <div class="flex items-center gap-space-xs text-on-surface-variant text-xs">
        <router-link to="/" class="hover:text-primary transition-colors">Beranda</router-link>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-on-surface font-semibold">Daftar Kos di Indonesia</span>
      </div>

      <div class="flex items-center gap-space-sm self-start sm:self-auto">
        <router-link
          to="/peta"
          class="inline-flex items-center gap-1.5 px-space-md py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary text-xs font-semibold transition-colors shadow-sm"
        >
          <span class="material-symbols-outlined text-[18px]">map</span>
          <span>Lihat di Peta</span>
        </router-link>

        <button
          @click="showMobileFilter = !showMobileFilter"
          class="lg:hidden inline-flex items-center gap-1.5 px-space-md py-1.5 rounded-lg bg-surface-container-lowest text-on-surface text-xs font-semibold shadow-sm border border-slate-200"
          type="button"
        >
          <span class="material-symbols-outlined text-[18px]">tune</span>
          <span>Filter</span>
        </button>
      </div>
    </div>

    <!-- Main 2-Column Split: Filter Sidebar (280px) & Listings Content -->
    <div class="flex flex-col lg:flex-row gap-space-xl items-start relative">
      <!-- COLUMN 1: FILTER SIDEBAR (280px Sticky Desktop) -->
      <aside
        :class="[
          'w-full lg:w-[280px] shrink-0 bg-surface-container-lowest rounded-xl shadow-sm p-space-md space-y-space-md lg:sticky lg:top-20 z-20 border border-slate-100',
          showMobileFilter ? 'block' : 'hidden lg:block'
        ]"
      >
        <!-- Header Filter -->
        <div class="flex items-center justify-between pb-space-xs border-b border-slate-100">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">filter_list</span>
            <h2 class="text-base font-bold text-on-surface">Filter</h2>
          </div>
          <button
            @click="resetFilters"
            class="text-xs text-primary hover:underline font-semibold cursor-pointer"
            type="button"
          >
            Reset
          </button>
        </div>

        <!-- Filter: Lokasi / Pencarian -->
        <div class="space-y-1.5">
          <label class="block text-xs font-semibold text-on-surface">Pencarian / Lokasi</label>
          <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3 text-secondary text-[20px] pointer-events-none">location_on</span>
            <input
              v-model="filters.q"
              @input="debounceFetch"
              type="text"
              class="w-full h-10 pl-10 pr-3 bg-surface-container-low rounded-lg text-xs text-on-surface placeholder:text-secondary focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all border-none"
              placeholder="Cari lokasi, jalan, nama..."
            />
          </div>
        </div>

        <!-- Filter: Rentang Harga -->
        <div class="space-y-2 pt-space-xs">
          <div class="flex items-center justify-between">
            <label class="text-xs font-semibold text-on-surface">Maks. Harga</label>
            <span class="text-xs text-primary font-bold">
              {{ filters.harga_max ? formatRupiah(filters.harga_max) : 'Semua' }}
            </span>
          </div>
          <input
            v-model.number="filters.harga_max"
            @change="fetchKosList"
            type="range"
            min="300000"
            max="3000000"
            step="50000"
            class="w-full h-2 bg-surface-container rounded-lg appearance-none cursor-pointer accent-primary-container"
          />
          <div class="flex justify-between items-center text-[11px] text-secondary">
            <span>Rp 300rb</span>
            <span>Rp 3.000.000</span>
          </div>
        </div>

        <!-- Filter: Tipe / Jenis Kos -->
        <div class="space-y-2 pt-space-xs">
          <label class="block text-xs font-semibold text-on-surface">Jenis Kos</label>
          <div class="space-y-1.5">
            <label class="flex items-center gap-2.5 cursor-pointer text-xs text-on-surface-variant hover:text-on-surface">
              <input
                type="radio"
                name="jenis"
                value=""
                v-model="filters.jenis"
                @change="fetchKosList"
                class="w-4 h-4 text-primary focus:ring-primary border-slate-300"
              />
              <span>Semua Jenis</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer text-xs text-on-surface-variant hover:text-on-surface">
              <input
                type="radio"
                name="jenis"
                value="putri"
                v-model="filters.jenis"
                @change="fetchKosList"
                class="w-4 h-4 text-primary focus:ring-primary border-slate-300"
              />
              <span>Khusus Putri</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer text-xs text-on-surface-variant hover:text-on-surface">
              <input
                type="radio"
                name="jenis"
                value="putra"
                v-model="filters.jenis"
                @change="fetchKosList"
                class="w-4 h-4 text-primary focus:ring-primary border-slate-300"
              />
              <span>Khusus Putra</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer text-xs text-on-surface-variant hover:text-on-surface">
              <input
                type="radio"
                name="jenis"
                value="campur"
                v-model="filters.jenis"
                @change="fetchKosList"
                class="w-4 h-4 text-primary focus:ring-primary border-slate-300"
              />
              <span>Campur</span>
            </label>
          </div>
        </div>

        <!-- Filter: Fasilitas -->
        <div class="space-y-2 pt-space-xs">
          <label class="block text-xs font-semibold text-on-surface">Fasilitas</label>
          <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
            <label
              v-for="fas in availableFasilitas"
              :key="fas.id"
              class="flex items-center gap-2.5 cursor-pointer text-xs text-on-surface-variant hover:text-on-surface"
            >
              <input
                type="checkbox"
                :value="fas.id"
                v-model="filters.fasilitas"
                @change="fetchKosList"
                class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300"
              />
              <span>{{ fas.nama }}</span>
            </label>
          </div>
        </div>

        <!-- Filter: Hanya yang tersedia -->
        <div class="pt-space-xs border-t border-slate-100">
          <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-on-surface">
            <input
              type="checkbox"
              v-model="filters.tersedia"
              @change="fetchKosList"
              class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300"
            />
            <span>Hanya Kamar Tersedia</span>
          </label>
        </div>
      </aside>

      <!-- COLUMN 2: LISTINGS CONTENT -->
      <section class="flex-1 w-full space-y-space-md">
        <!-- Result Bar & Order Indicator -->
        <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
          <div>
            <h1 class="text-lg font-bold text-on-surface">Hasil Pencarian Kos</h1>
            <p class="text-xs text-on-surface-variant">
              Menampilkan <span class="font-bold text-primary">{{ kosList.length }}</span> hunian di Indonesia
            </p>
          </div>

          <div class="flex items-center gap-2">
            <label class="text-xs text-secondary whitespace-nowrap">Urutkan:</label>
            <select
              v-model="filters.sort"
              @change="fetchKosList"
              class="text-xs bg-surface-container-low rounded-lg border-none focus:ring-2 focus:ring-primary/20 py-2 px-3 text-on-surface cursor-pointer"
            >
              <option value="">Rekomendasi / Terbaru</option>
              <option value="harga_asc">Harga Terendah</option>
              <option value="harga_desc">Harga Tertinggi</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
          <div v-for="i in 6" :key="i" class="h-80 bg-white rounded-xl border border-slate-100 animate-pulse"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="kosList.length === 0" class="text-center py-16 bg-surface-container-lowest rounded-xl border border-slate-100">
          <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mx-auto text-primary mb-4">
            <span class="material-symbols-outlined text-3xl">search_off</span>
          </div>
          <h3 class="text-base font-bold text-on-surface">Tidak ada kos yang cocok</h3>
          <p class="text-xs text-on-surface-variant mt-1 max-w-sm mx-auto">
            Coba sesuaikan kata kunci pencarian, rentang harga, atau filter fasilitas Anda.
          </p>
          <button
            @click="resetFilters"
            class="mt-4 px-4 py-2 bg-primary-container text-white text-xs font-semibold rounded-lg hover:bg-primary transition"
          >
            Reset Semua Filter
          </button>
        </div>

        <!-- Cards Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
          <article
            v-for="kos in kosList"
            :key="kos.id"
            class="bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden group border border-slate-100"
          >
            <!-- Thumbnail Image -->
            <div class="relative w-full aspect-[4/3] overflow-hidden bg-surface-container">
              <img
                :src="getImageForKos(kos.id)"
                :alt="kos.nama"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div class="absolute top-3 left-3 flex items-center gap-1.5">
                <span
                  :class="[
                    'text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-sm',
                    kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                    kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                    'bg-[#EDE9FE] text-[#6D28D9]'
                  ]"
                >
                  {{ formatJenis(kos.jenis) }}
                </span>
              </div>
              <button
                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-surface-container-lowest/90 backdrop-blur-sm text-secondary hover:text-error flex items-center justify-center shadow-sm transition-colors"
                title="Simpan Kos"
              >
                <span class="material-symbols-outlined text-lg select-none">favorite</span>
              </button>
            </div>

            <!-- Card Body -->
            <div class="p-space-md flex-1 flex flex-col justify-between space-y-space-sm">
              <div>
                <div class="flex items-center justify-between gap-space-xs mb-1">
                  <router-link :to="`/kos/${kos.id}`" class="text-base font-bold text-on-surface group-hover:text-primary transition-colors truncate">
                    {{ kos.nama }}
                  </router-link>
                  <div class="flex items-center gap-1 text-on-surface text-xs shrink-0">
                    <span class="material-symbols-outlined text-amber-500 text-sm select-none" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="font-semibold">{{ kos.rating_rata_rata ? Number(kos.rating_rata_rata).toFixed(1) : 'Baru' }}</span>
                    <span v-if="kos.ulasan_count" class="text-[10px] text-slate-400 font-normal">({{ kos.ulasan_count }})</span>
                  </div>
                </div>

                <div class="flex items-center gap-1 text-on-surface-variant text-xs mb-space-sm">
                  <span class="material-symbols-outlined text-sm text-primary select-none">location_on</span>
                  <span class="truncate">{{ kos.alamat }}</span>
                </div>

                <!-- Facilities Badges -->
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                  <span
                    v-for="fas in (kos.fasilitas || []).slice(0, 3)"
                    :key="fas.id"
                    class="inline-flex items-center gap-1 bg-surface-container-low text-primary text-[11px] font-semibold px-2.5 py-1 rounded-full"
                  >
                    <span class="material-symbols-outlined text-[13px] select-none">check_circle</span>
                    <span>{{ fas.nama }}</span>
                  </span>
                  <span v-if="(kos.fasilitas || []).length > 3" class="text-[11px] text-on-surface-variant">
                    +{{ kos.fasilitas.length - 3 }}
                  </span>
                </div>
              </div>

              <!-- Price & Action -->
              <div class="pt-space-sm border-t border-slate-100 flex items-center justify-between">
                <div>
                  <span class="text-base font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                  <span class="text-xs text-on-surface-variant font-normal"> / bln</span>
                  <div v-if="kos.harga_harian || kos.harga_mingguan" class="flex items-center gap-1 mt-0.5">
                    <span v-if="kos.harga_harian" class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Bisa Harian</span>
                    <span v-if="kos.harga_mingguan" class="text-[9px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">Mingguan</span>
                  </div>
                </div>
                <router-link
                  :to="`/kos/${kos.id}`"
                  class="px-3 py-1.5 bg-surface-container-low hover:bg-surface-container text-primary text-xs font-semibold rounded-lg transition"
                >
                  Detail
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
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()

const loading = ref(false)
const showMobileFilter = ref(false)
const kosList = ref([])
const availableFasilitas = ref([])

const filters = reactive({
  q: route.query.q || '',
  jenis: route.query.jenis || '',
  harga_max: route.query.harga_max ? Number(route.query.harga_max) : null,
  fasilitas: [],
  tersedia: false,
  sort: '',
})

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

const resetFilters = () => {
  filters.q = ''
  filters.jenis = ''
  filters.harga_max = null
  filters.fasilitas = []
  filters.tersedia = false
  filters.sort = ''
  fetchKosList()
}

const fetchFasilitas = async () => {
  try {
    const res = await api.get('/fasilitas')
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
