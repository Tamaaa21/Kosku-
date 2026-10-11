<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero / Header -->
    <div class="mb-8 text-center max-w-2xl mx-auto">
      <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
        Temukan Kos Nyaman & Strategis
      </h1>
      <p class="text-slate-600 mt-2 text-sm sm:text-base">
        Jelajahi pilihan hunian kos dengan informasi ketersediaan kamar dan fasilitas terlengkap.
      </p>

      <!-- Main Search Bar -->
      <div class="mt-6 flex items-center bg-white rounded-2xl shadow-sm border border-slate-200 p-2 focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500">
        <svg class="w-5 h-5 text-slate-400 ml-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
          v-model="filters.q"
          @keyup.enter="applyFilters"
          type="text"
          placeholder="Cari nama kos, daerah, atau alamat kampus..."
          class="w-full border-0 focus:ring-0 px-3 py-2 text-sm sm:text-base text-slate-800 placeholder-slate-400"
        />
        <button
          @click="applyFilters"
          class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-5 py-2.5 rounded-xl transition text-sm shrink-0"
        >
          Cari
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Sidebar Filters -->
      <aside class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <h2 class="font-bold text-slate-800 text-base">Filter Pencarian</h2>
          <button @click="resetFilters" class="text-xs text-emerald-600 hover:underline">
            Reset
          </button>
        </div>

        <!-- Tipe Penghuni -->
        <div class="mt-4">
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tipe Kos</label>
          <div class="space-y-1.5">
            <label v-for="type in typeOptions" :key="type.value" class="flex items-center text-sm text-slate-700 cursor-pointer">
              <input
                type="radio"
                name="jenis"
                :value="type.value"
                v-model="filters.jenis"
                @change="applyFilters"
                class="text-emerald-600 focus:ring-emerald-500 h-4 w-4 mr-2"
              />
              {{ type.label }}
            </label>
          </div>
        </div>

        <!-- Rentang Harga -->
        <div class="mt-6">
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Rentang Harga (Rp)</label>
          <div class="space-y-2">
            <input
              type="number"
              v-model.number="filters.harga_min"
              placeholder="Harga Min"
              class="w-full text-xs rounded-xl border-slate-200 p-2.5 focus:border-emerald-500 focus:ring-emerald-500"
            />
            <input
              type="number"
              v-model.number="filters.harga_max"
              placeholder="Harga Max"
              class="w-full text-xs rounded-xl border-slate-200 p-2.5 focus:border-emerald-500 focus:ring-emerald-500"
            />
          </div>
        </div>

        <!-- Fasilitas -->
        <div class="mt-6" v-if="availableFacilities.length > 0">
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Fasilitas</label>
          <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
            <label
              v-for="item in availableFacilities"
              :key="item.id"
              class="flex items-center text-sm text-slate-700 cursor-pointer"
            >
              <input
                type="checkbox"
                :value="item.id"
                v-model="filters.fasilitas"
                @change="applyFilters"
                class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4 mr-2"
              />
              {{ item.nama }}
            </label>
          </div>
        </div>

        <!-- Hanya Kamar Tersedia -->
        <div class="mt-6 pt-4 border-t border-slate-100">
          <label class="flex items-center text-sm text-slate-700 cursor-pointer">
            <input
              type="checkbox"
              v-model="filters.tersedia"
              @change="applyFilters"
              class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4 mr-2"
            />
            <span class="font-medium text-emerald-700">Hanya Kamar Tersedia</span>
          </label>
        </div>

        <!-- Tombol Terapkan -->
        <button
          @click="applyFilters"
          class="w-full mt-6 bg-slate-900 text-white font-medium py-2.5 rounded-xl hover:bg-slate-800 transition text-sm"
        >
          Terapkan Filter
        </button>
      </aside>

      <!-- Main Content / Catalog -->
      <section class="lg:col-span-3">
        <!-- Top Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <p class="text-sm text-slate-600">
            Menampilkan <span class="font-bold text-slate-900">{{ meta.total || kosList.length }}</span> properti kos
          </p>

          <!-- Sorting -->
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Urutkan:</span>
            <select
              v-model="filters.sort"
              @change="applyFilters"
              class="text-xs rounded-xl border-slate-200 py-1.5 pl-3 pr-8 focus:border-emerald-500 focus:ring-emerald-500"
            >
              <option value="terbaru">Terbaru</option>
              <option value="harga_asc">Harga Termurah</option>
              <option value="harga_desc">Harga Termahal</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="i in 4" :key="i" class="bg-white p-6 rounded-2xl border border-slate-200 animate-pulse h-64"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="kosList.length === 0" class="bg-white rounded-2xl p-12 text-center border border-slate-200">
          <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <h3 class="font-bold text-slate-800 text-lg">Tidak ada kos yang cocok</h3>
          <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
            Coba ubah kata kunci pencarian atau sesuaikan rentang filter harga Anda.
          </p>
          <button @click="resetFilters" class="mt-4 inline-block text-xs font-semibold text-emerald-600 hover:underline">
            Bersihkan semua filter
          </button>
        </div>

        <!-- Kos Cards Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div
            v-for="kos in kosList"
            :key="kos.id"
            class="bg-white rounded-2xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition overflow-hidden flex flex-col group"
          >
            <!-- Card Header / Image Placeholder -->
            <div class="h-40 bg-gradient-to-br from-emerald-100 to-slate-100 relative p-4 flex flex-col justify-between">
              <div class="flex items-center justify-between">
                <span
                  :class="badgeClass(kos.jenis)"
                  class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg shadow-sm"
                >
                  {{ kos.jenis }}
                </span>
                <span
                  v-if="kos.kamar_tersedia_count > 0"
                  class="text-xs font-semibold bg-emerald-600 text-white px-2.5 py-0.5 rounded-full shadow-sm"
                >
                  {{ kos.kamar_tersedia_count }} Kamar Tersedia
                </span>
                <span v-else class="text-xs font-semibold bg-rose-500 text-white px-2.5 py-0.5 rounded-full shadow-sm">
                  Penuh
                </span>
              </div>
              <div class="text-emerald-950 font-extrabold text-xl tracking-tight">
                Rp {{ Number(kos.harga).toLocaleString('id-ID') }}
                <span class="text-xs font-normal text-slate-600">/ bulan</span>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-5 flex-1 flex flex-col justify-between">
              <div>
                <h3 class="font-bold text-slate-900 text-lg group-hover:text-emerald-600 transition">
                  {{ kos.nama }}
                </h3>
                <p class="text-xs text-slate-500 mt-1 flex items-start gap-1 line-clamp-2">
                  <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  {{ kos.alamat }}
                </p>

                <!-- Facilities Chips -->
                <div class="mt-4 flex flex-wrap gap-1.5" v-if="kos.fasilitas?.length">
                  <span
                    v-for="fas in kos.fasilitas.slice(0, 3)"
                    :key="fas.id"
                    class="text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md font-medium"
                  >
                    {{ fas.nama }}
                  </span>
                  <span v-if="kos.fasilitas.length > 3" class="text-[11px] text-slate-400 font-medium self-center">
                    +{{ kos.fasilitas.length - 3 }} lainnya
                  </span>
                </div>
              </div>

              <!-- Action Link -->
              <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">Pemilik: {{ kos.pemilik?.nama }}</span>
                <router-link
                  :to="`/kos/${kos.id}`"
                  class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700 group-hover:translate-x-0.5 transition"
                >
                  Lihat Detail &rarr;
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="mt-8 flex justify-center items-center gap-2">
          <button
            @click="changePage(meta.current_page - 1)"
            :disabled="meta.current_page <= 1"
            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium disabled:opacity-40"
          >
            Sebelumnya
          </button>
          <span class="text-xs text-slate-600 font-medium px-2">
            Halaman {{ meta.current_page }} dari {{ meta.last_page }}
          </span>
          <button
            @click="changePage(meta.current_page + 1)"
            :disabled="meta.current_page >= meta.last_page"
            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium disabled:opacity-40"
          >
            Selanjutnya
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '../services/api'

