<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- Greeting & Quick Actions Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md mb-8">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="text-xs uppercase tracking-wider text-primary font-bold">Ringkasan Pengelola</span>
          <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
          <span class="text-xs text-on-surface-variant">Update terkini</span>
        </div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">
          Selamat Datang, {{ auth.user?.nama }}! 👋
        </h1>
        <p class="text-xs text-on-surface-variant mt-1">
          Berikut adalah ringkasan performa dan aktivitas properti kos Anda.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <router-link
          to="/dashboard/pemilik/kos"
          class="px-4 py-2 bg-surface-container text-primary text-xs font-semibold rounded-lg hover:bg-surface-container-high transition shadow-sm flex items-center gap-1.5"
        >
          <span class="material-symbols-outlined text-[18px]">apartment</span>
          <span>Data Kos</span>
        </router-link>

        <router-link
          to="/dashboard/pemilik/kamar"
          class="px-4 py-2 bg-surface-container text-primary text-xs font-semibold rounded-lg hover:bg-surface-container-high transition shadow-sm flex items-center gap-1.5"
        >
          <span class="material-symbols-outlined text-[18px]">meeting_room</span>
          <span>Kelola Kamar</span>
        </router-link>

        <router-link
          to="/dashboard/pemilik/pemesanan"
          class="px-4 py-2 bg-primary-container text-white text-xs font-semibold rounded-lg hover:bg-primary transition shadow-sm flex items-center gap-1.5"
        >
          <span class="material-symbols-outlined text-[18px]">event_available</span>
          <span>Pemesanan Masuk</span>
          <span
            v-if="pendingCount > 0"
            class="ml-1 px-1.5 py-0.5 rounded-full bg-error text-white text-[10px] font-bold"
          >
            {{ pendingCount }}
          </span>
        </router-link>
      </div>
    </div>

    <!-- 4 Stats Cards in Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mb-8">
      <!-- Card 1: Total Kos -->
      <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-on-surface-variant">Total Properti Kos</span>
          <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[22px]">apartment</span>
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-on-surface">{{ myKosList.length }}</span>
          <span class="text-[11px] text-tertiary font-semibold flex items-center gap-0.5 bg-surface-container-low px-2 py-0.5 rounded-full">
            <span class="material-symbols-outlined text-[14px]">check</span> Aktif
          </span>
        </div>
      </div>

      <!-- Card 2: Total Kamar -->
      <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-on-surface-variant">Total Kamar</span>
          <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[22px]">bed</span>
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-on-surface">{{ totalRoomsCount }}</span>
          <span class="text-xs text-on-surface-variant">
            <strong class="text-tertiary">{{ totalAvailableRooms }}</strong> Tersedia / <strong class="text-primary">{{ totalFilledRooms }}</strong> Terisi
          </span>
        </div>
      </div>

      <!-- Card 3: Permintaan Baru -->
      <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-on-surface-variant">Permintaan Baru</span>
          <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[22px]">mark_chat_unread</span>
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-primary">{{ pendingCount }}</span>
          <span v-if="pendingCount > 0" class="text-[11px] bg-error-container text-on-error-container px-2 py-0.5 rounded-full font-bold animate-pulse">
            Perlu Respon
          </span>
          <span v-else class="text-[11px] text-on-surface-variant bg-surface-container px-2 py-0.5 rounded-full">
            Semua Ditinjau
          </span>
        </div>
      </div>

      <!-- Card 4: Total Pemesanan -->
      <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-on-surface-variant">Total Transaksi</span>
          <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[22px]">calendar_month</span>
          </div>
        </div>
        <div class="mt-4 flex items-baseline justify-between">
          <span class="text-3xl font-black text-on-surface">{{ bookingsList.length }}</span>
          <span class="text-[11px] text-on-surface-variant">Sepanjang Waktu</span>
        </div>
      </div>
    </div>

    <!-- Main Section (2 Columns Split) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
      <!-- Kolom Kiri: Properti Kos Anda (8 cols) -->
      <div class="lg:col-span-8 flex flex-col gap-space-md">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-bold text-on-surface">Properti Kos Anda</h2>
            <p class="text-xs text-on-surface-variant">Daftar kos yang Anda daftarkan di platform KosKu</p>
          </div>
          <router-link
            to="/dashboard/pemilik/kos"
            class="text-xs font-semibold text-primary hover:underline flex items-center gap-1"
          >
            <span>Kelola Seluruh Kos</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </router-link>
        </div>

        <div v-if="loading" class="py-10 text-center animate-pulse text-xs text-secondary">
          Memuat properti kos...
        </div>

        <div v-else-if="myKosList.length === 0" class="text-center py-12 bg-white rounded-xl border border-slate-100">
          <span class="material-symbols-outlined text-4xl text-outline mb-2">apartment</span>
          <p class="text-xs text-on-surface-variant">Anda belum mendaftarkan properti kos.</p>
          <router-link
            to="/dashboard/pemilik/kos"
            class="mt-3 inline-block px-4 py-2 bg-primary-container text-white text-xs font-semibold rounded-lg"
          >
            + Tambah Kos Pertama
          </router-link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
          <div
            v-for="kos in myKosList.slice(0, 4)"
            :key="kos.id"
            class="bg-surface-container-lowest rounded-xl p-4 shadow-sm border border-slate-100 flex flex-col justify-between group hover:shadow-md transition"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <span
                  :class="[
                    'text-[10px] font-bold uppercase px-2 py-0.5 rounded-full',
                    kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                    kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                    'bg-[#EDE9FE] text-[#6D28D9]'
                  ]"
                >
                  {{ kos.jenis }}
                </span>
                <span class="text-[11px] text-tertiary font-bold flex items-center gap-0.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Aktif
                </span>
              </div>

              <h3 class="text-base font-bold text-on-surface group-hover:text-primary transition-colors">
                {{ kos.nama }}
              </h3>
              <p class="text-xs text-on-surface-variant truncate mt-0.5">{{ kos.alamat }}</p>

              <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-on-surface-variant">Unit Kamar:</span>
                <span class="font-bold text-primary">{{ kos.kamar?.length || 0 }} Kamar</span>
              </div>
              <div class="flex items-center justify-between text-xs mt-1">
                <span class="text-on-surface-variant">Harga Acuan:</span>
                <span class="font-bold text-on-surface">{{ formatRupiah(kos.harga) }} / bln</span>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
              <router-link
                to="/dashboard/pemilik/kamar"
                class="px-3 py-1.5 bg-surface-container text-primary text-xs font-semibold rounded-lg hover:bg-surface-container-high transition"
              >
                Kamar
              </router-link>
              <router-link
                :to="`/kos/${kos.id}`"
                class="px-3 py-1.5 bg-primary-container text-white text-xs font-semibold rounded-lg hover:bg-primary transition"
              >
                Lihat Listing
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Permintaan Masuk Terbaru (4 cols) -->
      <div class="lg:col-span-4 bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
          <div>
            <h2 class="text-sm font-bold text-on-surface">Permintaan Terbaru</h2>
            <p class="text-[11px] text-on-surface-variant">Perlu konfirmasi pemilik</p>
          </div>
          <router-link
            to="/dashboard/pemilik/pemesanan"
            class="text-xs text-primary font-semibold hover:underline"
          >
            Lihat Semua
          </router-link>
        </div>

        <div v-if="pendingBookings.length === 0" class="py-8 text-center text-xs text-on-surface-variant">
          Tidak ada permohonan yang menunggu respon saat ini.
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="b in pendingBookings.slice(0, 3)"
            :key="b.id"
            class="p-3 rounded-lg bg-surface-container-low/60 border border-slate-100 flex flex-col gap-2"
          >
            <div class="flex items-start justify-between">
              <div>
                <strong class="text-xs font-bold text-on-surface block">{{ b.pengguna?.nama || '-' }}</strong>
                <span class="text-[11px] text-on-surface-variant">{{ b.kos?.nama }} &bull; Kamar {{ b.kamar?.nama_kamar }}</span>
              </div>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">
                Menunggu
              </span>
            </div>

            <div class="text-[11px] text-secondary">
              Mulai Masuk: <strong class="text-on-surface">{{ formatDateOnly(b.tanggal_pemesanan) }}</strong>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
              <button
                @click="updateStatus(b.id, 'disetujui')"
                class="px-2.5 py-1 rounded bg-tertiary-container hover:bg-tertiary text-white text-[11px] font-bold transition"
              >
                Setujui
              </button>
              <button
                @click="updateStatus(b.id, 'ditolak')"
                class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold transition border border-rose-200"
              >
                Tolak
              </button>
            </div>
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

