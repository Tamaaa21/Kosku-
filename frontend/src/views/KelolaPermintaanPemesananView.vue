<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md mb-8">
      <div>
        <div class="flex items-center gap-1.5 text-primary text-xs font-bold uppercase tracking-wider mb-1">
          <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
          <span>Modul Reservasi KosKu</span>
        </div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Permintaan Pemesanan</h1>
        <p class="text-xs text-on-surface-variant mt-0.5">
          Kelola permintaan sewa dari calon penghuni secara cepat dan transparan.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <router-link
          to="/dashboard/pemilik"
          class="h-10 px-4 rounded-lg bg-surface-container text-on-surface text-xs font-semibold flex items-center gap-1.5 hover:bg-surface-container-high transition shadow-sm"
        >
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          <span>Dashboard</span>
        </router-link>
        <button
          @click="fetchBookings"
          :disabled="loading"
          class="h-10 px-4 rounded-lg bg-surface-container-lowest border border-slate-200 text-on-surface text-xs font-semibold flex items-center gap-2 hover:bg-slate-50 transition shadow-sm disabled:opacity-50"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': loading }">refresh</span>
          <span>Segarkan Data</span>
        </button>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div
      v-if="successMsg"
      class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-medium border border-emerald-200 flex items-center justify-between"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
        <span>{{ successMsg }}</span>
      </div>
      <button @click="successMsg = ''" class="text-emerald-700 font-bold ml-4 text-base">&times;</button>
    </div>
    <div
      v-if="errorMsg"
      class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-800 text-xs font-medium border border-rose-200 flex items-center justify-between"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
        <span>{{ errorMsg }}</span>
      </div>
      <button @click="errorMsg = ''" class="text-rose-700 font-bold ml-4 text-base">&times;</button>
    </div>

    <!-- Filter Bar & Search -->
    <div class="bg-surface-container-lowest rounded-xl p-space-sm shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-space-sm mb-6">
      <div class="flex flex-wrap items-center gap-1.5">
        <button
          @click="activeStatusFilter = 'all'"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition',
            activeStatusFilter === 'all'
              ? 'bg-primary-container text-white shadow-sm'
              : 'text-on-surface-variant hover:bg-surface-container-low'
          ]"
        >
          <span>Semua</span>
          <span
            :class="[
              'px-2 py-0.5 text-[10px] font-bold rounded-full',
              activeStatusFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'
            ]"
          >
            {{ allBookings.length }}
          </span>
        </button>

        <button
          @click="activeStatusFilter = 'menunggu'"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition',
            activeStatusFilter === 'menunggu'
              ? 'bg-amber-500 text-white shadow-sm'
              : 'text-on-surface-variant hover:bg-surface-container-low'
          ]"
        >
          <span>Menunggu</span>
          <span
            :class="[
              'px-2 py-0.5 text-[10px] font-bold rounded-full',
              activeStatusFilter === 'menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800'
            ]"
          >
            {{ countMenunggu }}
          </span>
        </button>

        <button
          @click="activeStatusFilter = 'disetujui'"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition',
            activeStatusFilter === 'disetujui'
              ? 'bg-emerald-600 text-white shadow-sm'
              : 'text-on-surface-variant hover:bg-surface-container-low'
          ]"
        >
          <span>Disetujui</span>
          <span
            :class="[
              'px-2 py-0.5 text-[10px] font-bold rounded-full',
              activeStatusFilter === 'disetujui' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800'
            ]"
          >
            {{ countDisetujui }}
          </span>
        </button>

        <button
          @click="activeStatusFilter = 'ditolak'"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition',
            activeStatusFilter === 'ditolak'
              ? 'bg-rose-600 text-white shadow-sm'
              : 'text-on-surface-variant hover:bg-surface-container-low'
          ]"
        >
          <span>Ditolak</span>
          <span
            :class="[
              'px-2 py-0.5 text-[10px] font-bold rounded-full',
              activeStatusFilter === 'ditolak' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800'
            ]"
          >
            {{ countDitolak }}
          </span>
        </button>
      </div>

      <div class="flex items-center gap-2 px-3 py-1.5 bg-surface-container-low rounded-lg w-full sm:w-auto">
        <span class="material-symbols-outlined text-[18px] text-outline">search</span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama atau kos..."
          class="bg-transparent text-on-surface text-xs placeholder:text-outline focus:outline-none w-44"
        />
      </div>
    </div>

    <!-- Booking Requests Table Card -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="overflow-x-auto w-full">
        <table class="w-full text-left">
          <thead class="bg-surface-container-low/60 text-on-surface-variant text-[11px] uppercase tracking-wider font-semibold select-none">
            <tr>
              <th class="py-3.5 px-4 w-12 text-center">No</th>
              <th class="py-3.5 px-4 min-w-[200px]">Calon Penghuni</th>
              <th class="py-3.5 px-4 min-w-[190px]">Kos & Kamar</th>
              <th class="py-3.5 px-4 min-w-[180px]">Periode Sewa</th>
              <th class="py-3.5 px-4 min-w-[140px]">Total Biaya</th>
              <th class="py-3.5 px-4 min-w-[110px] text-center">Status</th>
              <th class="py-3.5 px-4 min-w-[160px] text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-on-surface">
            <tr v-if="loading" class="text-center">
              <td colspan="7" class="py-12 text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[28px] text-primary">progress_activity</span>
                <p class="mt-2 text-xs">Memuat permintaan pemesanan...</p>
              </td>
            </tr>

            <tr v-else-if="filteredBookings.length === 0" class="text-center">
              <td colspan="7" class="py-12 text-slate-400">
                <span class="material-symbols-outlined text-[36px] text-slate-300">inbox</span>
                <p class="mt-2 text-xs font-semibold text-slate-600">Tidak ada permintaan pemesanan yang sesuai.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Semua permintaan sewa kamar telah ditinjau atau belum ada permintaan baru.</p>
              </td>
            </tr>

            <tr
              v-for="(booking, index) in filteredBookings"
              :key="booking.id"
              class="hover:bg-surface-container-low/40 transition group"
            >
              <td class="py-4 px-4 text-center font-medium text-secondary">
                {{ index + 1 }}
              </td>

              <!-- Calon Penghuni -->
              <td class="py-4 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                    {{ booking.user?.nama ? booking.user.nama.charAt(0).toUpperCase() : 'U' }}
                  </div>
                  <div class="flex flex-col">
                    <span class="font-semibold text-on-surface leading-tight">
                      {{ booking.user?.nama || 'Calon Penghuni' }}
                    </span>
                    <span class="text-[11px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                      <span class="material-symbols-outlined text-[13px] text-tertiary">call</span>
                      <span>{{ booking.user?.telepon || booking.user?.email || '-' }}</span>
                    </span>
                  </div>
                </div>
              </td>

              <!-- Kos & Kamar -->
              <td class="py-4 px-4">
                <div class="flex flex-col">
                  <span class="font-semibold text-primary leading-tight">
                    {{ booking.kos?.nama || 'Nama Kos' }}
                  </span>
                  <span class="text-[11px] text-on-surface-variant mt-0.5">
                    {{ booking.kamar?.nama_kamar || 'Kamar Unit' }}
                  </span>
                </div>
              </td>

              <!-- Periode & Durasi -->
              <td class="py-4 px-4">
                <div class="flex flex-col">
                  <span class="font-semibold text-on-surface leading-tight">
                    Mulai: {{ formatDate(booking.tanggal_mulai) }}
                  </span>
                  <span class="text-[11px] text-outline mt-0.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                    {{ booking.durasi_sewa_bulan || booking.durasi_bulan || 1 }} Bulan
                  </span>
                </div>
              </td>

              <!-- Total Biaya -->
              <td class="py-4 px-4">
                <span class="font-bold text-on-surface">
                  {{ formatRupiah(booking.total_harga) }}
                </span>
              </td>

              <!-- Status -->
              <td class="py-4 px-4 text-center">
                <span
                  :class="[
                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold capitalize',
                    booking.status === 'menunggu' ? 'bg-amber-50 text-amber-700' :
                    booking.status === 'diterima' ? 'bg-emerald-50 text-emerald-700' :
                    booking.status === 'ditolak' ? 'bg-rose-50 text-rose-700' :
                    'bg-slate-100 text-slate-700'
                  ]"
                >
                  <span
                    :class="[
                      'w-1.5 h-1.5 rounded-full',
                      booking.status === 'menunggu' ? 'bg-amber-500' :
                      booking.status === 'diterima' ? 'bg-emerald-500' :
                      booking.status === 'ditolak' ? 'bg-rose-500' : 'bg-slate-400'
                    ]"
                  ></span>
                  {{ booking.status }}
                </span>
              </td>

              <!-- Aksi -->
              <td class="py-4 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button
                    @click="openDetail(booking)"
                    class="p-1.5 rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition"
                    title="Lihat Detail Lengkap"
                  >
                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                  </button>

                  <template v-if="booking.status === 'menunggu'">
                    <button
                      @click="updateStatus(booking, 'disetujui')"
                      class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-semibold text-[11px] hover:bg-emerald-700 transition flex items-center gap-1 shadow-sm"
                      title="Setujui Permintaan Sewa"
                    >
                      <span class="material-symbols-outlined text-[14px]">check</span>
                      <span>Setujui</span>
                    </button>

                    <button
                      @click="updateStatus(booking, 'ditolak')"
                      class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 font-semibold text-[11px] hover:bg-rose-100 transition flex items-center gap-1 border border-rose-200"
                      title="Tolak Permintaan Sewa"
                    >
                      <span class="material-symbols-outlined text-[14px]">close</span>
                      <span>Tolak</span>
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination / Footer Info -->
      <div class="p-space-md bg-surface-container-lowest border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-space-md select-none">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <span class="font-bold text-on-surface">{{ filteredBookings.length }}</span> dari
          <span class="font-bold text-on-surface">{{ allBookings.length }}</span> total reservasi
        </span>
      </div>
    </div>

    <!-- Modal Detail Pemesanan -->
    <div
      v-if="selectedBooking"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-[24px]">receipt_long</span>
            </div>
            <div>
              <h3 class="text-lg font-bold text-on-surface">Detail Reservasi #{{ selectedBooking.id }}</h3>
              <p class="text-xs text-on-surface-variant">
                Dibuat pada {{ formatDate(selectedBooking.created_at) }}
              </p>
            </div>
          </div>
          <button
            @click="selectedBooking = null"
            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
          >
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <div class="space-y-4 text-xs">
          <!-- Calon Penghuni Info -->
          <div class="p-4 rounded-xl bg-surface-container-low/50 border border-slate-100 space-y-2">
            <h4 class="font-bold text-on-surface uppercase tracking-wider text-[11px] text-primary">Informasi Calon Penghuni</h4>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Nama:</span>
              <span class="font-semibold text-on-surface">{{ selectedBooking.user?.nama }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Email:</span>
              <span class="text-on-surface">{{ selectedBooking.user?.email || '-' }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Nomor WhatsApp / HP:</span>
              <span class="font-semibold text-primary">{{ selectedBooking.user?.telepon || '-' }}</span>
            </div>
          </div>

          <!-- Unit Kos & Kamar -->
          <div class="p-4 rounded-xl bg-surface-container-low/50 border border-slate-100 space-y-2">
            <h4 class="font-bold text-on-surface uppercase tracking-wider text-[11px] text-primary">Unit Properti</h4>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Kos:</span>
              <span class="font-semibold text-on-surface">{{ selectedBooking.kos?.nama }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Kamar:</span>
              <span class="font-semibold text-on-surface">{{ selectedBooking.kamar?.nama_kamar }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Tanggal Mulai Sewa:</span>
              <span class="font-semibold text-on-surface">{{ formatDate(selectedBooking.tanggal_mulai) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-secondary">Durasi Sewa:</span>
              <span class="font-semibold text-on-surface">{{ selectedBooking.durasi_sewa_bulan || selectedBooking.durasi_bulan || 1 }} Bulan</span>
            </div>
            <div class="flex items-center justify-between border-t border-slate-200/60 pt-2 mt-2">
              <span class="font-bold text-on-surface">Total Biaya Sewa:</span>
              <span class="font-bold text-primary text-sm">{{ formatRupiah(selectedBooking.total_harga) }}</span>
            </div>
          </div>

          <!-- Catatan -->
          <div v-if="selectedBooking.catatan" class="p-3 rounded-xl bg-slate-50 border border-slate-100">
            <span class="text-secondary block font-semibold mb-1">Catatan dari Pemesan:</span>
            <p class="text-slate-700 italic">"{{ selectedBooking.catatan }}"</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button
              @click="selectedBooking = null"
              class="h-10 px-5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container transition text-xs font-semibold"
            >
              Tutup
            </button>
            <template v-if="selectedBooking.status === 'menunggu'">
              <button
                @click="updateStatus(selectedBooking, 'ditolak')"
                class="h-10 px-4 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold hover:bg-rose-100 transition"
              >
                Tolak Reservasi
              </button>
              <button
                @click="updateStatus(selectedBooking, 'disetujui')"
                class="h-10 px-5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 shadow-sm transition"
              >
                Setujui Reservasi
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const loading = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

const allBookings = ref([])
const activeStatusFilter = ref('all')
const searchQuery = ref('')
const selectedBooking = ref(null)

const fetchBookings = async () => {
  loading.value = true
  errorMsg.value = ''
  try {
    const res = await api.get('/pemesanan')
    allBookings.value = res.data?.data || []
  } catch (err) {
    errorMsg.value = 'Gagal memuat permintaan pemesanan.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchBookings()
})

const filteredBookings = computed(() => {
  return allBookings.value.filter((b) => {
    const matchStatus =
      activeStatusFilter.value === 'all' || b.status === activeStatusFilter.value

    const matchSearch =
      !searchQuery.value ||
      (b.user?.nama && b.user.nama.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (b.kos?.nama && b.kos.nama.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (b.kamar?.nama_kamar && b.kamar.nama_kamar.toLowerCase().includes(searchQuery.value.toLowerCase()))

    return matchStatus && matchSearch
  })
})

const countMenunggu = computed(() => {
  return allBookings.value.filter((b) => b.status === 'menunggu').length
})

const countDisetujui = computed(() => {
  return allBookings.value.filter((b) => b.status === 'disetujui').length
})

const countDitolak = computed(() => {
  return allBookings.value.filter((b) => b.status === 'ditolak').length
})

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch (e) {
    return dateStr
  }
}

const openDetail = (booking) => {
  selectedBooking.value = booking
}

const updateStatus = async (booking, newStatus) => {
  const confirmMsg =
    newStatus === 'disetujui'
      ? `Setujui reservasi dari ${booking.user?.nama || 'calon penghuni'}?`
      : `Tolak reservasi dari ${booking.user?.nama || 'calon penghuni'}?`

  if (!confirm(confirmMsg)) return

  try {
    await api.patch(`/pemesanan/${booking.id}/status`, {
      status: newStatus
    })
    successMsg.value = `Reservasi #${booking.id} berhasil diubah menjadi ${newStatus}.`
    if (selectedBooking.value && selectedBooking.value.id === booking.id) {
      selectedBooking.value.status = newStatus
    }
    await fetchBookings()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal mengubah status pemesanan.'
  }
}
</script>
