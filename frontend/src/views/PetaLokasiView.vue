<template>
  <div class="w-full h-[calc(100vh-4rem)] pt-16 flex flex-col lg:flex-row overflow-hidden bg-background">
    <!-- LEFT PANEL: Listings & Filters (Fixed width desktop ~420px) -->
    <aside class="w-full lg:w-[420px] lg:min-w-[420px] lg:max-w-[420px] h-full flex flex-col bg-surface-container-lowest shadow-md z-20 overflow-hidden border-r border-slate-200">
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
          <span class="material-symbols-outlined absolute left-3 text-secondary text-[20px] pointer-events-none">search</span>
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
            :class="[
              'px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all',
              selectedJenis === chip.val
                ? 'bg-primary text-white shadow-sm'
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
            'group relative p-3 bg-surface-container-lowest rounded-xl cursor-pointer shadow-sm transition-all duration-200 border',
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
              <span
                :class="[
                  'absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[10px] font-semibold',
                  kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                  kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                  'bg-[#EDE9FE] text-[#6D28D9]'
                ]"
              >
                {{ formatJenis(kos.jenis) }}
              </span>
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
                  <span class="material-symbols-outlined text-[13px] text-secondary">location_on</span>
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

              <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-baseline justify-between">
                <div>
                  <span class="text-xs font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                  <span class="text-[10px] text-on-surface-variant">/ bln</span>
                </div>
                <router-link
                  :to="`/kos/${kos.id}`"
                  class="text-[11px] font-semibold text-primary hover:underline flex items-center"
                  @click.stop
                >
                  Detail <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                </router-link>
              </div>
            </div>
          </div>
        </article>
      </div>
    </aside>

    <!-- RIGHT PANEL: Full Height Leaflet Map -->
    <main class="flex-1 h-full relative">
      <div id="full-screen-map" class="w-full h-full"></div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import L from 'leaflet'
import api from '../services/api'

const kosList = ref([])
const loading = ref(false)
const searchQuery = ref('')
const selectedJenis = ref('')
const selectedKos = ref(null)

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

const initMap = () => {
  // Center: Kota Tegal default [-6.8694, 109.1402]
  map = L.map('full-screen-map', {
    zoomControl: true,
  }).setView([-6.8694, 109.1402], 13)

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
      marker.bindPopup(`
        <div style="font-family: Inter, sans-serif; padding: 2px;">
          <strong style="color: #004ac6; font-size: 13px;">${kos.nama}</strong><br/>
          <span style="font-size: 11px; color: #585f67;">${kos.alamat}</span><br/>
          <strong style="color: #0b1c30; font-size: 12px; margin-top: 4px; display: block;">${formatRupiah(kos.harga)} / bln</strong>
          <a href="/kos/${kos.id}" style="display: inline-block; margin-top: 6px; font-size: 11px; color: #2563eb; font-weight: bold; text-decoration: underline;">Lihat Detail &rarr;</a>
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

const selectKos = (kos) => {
  selectedKos.value = kos
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
  initMap()
  await fetchAllKos()
})

onBeforeUnmount(() => {
  if (map) {
    map.remove()
    map = null
  }
})
</script>
