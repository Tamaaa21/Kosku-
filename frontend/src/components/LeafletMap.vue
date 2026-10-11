<template>
  <div class="relative w-full rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
    <div v-if="!hasValidCoords" class="h-64 flex flex-col items-center justify-center bg-slate-100 text-slate-500 p-6 text-center">
      <svg class="w-10 h-10 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      <p class="font-medium">Koordinat peta belum tersedia untuk kos ini.</p>
      <p class="text-xs text-slate-400 mt-1">Alamat: {{ address || '-' }}</p>
    </div>
    <div v-else ref="mapContainer" class="h-72 w-full z-10"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, onBeforeUnmount } from 'vue'
import L from 'leaflet'

const props = defineProps({
  latitude: {
    type: [Number, String],
    default: null,
  },
  longitude: {
    type: [Number, String],
    default: null,
  },
  title: {
    type: String,
    default: 'Lokasi Kos',
  },
  address: {
    type: String,
    default: '',
  },
})

const mapContainer = ref(null)
let mapInstance = null
let markerInstance = null

const hasValidCoords = computed(() => {
  const lat = parseFloat(props.latitude)
  const lng = parseFloat(props.longitude)
  return !isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0
})

const initMap = () => {
  if (!hasValidCoords.value || !mapContainer.value) return

  const lat = parseFloat(props.latitude)
  const lng = parseFloat(props.longitude)

  if (mapInstance) {
    mapInstance.remove()
  }

  mapInstance = L.map(mapContainer.value).setView([lat, lng], 15)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(mapInstance)

  // Standard marker
  markerInstance = L.marker([lat, lng]).addTo(mapInstance)
  markerInstance.bindPopup(`<b>${props.title}</b><br>${props.address}`).openPopup()
}

onMounted(() => {
  initMap()
})

watch(() => [props.latitude, props.longitude], () => {
  initMap()
})

onBeforeUnmount(() => {
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>
