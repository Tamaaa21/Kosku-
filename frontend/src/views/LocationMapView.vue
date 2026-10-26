<template>
  <div class="w-full h-[calc(100vh-4rem)] pt-16 flex flex-col lg:flex-row overflow-hidden bg-background relative">
    <!-- MOBILE VIEW TOGGLE TABS (Visible only below lg) -->
    <div class="lg:hidden z-30 bg-surface-container-lowest border-b border-slate-200 px-3 py-2 flex items-center justify-between gap-2 shadow-xs">
      <div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-xl flex-1 max-w-sm mx-auto">
        <button
          type="button"
          @click="setMobileView('list')"
          class="flex-1 py-1.5 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition-all"
          :class="mobileView === 'list' ? 'bg-surface-container-lowest text-primary shadow-xs' : 'text-on-surface-variant hover:text-on-surface'"
        >
          <PhListBullets :size="16" weight="bold" />
          <span>Daftar Kos ({{ filteredKosList.length }})</span>
        </button>
        <button
          type="button"
          @click="setMobileView('map')"
          class="flex-1 py-1.5 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition-all"
          :class="mobileView === 'map' ? 'bg-surface-container-lowest text-primary shadow-xs' : 'text-on-surface-variant hover:text-on-surface'"
        >
          <PhMapTrifold :size="16" weight="bold" />
          <span>Peta Lokasi</span>
        </button>
      </div>
    </div>

    <!-- LEFT PANEL: Listings & Filters -->
    <aside
      class="w-full lg:w-[420px] lg:min-w-[420px] lg:max-w-[420px] h-full flex flex-col bg-surface-container-lowest shadow-md z-20 overflow-hidden border-r border-slate-200 transition-all"
      :class="mobileView === 'list' ? 'flex' : 'hidden lg:flex'"
    >
      <!-- Panel Top Header & Search Area -->
      <div class="p-space-md pb-space-sm bg-surface-container-lowest flex flex-col gap-space-sm border-b border-slate-100">
        <div class="flex items-center justify-between">
          <div>
            <span class="text-[11px] text-primary font-semibold uppercase tracking-wider">Eksplorasi Area</span>
            <h1 class="text-lg font-bold text-on-surface tracking-tight">Daftar Kos di Sekitar</h1>
          </div>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-primary text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
            {{ filteredKosList.length }} Kos Aktif
          </span>
        </div>

        <!-- Search Bar -->
        <div class="relative flex items-center">
          <PhMagnifyingGlass :size="20" class="absolute left-3 text-secondary pointer-events-none" />
          <input
            v-model="searchQuery"
            type="text"
            class="w-full h-10 pl-10 pr-3 bg-surface-container-low text-on-surface text-xs rounded-lg focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all border-none"
            placeholder="Cari jalan, kampus, atau nama kos..."
          />
        </div>

        <!-- Quick Filter Chips Horizontal Scroll -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-0.5 no-scrollbar">
          <button
            v-for="chip in [
              { label: 'Semua', val: '' },
              { label: 'Putri', val: 'putri' },
              { label: 'Putra', val: 'putra' },
              { label: 'Campur', val: 'campur' }
            ]"
            :key="chip.val"
            @click="selectedJenis = chip.val"
            class="min-h-[36px] px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all flex items-center"
            :class="[
              selectedJenis === chip.val
                ? 'bg-primary text-white shadow-xs'
                : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'
            ]"
          >
            {{ chip.label }}
          </button>
        </div>
      </div>

      <!-- Result Count indicator -->
      <div class="px-space-md py-2 bg-surface-container-low flex items-center justify-between border-b border-slate-100">
        <span class="text-xs text-on-surface-variant">Menampilkan {{ filteredKosList.length }} hunian</span>
      </div>

      <!-- Scrollable Kos Cards List -->
      <div class="flex-1 overflow-y-auto p-space-md space-y-3 bg-surface-container-low/50">
        <div v-if="loading" class="text-center py-10 text-xs text-secondary animate-pulse">
          Memuat data peta dan sebaran kos...
        </div>

        <div v-else-if="filteredKosList.length === 0" class="text-center py-10 text-xs text-secondary">
          Tidak ditemukan kos di area ini.
        </div>

        <article
          v-for="kos in filteredKosList"
          :key="kos.id"
          @click="selectKos(kos)"
          :class="[
            'group relative p-3 bg-surface-container-lowest rounded-xl cursor-pointer shadow-xs transition-all duration-200 border',
            selectedKos?.id === kos.id
              ? 'border-primary ring-1 ring-primary bg-primary/5'
              : 'border-slate-100 hover:border-slate-300'
          ]"
        >
          <div class="flex gap-3">
            <div class="relative w-24 h-24 rounded-lg overflow-hidden shrink-0 bg-surface-container">
              <img
                :src="getImageForKos(kos.id)"
                :alt="kos.nama"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <GenderBadge :jenis="kos.jenis" size="sm" class="absolute top-1.5 left-1.5" />
            </div>

            <div class="flex-1 min-w-0 flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between">
                  <h2 class="text-sm font-bold text-on-surface truncate group-hover:text-primary transition-colors">
                    {{ kos.nama }}
                  </h2>
                  <span
                    v-if="selectedKos?.id === kos.id"
                    class="w-2.5 h-2.5 rounded-full bg-primary shrink-0"
                    title="Terpilih di peta"
                  ></span>
                </div>

                <p class="text-[11px] text-on-surface-variant flex items-center gap-1 mt-0.5 truncate">
                  <PhMapPin :size="13" class="text-secondary shrink-0" />
                  {{ kos.alamat }}
                </p>

                <div class="flex items-center gap-1 mt-1.5 flex-wrap">
                  <span
                    v-for="fas in (kos.fasilitas || []).slice(0, 2)"
                    :key="fas.id"
                    class="inline-flex items-center px-1.5 py-0.5 rounded bg-surface-container text-primary text-[10px] font-medium"
                  >
                    {{ fas.nama }}
                  </span>
                </div>
              </div>

              <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between gap-2">
                <div>
                  <span class="text-xs font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                  <span class="text-[10px] text-on-surface-variant">/ bln</span>
                </div>
                <div class="flex items-center gap-2.5">
                  <a
                    :href="getGoogleMapsUrl(kos)"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="min-h-[32px] px-2 py-1 rounded text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 hover:underline"
                    @click.stop
                    title="Buka rute navigasi di Google Maps"
                  >
                    <PhNavigationArrow :size="13" weight="bold" />
                    <span>Maps</span>
                  </a>
                  <router-link
                    :to="`/kos/${kos.id}`"
                    class="min-h-[32px] px-2 py-1 rounded text-[11px] font-semibold text-primary hover:underline flex items-center gap-0.5"
                    @click.stop
                  >
                    Detail <PhCaretRight :size="12" weight="bold" />
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>

      <!-- Mobile Floating Map Button on List View -->
      <div class="lg:hidden absolute bottom-5 left-1/2 -translate-x-1/2 z-30">
        <button
          type="button"
          @click="setMobileView('map')"
          class="min-h-[44px] px-5 py-2.5 rounded-full bg-primary hover:bg-primary-container text-white text-xs font-bold shadow-lg flex items-center gap-2 transition active:scale-95"
        >
          <PhMapTrifold :size="18" weight="bold" />
          <span>Buka Peta Interaktif</span>
        </button>
      </div>
    </aside>

    <!-- RIGHT PANEL: Full Height Leaflet Map -->
    <main
      class="flex-1 h-full relative"
      :class="mobileView === 'map' ? 'flex' : 'hidden lg:flex'"
    >
      <div id="full-screen-map" class="w-full h-full"></div>

      <!-- Mobile Floating List Button on Map View -->
      <div class="lg:hidden absolute bottom-5 left-1/2 -translate-x-1/2 z-[1000]">
        <button
          type="button"
          @click="setMobileView('list')"
          class="min-h-[44px] px-5 py-2.5 rounded-full bg-surface-container-lowest text-primary text-xs font-bold shadow-xl border border-slate-200/80 flex items-center gap-2 transition active:scale-95"
        >
          <PhListBullets :size="18" weight="bold" />
          <span>Lihat Daftar Kos ({{ filteredKosList.length }})</span>
        </button>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import L from 'leaflet'
