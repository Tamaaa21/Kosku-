<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Pencari Kos</h1>
        <p class="text-sm text-slate-500 mt-1">
          Selamat datang kembali, <span class="font-bold text-slate-700">{{ auth.user?.nama }}</span>. Pantau riwayat pemesanan kamar Anda di sini.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <router-link
          to="/"
          class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Cari Kos Lainnya
        </router-link>
        <button
          @click="fetchBookings"
          :disabled="loading"
          class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold p-2.5 rounded-xl transition"
          title="Segarkan Data"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div v-if="successMsg" class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200 flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = null" class="text-emerald-700 font-bold ml-4">&times;</button>
    </div>
    <div v-if="errorMsg" class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 text-sm border border-rose-200 flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = null" class="text-rose-700 font-bold ml-4">&times;</button>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pemesanan</span>
        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ bookings.length }}</span>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider block">Menunggu Konfirmasi</span>
        <span class="text-2xl font-black text-amber-600 mt-1 block">{{ countStatus('menunggu') }}</span>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Disetujui</span>
        <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ countStatus('disetujui') }}</span>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Selesai / Batal</span>
        <span class="text-2xl font-black text-slate-500 mt-1 block">{{ countStatus('ditolak') + countStatus('dibatalkan') }}</span>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-3 overflow-x-auto">
      <button
        v-for="tab in filterTabs"
        :key="tab.value"
        @click="activeFilter = tab.value"
        :class="[
          'px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap',
          activeFilter === tab.value
            ? 'bg-slate-900 text-white'
            : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="h-28 bg-white border border-slate-200 rounded-2xl animate-pulse"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredBookings.length === 0" class="text-center py-16 bg-white rounded-3xl border border-slate-200">
      <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
      </div>
      <h3 class="text-base font-bold text-slate-800">Tidak ada data pemesanan</h3>
      <p class="text-xs text-slate-500 mt-1">Anda belum memiliki pemesanan kamar pada kategori ini.</p>
      <router-link
        to="/"
        class="inline-block mt-4 text-xs font-bold bg-emerald-600 text-white px-4 py-2 rounded-xl hover:bg-emerald-700 transition"
      >
        Eksplorasi Kos Sekarang
      </router-link>
    </div>

    <!-- Bookings List -->
    <div v-else class="space-y-4">
      <div
        v-for="item in filteredBookings"
        :key="item.id"
        class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col md:flex-row md:items-center justify-between gap-6"
      >
        <div class="flex-1 space-y-2">
          <div class="flex items-center gap-3 flex-wrap">
            <span
              :class="[
                'text-[11px] font-extrabold uppercase px-2.5 py-1 rounded-md border tracking-wider',
                statusBadgeClass(item.status)
              ]"
            >
              {{ item.status }}
            </span>
            <span class="text-xs text-slate-400">ID Pesanan #{{ item.id }}</span>
            <span class="text-xs text-slate-400">&bull; Diajukan: {{ formatDate(item.created_at) }}</span>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-baseline gap-2">
            <h3 class="text-lg font-bold text-slate-900">
              <router-link v-if="item.kos" :to="`/kos/${item.kos.id}`" class="hover:text-emerald-600 transition">
                {{ item.kos.nama }}
              </router-link>
              <span v-else class="text-slate-400">Kos Tidak Tersedia</span>
            </h3>
            <span class="text-sm font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
              Kamar: {{ item.kamar?.nama_kamar || 'Kamar Tidak Diketahui' }}
            </span>
          </div>

          <p v-if="item.kos?.alamat" class="text-xs text-slate-500 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ item.kos.alamat }}
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 text-xs text-slate-600">
            <div>
              <span class="text-slate-400">Mulai Masuk:</span>
              <span class="font-bold text-slate-800 ml-1">{{ formatDateOnly(item.tanggal_pemesanan) }}</span>
            </div>
            <div v-if="item.kos?.pemilik">
              <span class="text-slate-400">Kontak Pemilik:</span>
              <span class="font-bold text-slate-800 ml-1">{{ item.kos.pemilik.nama }} ({{ item.kos.pemilik.no_hp || item.kos.pemilik.email }})</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col sm:items-end justify-between border-t md:border-t-0 pt-4 md:pt-0 gap-3">
          <div class="text-left sm:text-right">
            <span class="text-xs text-slate-400 block">Biaya Sewa / Bulan:</span>
            <span class="text-lg font-black text-slate-900">{{ formatRupiah(item.kamar?.harga || item.kos?.harga) }}</span>
          </div>

          <div class="flex items-center gap-2">
            <!-- Cancel Action if status is menunggu -->
            <button
              v-if="item.status === 'menunggu'"
              @click="cancelBooking(item.id)"
              :disabled="actionLoading === item.id"
              class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-2 rounded-xl transition"
            >
              {{ actionLoading === item.id ? 'Membatalkan...' : 'Batalkan Pesanan' }}
            </button>
            <router-link
              v-if="item.kos"
              :to="`/kos/${item.kos.id}`"
              class="text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition"
            >
              Lihat Kos
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const auth = useAuthStore()

const bookings = ref([])
const loading = ref(false)
const actionLoading = ref(null)
const errorMsg = ref(null)
const successMsg = ref(null)
const activeFilter = ref('semua')

const filterTabs = [
  { label: 'Semua Status', value: 'semua' },
  { label: 'Menunggu', value: 'menunggu' },
  { label: 'Disetujui', value: 'disetujui' },
  { label: 'Ditolak', value: 'ditolak' },
  { label: 'Dibatalkan', value: 'dibatalkan' },
]

const countStatus = (status) => {
  return bookings.value.filter((b) => b.status === status).length
}

const filteredBookings = computed(() => {
  if (activeFilter.value === 'semua') return bookings.value
  return bookings.value.filter((b) => b.status === activeFilter.value)
})

const fetchBookings = async () => {
  loading.value = true
  errorMsg.value = null
  try {
    const res = await api.get('/pemesanan')
    bookings.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat daftar pemesanan.'
  } finally {
    loading.value = false
  }
}

const cancelBooking = async (id) => {
  if (!confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')) return

  actionLoading.value = id
  errorMsg.value = null
  successMsg.value = null
  try {
    await api.patch(`/pemesanan/${id}/status`, { status: 'dibatalkan' })
    successMsg.value = 'Pemesanan berhasil dibatalkan.'
    await fetchBookings()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal membatalkan pemesanan.'
  } finally {
    actionLoading.value = null
  }
}

const statusBadgeClass = (status) => {
  switch (status) {
    case 'menunggu':
      return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'disetujui':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'ditolak':
      return 'bg-rose-50 text-rose-700 border-rose-200'
    case 'dibatalkan':
      return 'bg-slate-100 text-slate-600 border-slate-300'
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchBookings()
})
</script>
