<template>
  <div class="space-y-2">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <label class="text-xs font-semibold text-on-surface flex items-center gap-1.5">
          <span class="material-symbols-outlined text-[16px] text-primary">pin_drop</span>
          <span>Titik Lokasi Peta (Responsif)</span>
        </label>
        <p class="text-[11px] text-on-surface-variant">
          Geser pin marker atau klik di peta untuk menentukan koordinat kos secara akurat.
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="detectCurrentLocation"
          :disabled="detectingGps"
          class="h-8 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold border border-emerald-200 transition flex items-center gap-1 shrink-0 disabled:opacity-60"
          title="Gunakan lokasi GPS perangkat saya saat ini"
        >
          <span
            v-if="detectingGps"
            class="material-symbols-outlined animate-spin text-[14px]"
          >
            progress_activity
          </span>
          <span v-else class="material-symbols-outlined text-[14px]">my_location</span>
          <span>{{ detectingGps ? 'Mendeteksi...' : 'Lokasi GPS Saya' }}</span>
        </button>

        <button
          v-if="address"
          type="button"
          @click="searchByAddress"
          :disabled="searchingAddress"
          class="h-8 px-3 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary text-[11px] font-bold transition flex items-center gap-1 shrink-0 disabled:opacity-60"
          title="Cari koordinat berdasarkan alamat yang diketik"
        >
          <span
            v-if="searchingAddress"
            class="material-symbols-outlined animate-spin text-[14px]"
          >
            progress_activity
          </span>
          <span v-else class="material-symbols-outlined text-[14px]">search</span>
          <span>Cari dari Alamat</span>
        </button>
      </div>
    </div>

    <!-- Map Container -->
    <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 shadow-xs bg-slate-100">
      <div ref="mapContainer" class="h-56 sm:h-64 w-full z-10"></div>

      <!-- Floating Coordinate Badge -->
      <div class="absolute bottom-2 left-2 z-20 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-mono px-2.5 py-1 rounded-md shadow-md flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Lat: {{ currentLat !== null ? currentLat.toFixed(6) : '-' }}</span>
        <span>&bull;</span>
        <span>Lng: {{ currentLng !== null ? currentLng.toFixed(6) : '-' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount, nextTick } from 'vue'
import L from 'leaflet'

// Fix default Leaflet icon paths in Vite build
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
})

const props = defineProps({
  latitude: {
    type: [Number, String],
    default: null,
  },
  longitude: {
    type: [Number, String],
    default: null,
  },
  address: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:latitude', 'update:longitude', 'update:address'])

const mapContainer = ref(null)
let mapInstance = null
let markerInstance = null

const detectingGps = ref(false)
const searchingAddress = ref(false)

const currentLat = ref(props.latitude ? parseFloat(props.latitude) : -7.7828)
const currentLng = ref(props.longitude ? parseFloat(props.longitude) : 110.3671)

const initMap = () => {
  if (!mapContainer.value) return

  const initialLat = props.latitude ? parseFloat(props.latitude) : -7.7828
  const initialLng = props.longitude ? parseFloat(props.longitude) : 110.3671

  currentLat.value = initialLat
  currentLng.value = initialLng

  if (mapInstance) {
    mapInstance.remove()
  }

  mapInstance = L.map(mapContainer.value, {
    center: [initialLat, initialLng],
    zoom: props.latitude && props.longitude ? 16 : 13,
    scrollWheelZoom: true,
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(mapInstance)

  // Create draggable marker
  markerInstance = L.marker([initialLat, initialLng], {
    draggable: true,
  }).addTo(mapInstance)

  markerInstance.bindPopup('<b>Titik Kos Anda</b><br>Geser ke posisi yang tepat').openPopup()

  // Marker drag event
  markerInstance.on('dragend', (e) => {
    const pos = e.target.getLatLng()
    updatePosition(pos.lat, pos.lng)
  })

  // Map click event
  mapInstance.on('click', (e) => {
    markerInstance.setLatLng(e.latlng)
    updatePosition(e.latlng.lat, e.latlng.lng)
  })

  // Ensure tiles render completely after layout calculation
  setTimeout(() => {
    if (mapInstance) {
      mapInstance.invalidateSize()
    }
  }, 300)
}

const updatePosition = (lat, lng) => {
  const roundedLat = Number(lat.toFixed(7))
  const roundedLng = Number(lng.toFixed(7))
  currentLat.value = roundedLat
  currentLng.value = roundedLng
  emit('update:latitude', roundedLat)
  emit('update:longitude', roundedLng)
}

const detectCurrentLocation = () => {
  if (!navigator.geolocation) {
    alert('Geolokasi tidak didukung oleh browser Anda.')
    return
  }

  detectingGps.value = true
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      detectingGps.value = false
      const lat = pos.coords.latitude
      const lng = pos.coords.longitude
      updatePosition(lat, lng)

      if (mapInstance && markerInstance) {
        mapInstance.setView([lat, lng], 16)
        markerInstance.setLatLng([lat, lng])
        markerInstance.openPopup()
      }
    },
    (err) => {
      detectingGps.value = false
      alert('Tidak dapat mendeteksi lokasi GPS: ' + err.message)
    },
    { enableHighAccuracy: true, timeout: 10000 }
  )
}

const searchByAddress = async () => {
  const query = props.address?.trim()
  if (!query) return

  searchingAddress.value = true
  try {
    const res = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`,
      { headers: { 'Accept-Language': 'id' } }
    )
    const data = await res.json()
    if (data && data.length > 0) {
      const lat = parseFloat(data[0].lat)
      const lng = parseFloat(data[0].lon)
      updatePosition(lat, lng)

      if (mapInstance && markerInstance) {
        mapInstance.setView([lat, lng], 16)
        markerInstance.setLatLng([lat, lng])
        markerInstance.openPopup()
      }
    } else {
      alert('Alamat tidak ditemukan di peta. Anda dapat menggeser pin secara manual.')
    }
  } catch (err) {
    console.error('Geocoding error:', err)
  } finally {
    searchingAddress.value = false
  }
}

watch(
  () => [props.latitude, props.longitude],
  ([newLat, newLng]) => {
    if (newLat && newLng && mapInstance && markerInstance) {
      const lat = parseFloat(newLat)
      const lng = parseFloat(newLng)
      if (lat !== currentLat.value || lng !== currentLng.value) {
        currentLat.value = lat
        currentLng.value = lng
        markerInstance.setLatLng([lat, lng])
        mapInstance.setView([lat, lng], 16)
      }
    }
  }
)

onMounted(() => {
  nextTick(() => {
    initMap()
  })
})

onBeforeUnmount(() => {
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})

defineExpose({
  invalidateSize: () => {
    if (mapInstance) {
      mapInstance.invalidateSize()
    }
  },
})
</script>