import {
  PhListBullets,
  PhMapTrifold,
  PhMagnifyingGlass,
  PhMapPin,
  PhNavigationArrow,
  PhCaretRight,
} from '@phosphor-icons/vue'
import GenderBadge from '../components/GenderBadge.vue'
import api from '../services/api'
import { getGoogleMapsUrl } from '../utils/whatsapp'

const router = useRouter()

const kosList = ref([])
const loading = ref(false)
const searchQuery = ref('')
const selectedJenis = ref('')
const selectedKos = ref(null)
const mobileView = ref('list') // 'list' | 'map'

let map = null
const markers = new Map()

const samplePhotos = [
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBtAMGcNImL28Wkcksd1pB136MBTXtxAeUZZdLwny2E0TfcGGRkdrMA_JR6pVnktaHUu8Ffua13FdN3HdgZd29vhkeFDWM4WJtQJFMuZndi8o1HB2GXyn8ikhIgK45eAlZmqqiEVYkjMe7_m-Cvw0nxPY6cKLGKCrWp2Ch2dtBIlIqQb5pKjNVS1WaRhbnQPFyDftN7_uvVGXkv3Z_JUnsJpPxReDktVkZYw0eJ1YRW7caBLYvFdhslDw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCOitClHgLDKT01wozYNVTlNFfj9k7oJNfa4q2kNrDk86lv3RxjjBUoPS6XVwSRz1fJt2Njq_q5ULmoPtKJ6tl88vke1gmGCs0RASAks5YEI7qsJO_F2vpf0H79xaVOLcj6daJIZt4_vYQD98RpawFS-fn8uc4Gg8SAZ6U1phc_LSoddYK5bxVMFELhOTo3bzl4rS6xJsZs6VtkRf6E9koZW5_wbWiwXvorevgASvvlZRP5P-63L62c2Q',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCq7lWrhImOr_FOsjNzINLkTN7b_5naoC4qeVNpyv6ugH4fnh9cc_IfxyVcenUHoVsykW_bUzYT96tFdwcpt9ghlRd6ji-wQjUfun67OQOq3T1S6XB2tOmVgvzykn_ra-KV7mRpZLQ09KBeHgpQj_rkik9wD8A8tT5piCUPOvWmwULPLmec2WUaeEHWcK2rVuT-D0jdYE8LleRBoUP8Vwlz6LcasWJw3DEPRxzciDC7dsnsgoobvasOfw',
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

const filteredKosList = computed(() => {
  return kosList.value.filter((kos) => {
    const matchesSearch = !searchQuery.value ||
      kos.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      kos.alamat.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesJenis = !selectedJenis.value || kos.jenis === selectedJenis.value

    return matchesSearch && matchesJenis
  })
})

const setMobileView = async (view) => {
  mobileView.value = view
  if (view === 'map' && map) {
    await nextTick()
    map.invalidateSize()
  }
}

const initMap = () => {
  // Center Indonesia default [-2.5489, 118.0149] zoom 5
  map = L.map('full-screen-map', {
    zoomControl: true,
  }).setView([-2.5489, 118.0149], 5)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map)

  renderMarkers()
}