const myKosList = ref([])
const bookingsList = ref([])
const loading = ref(false)

const totalRoomsCount = computed(() => {
  return myKosList.value.reduce((acc, k) => acc + (k.kamar ? k.kamar.length : 0), 0)
})

const totalAvailableRooms = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    if (!k.kamar) return acc
    return acc + k.kamar.filter((r) => r.status === 'tersedia').length
  }, 0)
})

const totalFilledRooms = computed(() => {
  return totalRoomsCount.value - totalAvailableRooms.value
})

const pendingCount = computed(() => {
  return bookingsList.value.filter((b) => b.status === 'menunggu').length
})

const pendingBookings = computed(() => {
  return bookingsList.value.filter((b) => b.status === 'menunggu')
})

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const [resKos, resBookings] = await Promise.all([
      api.get('/kos', { params: { pemilik_id: auth.user?.id } }),
      api.get('/pemesanan'),
    ])
    myKosList.value = resKos.data.data || []
    bookingsList.value = resBookings.data.data || []
  } catch (err) {
    // Abaikan
  } finally {
    loading.value = false
  }
}

const updateStatus = async (id, status) => {
  const label = status === 'disetujui' ? 'menyetujui' : 'menolak'
  if (!confirm(`Apakah Anda yakin ingin ${label} permohonan ini?`)) return
  try {
    await api.patch(`/pemesanan/${id}/status`, { status })
    await fetchDashboardData()
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal memperbarui status.')
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
