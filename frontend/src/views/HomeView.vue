<template>
  <div class="w-full">
    <!-- Hero Section -->
    <section class="relative w-full overflow-hidden pt-16">
      <div
        class="relative w-full min-h-[520px] lg:min-h-[580px] bg-cover bg-center flex items-center justify-center px-margin py-space-xl"
        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAuxX9rhUIqskAHsOQuGOG_bIw28TdVEZZqZJYbCEyyKVNen8W7vHD5Na3XRNyv_VX49pIw4lTGVOYQRilL3WL7ZJ_0_sJs4gtMHLS4VDAIczLtBi65wMFCSKl5M_HKm-iGLPGX22UEgiDygZJppjal4fN-7qoh6R_3V3r4xXZMu0dA0GFaiFw_auXRxOz0sTpMSqJ8iMdXT1UPsArttAUJTv57SVsfWyOEarTJ-R2Iz3U3vjNLRwlv3Q');"
      >
        <!-- Dark scrim overlay for high legibility -->
        <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/90 via-inverse-surface/65 to-inverse-surface/40 backdrop-blur-[1px]"></div>

        <div class="relative z-10 max-w-5xl mx-auto w-full flex flex-col items-center text-center mt-6 lg:mt-10">
          <h1 class="text-3xl sm:text-5xl font-bold text-white tracking-tight max-w-3xl drop-shadow-sm">
            Cari Kos Jadi Lebih Mudah
          </h1>
          <p class="text-base sm:text-lg text-surface-container-low max-w-2xl mt-space-sm mb-space-lg leading-relaxed">
            Temukan kos terbaik sesuai kebutuhanmu. Dengan lokasi yang akurat, fasilitas lengkap, dan harga yang transparan di seluruh wilayah Indonesia.
          </p>

          <!-- Floating Search Bar -->
          <div class="w-full max-w-4xl bg-surface-container-lowest rounded-xl shadow-xl p-space-xs sm:p-space-sm flex flex-col lg:flex-row items-stretch lg:items-center gap-space-xs sm:gap-space-sm">
            <!-- Text Search -->
            <div class="flex-1 flex items-center gap-space-xs px-space-sm py-2 bg-surface-container-low rounded-lg focus-within:bg-surface-container-lowest focus-within:ring-2 focus-within:ring-primary/20 transition-all">
              <span class="material-symbols-outlined text-primary text-xl select-none">search</span>
              <input
                v-model="searchQuery"
                type="text"
                class="w-full bg-transparent text-sm text-on-surface placeholder:text-on-surface-variant/70 border-none focus:ring-0 p-0"
                placeholder="Cari nama kos, jalan, atau kota di Indonesia..."
                @keyup.enter="handleSearch"
              />
            </div>

            <!-- Divider Desktop -->
            <div class="hidden lg:block w-px h-8 bg-surface-container-high"></div>

            <!-- Jenis Kos Dropdown -->
            <div class="flex items-center gap-space-xs px-space-sm py-2 bg-surface-container-low rounded-lg min-w-[160px]">
              <span class="material-symbols-outlined text-secondary text-lg select-none">group</span>
              <select
                v-model="selectedJenis"
                class="w-full bg-transparent text-sm text-on-surface border-none focus:ring-0 p-0 cursor-pointer"
              >
                <option value="">Semua Jenis</option>
                <option value="putra">Putra</option>
                <option value="putri">Putri</option>
                <option value="campur">Campur</option>
              </select>
            </div>

            <!-- Divider Desktop -->
            <div class="hidden lg:block w-px h-8 bg-surface-container-high"></div>

            <!-- Rentang Harga Dropdown -->
            <div class="flex items-center gap-space-xs px-space-sm py-2 bg-surface-container-low rounded-lg min-w-[170px]">
              <span class="material-symbols-outlined text-secondary text-lg select-none">payments</span>
              <select
                v-model="selectedPriceRange"
                class="w-full bg-transparent text-sm text-on-surface border-none focus:ring-0 p-0 cursor-pointer"
              >
                <option value="">Semua Harga</option>
                <option value="under500">&lt; Rp 500rb</option>
                <option value="500-1000">Rp 500rb - 1jt</option>
                <option value="above1000">&gt; Rp 1jt</option>
              </select>
            </div>

            <!-- Search Button -->
            <button
              @click="handleSearch"
              class="bg-primary-container hover:bg-primary text-white px-space-lg py-3 rounded-lg text-sm font-semibold flex items-center justify-center gap-space-xs transition-all shadow-md hover:shadow-lg active:scale-[0.99] whitespace-nowrap"
            >
              <span class="material-symbols-outlined text-lg select-none">search</span>
              <span>Cari</span>
            </button>
          </div>

          <!-- Quick Sub-tags -->
          <div class="flex flex-wrap items-center justify-center gap-space-xs mt-space-md text-white/80 text-xs">
            <span class="text-surface-container-high">Pencarian populer:</span>
            <button @click="quickSearch('Jakarta')" class="bg-white/15 hover:bg-white/30 px-2.5 py-1 rounded backdrop-blur-sm transition text-white">
              Jakarta
            </button>
            <button @click="quickSearch('Yogyakarta')" class="bg-white/15 hover:bg-white/30 px-2.5 py-1 rounded backdrop-blur-sm transition text-white">
              Yogyakarta
            </button>
            <button @click="quickSearch('Bandung')" class="bg-white/15 hover:bg-white/30 px-2.5 py-1 rounded backdrop-blur-sm transition text-white">
              Bandung
            </button>
            <button @click="quickSearch('Surabaya')" class="bg-white/15 hover:bg-white/30 px-2.5 py-1 rounded backdrop-blur-sm transition text-white">
              Surabaya
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Platform Advantages Section -->
    <section class="max-w-7xl mx-auto w-full px-margin py-space-xl">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-space-lg">
        <!-- Card 1 -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md group border border-slate-100">
          <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors flex-shrink-0">
            <span class="material-symbols-outlined text-2xl select-none" style="font-variation-settings: 'FILL' 1;">location_on</span>
          </div>
          <div class="space-y-1">
            <h3 class="text-base font-bold text-on-surface">Lokasi Akurat</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Terintegrasi dengan peta presisi OpenStreetMap dan Geocoding API untuk memastikan titik lokasi kos tepat tanpa tersesat.
            </p>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md group border border-slate-100">
          <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors flex-shrink-0">
            <span class="material-symbols-outlined text-2xl select-none" style="font-variation-settings: 'FILL' 1;">checklist</span>
          </div>
          <div class="space-y-1">
            <h3 class="text-base font-bold text-on-surface">Fasilitas Lengkap</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Lihat ketersediaan kamar, kamar mandi dalam, WiFi, dan parkir kendaraan secara transparan dan detail.
            </p>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex items-start gap-space-md group border border-slate-100">
          <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors flex-shrink-0">
            <span class="material-symbols-outlined text-2xl select-none" style="font-variation-settings: 'FILL' 1;">verified_user</span>
          </div>
          <div class="space-y-1">
            <h3 class="text-base font-bold text-on-surface">Aman & Terpercaya</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Seluruh data listing dikelola langsung oleh pemilik properti resmi dengan proteksi akun terverifikasi.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Kos Section -->
    <section class="max-w-7xl mx-auto w-full px-margin pb-space-xl">
      <!-- Section Header -->
      <div class="flex items-center justify-between gap-space-md mb-space-lg">
        <div class="space-y-1">
          <h2 class="text-2xl font-bold text-on-surface tracking-tight">Kos Populer</h2>
          <p class="text-xs text-on-surface-variant">Paling banyak diminati dan berlokasi strategis di berbagai kota di Indonesia</p>
        </div>
        <router-link
          to="/kos"
          class="text-xs font-semibold text-primary hover:text-primary-container flex items-center gap-1 group"
        >
          <span>Lihat Semua</span>
          <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform select-none">arrow_forward</span>
        </router-link>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
        <div v-for="i in 6" :key="i" class="h-80 bg-white rounded-xl border border-slate-100 animate-pulse"></div>
      </div>

      <!-- Cards Grid -->
      <div v-else-if="kosList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
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
              <span class="bg-primary text-white text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-sm tracking-wide">
                Populer
              </span>
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
                <div class="flex items-center gap-1 text-on-surface text-xs">
                  <span class="material-symbols-outlined text-amber-500 text-sm select-none" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="font-semibold">4.8</span>
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
                  +{{ kos.fasilitas.length - 3 }} lainnya
                </span>
              </div>
            </div>

            <!-- Price & Action -->
            <div class="pt-space-sm border-t border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-base font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                <span class="text-xs text-on-surface-variant font-normal"> / bln</span>
              </div>
              <router-link
                :to="`/kos/${kos.id}`"
                class="px-3 py-1.5 bg-surface-container-low hover:bg-surface-container text-primary text-xs font-semibold rounded-lg transition"
              >
                Lihat Detail
              </router-link>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-xl border border-slate-100">
        <p class="text-sm text-on-surface-variant">Belum ada properti kos yang tersedia.</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const searchQuery = ref('')
const selectedJenis = ref('')
const selectedPriceRange = ref('')
const kosList = ref([])
const loading = ref(false)

// Sample default cover photos from Stitch design assets
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

const handleSearch = () => {
  const query = {}
  if (searchQuery.value) query.q = searchQuery.value
  if (selectedJenis.value) query.jenis = selectedJenis.value

  if (selectedPriceRange.value === 'under500') {
    query.harga_max = 500000
  } else if (selectedPriceRange.value === '500-1000') {
    query.harga_min = 500000
    query.harga_max = 1000000
  } else if (selectedPriceRange.value === 'above1000') {
    query.harga_min = 1000000
  }

  router.push({ path: '/kos', query })
}

const quickSearch = (keyword) => {
  searchQuery.value = keyword
  handleSearch()
}

const fetchPopularKos = async () => {
  loading.value = true
  try {
    const res = await api.get('/kos', { params: { per_page: 6 } })
    kosList.value = res.data.data || []
  } catch (err) {
    // Abaikan error saat fetch
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPopularKos()
})
</script>
