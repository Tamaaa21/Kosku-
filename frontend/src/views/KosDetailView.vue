<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back button -->
    <router-link to="/" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-emerald-600 mb-6 transition">
      &larr; Kembali ke Katalog
    </router-link>

    <div v-if="loading" class="bg-white p-8 rounded-2xl border border-slate-200 animate-pulse h-96"></div>

    <div v-else-if="!kos" class="bg-white p-12 text-center rounded-2xl border border-slate-200">
      <h2 class="text-xl font-bold text-slate-800">Properti Kos Tidak Ditemukan</h2>
      <p class="text-sm text-slate-500 mt-2">Data kos mungkin telah dihapus atau tidak tersedia.</p>
    </div>

    <div v-else class="space-y-8">
      <!-- Header Banner -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-3">
              <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-lg bg-emerald-100 text-emerald-800">
                Kos {{ kos.jenis }}
              </span>
              <span class="text-xs text-slate-400">ID: #{{ kos.id }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">
              {{ kos.nama }}
            </h1>
            <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
              <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ kos.alamat }}
            </p>
          </div>

          <!-- Price & Contact Box -->
          <div class="bg-slate-50 border border-slate-200 p-5 rounded-2xl shrink-0 text-right sm:min-w-[220px]">
            <span class="text-xs text-slate-500">Harga Acuan Mulai</span>
            <div class="text-2xl font-black text-emerald-600">
              Rp {{ Number(kos.harga).toLocaleString('id-ID') }}
            </div>
            <span class="text-[11px] text-slate-400">/ bulan</span>
            <div class="mt-3 pt-3 border-t border-slate-200 text-xs text-slate-600 text-left">
              <span class="text-slate-400 block text-[10px] uppercase font-bold">Pemilik Kos</span>
              <span class="font-semibold text-slate-800">{{ kos.pemilik?.nama }}</span>
            </div>
          </div>
        </div>

        <!-- Deskripsi -->
        <div class="mt-6 pt-6 border-t border-slate-100">
          <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Deskripsi Properti</h2>
          <p class="text-sm text-slate-600 mt-2 leading-relaxed whitespace-pre-line">
            {{ kos.deskripsi || 'Tidak ada deskripsi rinci untuk kos ini.' }}
          </p>
        </div>

        <!-- Fasilitas Tags -->
        <div class="mt-6 pt-6 border-t border-slate-100">
          <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Fasilitas Tersedia</h2>
          <div v-if="kos.fasilitas?.length" class="flex flex-wrap gap-2">
            <span
              v-for="fas in kos.fasilitas"
              :key="fas.id"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs font-medium"
            >
              <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ fas.nama }}
            </span>
          </div>
          <p v-else class="text-xs text-slate-400">Belum ada data fasilitas.</p>
        </div>
      </div>

      <!-- Grid: Peta Lokasi & Tabel Ketersediaan Kamar -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Peta Lokasi (Leaflet) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
          <div>
            <h2 class="text-base font-bold text-slate-900 mb-2">Lokasi Peta</h2>
            <p class="text-xs text-slate-500 mb-4">Koordinat diperoleh via OpenStreetMap Geocoding.</p>
            <LeafletMap
              :latitude="kos.latitude"
              :longitude="kos.longitude"
              :title="kos.nama"
              :address="kos.alamat"
            />
          </div>
        </div>

        <!-- Tabel Unit Kamar -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-bold text-slate-900">Unit Kamar</h2>
              <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">
                Total {{ kos.kamar?.length || 0 }} Unit
              </span>
            </div>

            <div v-if="!kos.kamar || kos.kamar.length === 0" class="text-center py-8 text-xs text-slate-400">
              Belum ada data unit kamar yang didaftarkan.
            </div>

            <div v-else class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
              <div
                v-for="kamar in kos.kamar"
                :key="kamar.id"
                class="py-3.5 flex items-center justify-between gap-4"
              >
                <div>
                  <h4 class="font-bold text-slate-800 text-sm">{{ kamar.nama_kamar }}</h4>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-emerald-600 font-semibold">
                      Rp {{ Number(kamar.harga).toLocaleString('id-ID') }}/bln
                    </span>
                    <span
                      :class="kamar.status === 'tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'"
                      class="text-[10px] font-bold px-2 py-0.5 rounded uppercase"
                    >
                      {{ kamar.status }}
                    </span>
                  </div>
                </div>

                <!-- Tombol Ajukan Sewa (Hanya jika tersedia) -->
                <div>
                  <button
                    v-if="kamar.status === 'tersedia'"
                    @click="openBookingModal(kamar)"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs px-3.5 py-2 rounded-xl transition shadow-sm"
                  >
                    Ajukan Sewa
                  </button>
                  <span v-else class="text-xs text-slate-400 font-medium px-2 py-1">
                    Penuh
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-lg font-bold text-slate-900">Form Pengajuan Sewa</h3>
        <p class="text-xs text-slate-500 mt-1">
          Unit: <span class="font-semibold text-slate-800">{{ selectedKamar?.nama_kamar }}</span> -
          Rp {{ Number(selectedKamar?.harga).toLocaleString('id-ID') }}/bln
        </p>

        <div v-if="bookingError" class="mt-4 p-3 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-200">
          {{ bookingError }}
        </div>

        <div class="mt-4">
          <label class="block text-xs font-semibold text-slate-600 mb-1">Rencana Tanggal Mulai Ngekos</label>
          <input
            type="date"
            v-model="bookingForm.tanggal_pemesanan"
            :min="todayDate"
            class="w-full text-sm rounded-xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
          />
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="showModal = false"
            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition"
          >
            Batal
          </button>
          <button
            @click="submitBooking"
            :disabled="bookingLoading"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 text-xs font-bold rounded-xl transition shadow-sm disabled:opacity-50"
          >
            {{ bookingLoading ? 'Mengirim...' : 'Kirim Pengajuan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import LeafletMap from '../components/LeafletMap.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const kos = ref(null)
const loading = ref(true)

const showModal = ref(false)
const selectedKamar = ref(null)
const bookingLoading = ref(false)
const bookingError = ref(null)

const todayDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const bookingForm = reactive({
  tanggal_pemesanan: todayDate.value,
})

const fetchDetail = async () => {
  loading.value = true
  try {
    const res = await api.get(`/kos/${route.params.id}`)
    kos.value = res.data.data
  } catch (err) {
    console.error('Gagal mengambil data kos', err)
  } finally {
    loading.value = false
  }
}

const openBookingModal = (kamar) => {
  if (!auth.isAuthenticated) {
    alert('Silakan login terlebih dahulu untuk mengajukan pemesanan.')
    return router.push({ name: 'login', query: { redirect: route.fullPath } })
  }

  if (auth.role !== 'pencari') {
    return alert('Hanya akun dengan peran Pencari yang dapat mengajukan pemesanan kamar.')
  }

  selectedKamar.value = kamar
  bookingError.value = null
  showModal.value = true
}

const submitBooking = async () => {
  bookingLoading.value = true
  bookingError.value = null
  try {
    await api.post('/pemesanan', {
      kamar_id: selectedKamar.value.id,
      tanggal_pemesanan: bookingForm.tanggal_pemesanan,
    })
    showModal.value = false
    alert('Pengajuan pemesanan berhasil dikirim! Anda dapat memantau statusnya di Dashboard Pencari.')
    router.push({ name: 'pencari-dashboard' })
  } catch (err) {
    bookingError.value = err.response?.data?.message || 'Gagal mengajukan pemesanan.'
  } finally {
    bookingLoading.value = false
  }
}

onMounted(() => {
  fetchDetail()
})
</script>