const kosList = ref([])
const availableFacilities = ref([])
const loading = ref(false)
const meta = ref({})

const typeOptions = [
  { label: 'Semua Kategori', value: '' },
  { label: 'Putra', value: 'putra' },
  { label: 'Putri', value: 'putri' },
  { label: 'Campur', value: 'campur' },
]

const filters = reactive({
  q: '',
  jenis: '',
  harga_min: null,
  harga_max: null,
  fasilitas: [],
  tersedia: false,
  sort: 'terbaru',
  page: 1,
})

const badgeClass = (jenis) => {
  switch (jenis) {
    case 'putra':
      return 'bg-blue-100 text-blue-700'
    case 'putri':
      return 'bg-pink-100 text-pink-700'
    default:
      return 'bg-purple-100 text-purple-700'
  }
}

const fetchFacilities = async () => {
  try {
    const res = await api.get('/fasilitas')
    availableFacilities.value = res.data.data || []
  } catch (err) {
    console.error('Failed to load facilities', err)
  }
}

const fetchKos = async () => {
  loading.value = true
  try {
    const params = {
      page: filters.page,
      sort: filters.sort,
    }
    if (filters.q) params.q = filters.q
    if (filters.jenis) params.jenis = filters.jenis
    if (filters.harga_min) params.harga_min = filters.harga_min
    if (filters.harga_max) params.harga_max = filters.harga_max
    if (filters.tersedia) params.tersedia = 1
    if (filters.fasilitas?.length) {
      params.fasilitas = filters.fasilitas
    }

    const res = await api.get('/kos', { params })
    kosList.value = res.data.data || []
    meta.value = res.data.meta || {}
  } catch (err) {
    console.error('Failed to fetch kos', err)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  filters.page = 1
  fetchKos()
}

const resetFilters = () => {
  filters.q = ''
  filters.jenis = ''
  filters.harga_min = null
  filters.harga_max = null
  filters.fasilitas = []
  filters.tersedia = false
  filters.sort = 'terbaru'
  filters.page = 1
  fetchKos()
}

const changePage = (newPage) => {
  if (newPage >= 1 && newPage <= meta.value.last_page) {
    filters.page = newPage
    fetchKos()
  }
}

onMounted(() => {
  fetchFacilities()
  fetchKos()
})
</script>