const renderMarkers = () => {
  if (!map) return

  // Clear existing
  markers.forEach((marker) => map.removeLayer(marker))
  markers.clear()

  const bounds = []

  filteredKosList.value.forEach((kos) => {
    if (kos.latitude && kos.longitude) {
      const lat = parseFloat(kos.latitude)
      const lng = parseFloat(kos.longitude)

      const marker = L.marker([lat, lng]).addTo(map)
      const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`
      marker.bindPopup(`
        <div style="font-family: Satoshi, system-ui, sans-serif; padding: 4px; max-width: 200px;">
          <strong style="color: #0B478B; font-size: 13px; display: block; line-height: 1.2;">${kos.nama}</strong>
          <span style="font-size: 11px; color: #64748b; display: block; margin-top: 3px;">${kos.alamat}</span>
          <strong style="color: #0f172a; font-size: 12px; margin-top: 5px; display: block;">${formatRupiah(kos.harga)} / bln</strong>
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; padding-top: 6px; border-top: 1px solid #e2e8f0;">
            <button onclick="window.__kostaraNavigateTo(${kos.id})" style="background: none; border: none; padding: 0; font-size: 11px; color: #0B478B; font-weight: bold; cursor: pointer; text-decoration: underline;">Detail &rarr;</button>
            <a href="${mapsUrl}" target="_blank" rel="noopener noreferrer" style="font-size: 11px; color: #16a34a; font-weight: bold; text-decoration: underline;">Buka di Maps &nearr;</a>
          </div>
        </div>
      `)

      marker.on('click', () => {
        selectedKos.value = kos
      })

      markers.set(kos.id, marker)
      bounds.push([lat, lng])
    }
  })

  if (bounds.length > 0) {
    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 })
  }
}

const selectKos = async (kos) => {
  selectedKos.value = kos
  // In mobile, switch to map view to show where it is
  if (window.innerWidth < 1024) {
    mobileView.value = 'map'
    await nextTick()
    if (map) {
      map.invalidateSize()
    }
  }

  if (kos.latitude && kos.longitude && map) {
    const lat = parseFloat(kos.latitude)
    const lng = parseFloat(kos.longitude)
    map.flyTo([lat, lng], 16, { duration: 1.2 })

    const marker = markers.get(kos.id)
    if (marker) {
      marker.openPopup()
    }
  }
}

const fetchAllKos = async () => {
  loading.value = true
  try {
    const res = await api.get('/kos', { params: { per_page: 50 } })
    kosList.value = res.data.data || []
    await nextTick()
    renderMarkers()
  } catch (err) {
    // Abaikan
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  window.__kostaraNavigateTo = (id) => {
    router.push(`/kos/${id}`)
  }

  initMap()
  await fetchAllKos()
})

onBeforeUnmount(() => {
  delete window.__kostaraNavigateTo
  if (map) {
    map.remove()
    map = null
  }
})
</script>
