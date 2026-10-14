<template>
  <div class="w-full max-w-5xl mx-auto px-margin py-space-xl pt-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md pb-space-lg">
      <div class="space-y-space-xs">
       
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">
          {{ auth.isPemilik ? 'Kelola Pemesanan Masuk' : '' }}
        </h1>
        <p class="text-xs text-on-surface-variant max-w-2xl">
          {{ auth.isPemilik
            ? 'Pantau dan konfirmasi permohonan sewa kamar dari calon penyewa pada properti kos Anda.'
            : 'Pantau status pengajuan sewa, detail kamar, dan riwayat hunian kos yang pernah Anda ajukan.'
          }}
        </p>
      </div>

      <!-- Quick Metrics Summary -->
      <div class="flex items-center gap-space-sm bg-surface-container-low px-space-md py-space-sm rounded-xl border border-slate-100">
        <div class="flex items-center gap-2">
          <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
          <span class="text-xs text-on-surface-variant font-medium">{{ countStatus('menunggu') }} Menunggu</span>
        </div>
        <div class="w-px h-4 bg-outline-variant"></div>
        <div class="flex items-center gap-2">
          <div class="w-2.5 h-2.5 rounded-full bg-tertiary"></div>
          <span class="text-xs text-on-surface-variant font-medium">{{ countStatus('disetujui') }} Disetujui</span>
        </div>
      </div>
    </div>

    <!-- Tab Filter Status & Mini Search -->
    <div class="flex items-center justify-between gap-space-md overflow-x-auto pb-space-md no-scrollbar">
      <div class="flex items-center gap-space-xs bg-surface-container-low p-1.5 rounded-xl border border-slate-100">
        <button
          v-for="tab in filterTabs"
          :key="tab.value"
          @click="activeFilter = tab.value"
          :class="[
            'px-space-md py-1.5 rounded-lg text-xs transition-all whitespace-nowrap',
            activeFilter === tab.value
              ? 'bg-primary-container text-white shadow-sm font-semibold'
              : 'text-on-surface-variant hover:text-on-surface font-medium'
          ]"
        >
          {{ tab.label }}
          <span
            :class="[
              'ml-1 text-[10px] px-1.5 py-0.5 rounded-full',
              activeFilter === tab.value ? 'bg-white/20 text-white' : 'bg-surface-container text-on-surface-variant'
            ]"
          >
            {{ tab.value === 'semua' ? bookings.length : countStatus(tab.value) }}
          </span>
        </button>
      </div>

      <!-- Search mini input -->
      <div class="hidden sm:flex items-center gap-2 bg-surface-container-lowest px-3 py-1.5 rounded-lg shadow-sm border border-slate-200">
        <PhMagnifyingGlass :size="16" class="text-outline" />
        <input
          v-model="searchFilter"
          type="text"
          :placeholder="auth.isPemilik ? 'Cari nama penyewa / kos...' : 'Cari nama kos...'"
          class="bg-transparent text-xs text-on-surface placeholder:text-outline focus:outline-none w-36 lg:w-48 border-none p-0"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-4 mt-space-sm">
      <div v-for="i in 3" :key="i" class="h-32 bg-white rounded-xl border border-slate-100 animate-pulse"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredBookings.length === 0" class="text-center py-16 bg-surface-container-lowest rounded-xl border border-slate-100 mt-space-sm">
      <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mx-auto text-primary mb-4">
        <PhReceipt :size="32" weight="duotone" />
      </div>
      <h3 class="text-base font-bold text-on-surface">Tidak ada data pemesanan</h3>
      <p class="text-xs text-on-surface-variant mt-1">
        {{ auth.isPemilik ? 'Belum ada permohonan sewa masuk pada kategori ini.' : 'Anda belum memiliki riwayat pemesanan pada kategori ini.' }}
      </p>
      <router-link
        v-if="!auth.isPemilik"
        to="/kos"
        class="inline-block mt-4 text-xs font-semibold bg-primary-container text-white px-4 py-2 rounded-lg hover:bg-primary transition"
      >
        Cari Kos Sekarang
      </router-link>
    </div>

    <!-- Daftar Pemesanan (Cards Stack) -->
    <div v-else class="space-y-space-md mt-space-sm">
      <div
        v-for="item in filteredBookings"
        :key="item.id"
        class="group relative bg-surface-container-lowest rounded-xl p-space-md shadow-sm hover:shadow-md transition-all duration-200 border border-slate-100 overflow-hidden"
      >
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
          <!-- Image and Main Info -->
          <div class="flex items-start sm:items-center gap-space-md">
            <div class="relative w-28 h-24 sm:w-36 sm:h-28 rounded-lg overflow-hidden shrink-0 bg-surface-container">
              <img
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCnt30LyyZlzU1QOvcSRRMGiQ3FJsh4luIKXJX7aqXOjUPxTvMhrxNQRDu2wjgg4Zpze-wPO3sbwBrOI44NhDD87XEhVTvk5o_vsvzP99EXjgB9OByNEwSGlVuxniCRLUSPZIunX09fElg0VYOLRGJNUgIQrE1_i1Zt4qVTrSIufxO97mIpXR4tQt4Mu40AZyAY0adO3qIwZwuU5OhcpZtu1tB57ewE6-lcC8hcwoE_ux6yIt44hm8NQA"
                :alt="item.kos?.nama"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div
                v-if="item.kos?.jenis"
                :class="[
                  'absolute top-1.5 left-1.5 text-[10px] font-semibold px-2 py-0.5 rounded-full',
                  item.kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                  item.kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                  'bg-[#EDE9FE] text-[#6D28D9]'
                ]"
              >
                {{ formatJenis(item.kos.jenis) }}
              </div>
            </div>

            <div class="space-y-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <router-link
                  v-if="item.kos"
                  :to="`/kos/${item.kos.id}`"
                  class="text-base font-bold text-on-surface group-hover:text-primary transition-colors truncate"
                >
                  {{ item.kos.nama }}
                </router-link>
                <span v-else class="text-base font-bold text-slate-400">Kos Tidak Tersedia</span>

                <span
                  :class="[
                    'inline-flex items-center gap-1 text-[10px] font-semibold px-2.5 py-0.5 rounded-full border md:hidden',
                    statusBadgeClass(item.status)
                  ]"
                >
                  {{ formatStatus(item.status) }}
                </span>
              </div>

              <div class="flex items-center gap-1 text-on-surface-variant text-xs">
                <PhMapPin :size="14" class="text-primary shrink-0" />
                <span>{{ item.kos?.alamat || '-' }}</span>
              </div>

              <!-- Schedule & Details -->
              <div class="flex flex-wrap items-center gap-y-1 gap-x-2 pt-1 text-on-surface-variant text-xs">
                <div class="flex items-center gap-1 bg-surface-container-low px-2 py-0.5 rounded">
                  <PhCalendar :size="12" class="text-outline" />
                  <span>Masuk: <strong class="text-on-surface font-semibold">{{ formatDateOnly(item.tanggal_pemesanan) }}</strong></span>
                </div>
                <div class="flex items-center gap-1 bg-surface-container-low px-2 py-0.5 rounded">
                  <PhClock :size="12" class="text-outline" />
                  <span>Durasi: <strong class="text-on-surface font-semibold capitalize">{{ item.durasi_jumlah || 1 }} {{ item.durasi_tipe || 'bulan' }}</strong></span>
                </div>
                <div class="flex items-center gap-1 bg-surface-container-low px-2 py-0.5 rounded">
                  <PhDoor :size="12" class="text-outline" />
                  <span>Kamar: <strong class="text-on-surface font-semibold">{{ item.kamar?.nama_kamar || '-' }}</strong></span>
                </div>
              </div>

              <!-- Contact Info: Pemilik or Penyewa depending on current role -->
              <div v-if="auth.isPemilik && item.pengguna" class="text-[11px] text-on-surface-variant pt-1 bg-surface-container-low p-2 rounded-lg">
                Pemohon Sewa: <strong class="text-on-surface">{{ item.pengguna.nama }}</strong> &bull;
                HP/WA: <a :href="'https://wa.me/' + item.pengguna.no_hp" target="_blank" class="text-primary font-semibold hover:underline">{{ item.pengguna.no_hp || '-' }}</a>
              </div>
              <div v-else-if="!auth.isPemilik && item.kos?.pemilik" class="text-[11px] text-on-surface-variant pt-1">
                Kontak Pemilik: <span class="font-semibold text-on-surface">{{ item.kos.pemilik.nama }} ({{ item.kos.pemilik.no_hp || '-' }})</span>
              </div>
            </div>
          </div>

          <!-- Price & Actions Right Desktop -->
          <div class="flex flex-col md:items-end justify-between border-t md:border-t-0 pt-3 md:pt-0 gap-3">
            <div class="hidden md:block">
              <span
                :class="[
                  'inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full border',
                  statusBadgeClass(item.status)
                ]"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(item.status)"></span>
                {{ formatStatus(item.status) }}
              </span>
            </div>

            <div class="text-left md:text-right">
              <span class="text-[11px] text-on-surface-variant block">Total Biaya:</span>
              <span class="text-lg font-black text-primary">{{ formatRupiah(item.total_harga || item.kamar?.harga || item.kos?.harga) }}</span>
              <span class="text-[11px] text-on-surface-variant"> ({{ item.durasi_jumlah || 1 }} {{ item.durasi_tipe || 'bulan' }})</span>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap justify-end">
              <!-- If PENCARI & status disetujui: tombol Beri Ulasan -->
              <router-link
                v-if="!auth.isPemilik && item.status === 'disetujui' && item.kos_id"
                :to="{ path: `/kos/${item.kos_id}`, query: { tab: 'ulasan' } }"
                class="text-xs font-semibold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1 shadow-xs"
                title="Beri ulasan dan rating untuk kos ini"
              >
                <PhStar :size="16" weight="fill" class="text-amber-600" />
                <span>Beri Ulasan</span>
              </router-link>

              <!-- If PEMILIK: Approve / Reject buttons for pending bookings -->
              <template v-if="auth.isPemilik">
                <div v-if="item.status === 'menunggu'" class="flex items-center gap-2">
                  <button
                    @click="updateBookingStatus(item.id, 'disetujui')"
                    :disabled="actionLoading === item.id"
                    class="text-xs font-semibold bg-tertiary-container hover:bg-tertiary text-white px-3 py-1.5 rounded-lg transition shadow-sm"
                  >
                    {{ actionLoading === item.id ? 'Memproses...' : 'Setujui' }}
                  </button>
                  <button
                    @click="updateBookingStatus(item.id, 'ditolak')"
                    :disabled="actionLoading === item.id"
                    class="text-xs font-semibold text-error hover:bg-error-container/40 border border-error/30 px-3 py-1.5 rounded-lg transition"
                  >
                    Tolak
                  </button>
                </div>
              </template>

              <!-- If PENCARI: Cancel button for pending bookings -->
              <template v-else>
                <button
                  v-if="item.status === 'menunggu'"
                  @click="cancelBooking(item.id)"
                  :disabled="actionLoading === item.id"
                  class="text-xs font-semibold text-error hover:bg-error-container/40 border border-error/30 px-3 py-1.5 rounded-lg transition"
                >
                  {{ actionLoading === item.id ? 'Membatalkan...' : 'Batalkan Pesanan' }}
                </button>
              </template>

              <!-- WhatsApp Action Button -->
              <button
                v-if="!auth.isPemilik && item.kos?.pemilik"
                @click="chatPemilikWA(item)"
                type="button"
                class="text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5"
                title="Hubungi Pemilik Kos via WhatsApp"
              >
                <PhWhatsappLogo :size="16" weight="fill" class="text-emerald-600" />
                <span>Chat WA</span>
              </button>

              <button
                v-else-if="auth.isPemilik && item.pengguna"
                @click="chatPenyewaWA(item)"
                type="button"
                class="text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5"
                title="Hubungi Calon Penyewa via WhatsApp"
              >
                <PhWhatsappLogo :size="16" weight="fill" class="text-emerald-600" />
                <span>Chat WA</span>
              </button>

              <router-link
                v-if="item.kos"
                :to="`/kos/${item.kos.id}`"
                class="text-xs font-semibold bg-surface-container-low hover:bg-surface-container text-primary px-3 py-1.5 rounded-lg transition"
              >
                Lihat Kos
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  PhReceipt,
  PhMagnifyingGlass,
  PhMapPin,
  PhCalendar,
  PhClock,
  PhDoor,
  PhStar,
  PhWhatsappLogo,
} from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import api from '../services/api'
import { openWhatsAppChat, buildTenantInquiryTemplate, buildOwnerContactTenantTemplate } from '../utils/whatsapp'

const auth = useAuthStore()
const ui = useUiStore()

const bookings = ref([])
const loading = ref(false)
const actionLoading = ref(null)
const activeFilter = ref('semua')
const searchFilter = ref('')

const filterTabs = [
  { label: 'Semua', value: 'semua' },
  { label: 'Menunggu', value: 'menunggu' },
  { label: 'Disetujui', value: 'disetujui' },
  { label: 'Ditolak', value: 'ditolak' },
  { label: 'Dibatalkan', value: 'dibatalkan' },
]

const countStatus = (status) => {
  return bookings.value.filter((b) => b.status === status).length
}

const filteredBookings = computed(() => {
  return bookings.value.filter((b) => {
    const matchesStatus = activeFilter.value === 'semua' || b.status === activeFilter.value
    const matchesSearch = !searchFilter.value ||
      (b.kos?.nama && b.kos.nama.toLowerCase().includes(searchFilter.value.toLowerCase())) ||
      (b.pengguna?.nama && b.pengguna.nama.toLowerCase().includes(searchFilter.value.toLowerCase()))
    return matchesStatus && matchesSearch
  })
})

const fetchBookings = async () => {
  loading.value = true
  try {
    const res = await api.get('/pemesanan')
    bookings.value = res.data.data || []
  } catch (err) {
    // Abaikan
  } finally {
    loading.value = false
  }
}

// Pencari cancel action
const cancelBooking = async (id) => {
  const confirmed = await ui.confirm({
    title: 'Batalkan Pengajuan Sewa?',
    message: 'Apakah Anda yakin ingin membatalkan pengajuan sewa ini? Tindakan ini tidak dapat dibatalkan.',
    confirmText: 'Ya, Batalkan',
    cancelText: 'Kembali',
    confirmType: 'danger'
  })
  if (!confirmed) return

  actionLoading.value = id
  try {
    await api.patch(`/pemesanan/${id}/status`, { status: 'dibatalkan' })
    ui.success('Pengajuan sewa berhasil dibatalkan.')
    await fetchBookings()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal membatalkan pemesanan.')
  } finally {
    actionLoading.value = null
  }
}

// Pemilik approve/reject action
const updateBookingStatus = async (id, status) => {
  const label = status === 'disetujui' ? 'menyetujui' : 'menolak'
  const confirmed = await ui.confirm({
    title: `${status === 'disetujui' ? 'Setujui' : 'Tolak'} Permohonan Sewa?`,
    message: `Apakah Anda yakin ingin ${label} permohonan sewa ini?`,
    confirmText: status === 'disetujui' ? 'Ya, Setujui' : 'Ya, Tolak',
    cancelText: 'Batal',
    confirmType: status === 'disetujui' ? 'primary' : 'danger'
  })
  if (!confirmed) return

  actionLoading.value = id
  try {
    await api.patch(`/pemesanan/${id}/status`, { status })
    ui.success(`Permohonan sewa berhasil di-${status}!`)
    await fetchBookings()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal memperbarui status pemesanan.')
  } finally {
    actionLoading.value = null
  }
}

const formatJenis = (jenis) => {
  if (!jenis) return '-'
  return jenis.charAt(0).toUpperCase() + jenis.slice(1)
}

const formatStatus = (status) => {
  switch (status) {
    case 'menunggu': return 'Menunggu Konfirmasi'
    case 'disetujui': return 'Disetujui'
    case 'ditolak': return 'Ditolak'
    case 'dibatalkan': return 'Dibatalkan'
    default: return status
  }
}

const statusBadgeClass = (status) => {
  switch (status) {
    case 'menunggu': return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'disetujui': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'ditolak': return 'bg-rose-50 text-rose-700 border-rose-200'
    case 'dibatalkan': return 'bg-slate-100 text-slate-600 border-slate-200'
    default: return 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

const statusDotClass = (status) => {
  switch (status) {
    case 'menunggu': return 'bg-amber-500 animate-pulse'
    case 'disetujui': return 'bg-emerald-500'
    case 'ditolak': return 'bg-rose-500'
    case 'dibatalkan': return 'bg-slate-400'
    default: return 'bg-slate-400'
  }
}

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

const chatPemilikWA = (item) => {
  const phone = item.kos?.pemilik?.telepon || item.kos?.pemilik?.no_hp
  const message = buildTenantInquiryTemplate({
    ownerName: item.kos?.pemilik?.nama || 'Pemilik Kos',
    kosName: item.kos?.nama || 'Kos',
    roomName: item.kamar?.nama_kamar || '',
    bookingId: item.id,
  })
  openWhatsAppChat(phone, message)
}

const chatPenyewaWA = (item) => {
  const phone = item.pengguna?.telepon || item.pengguna?.no_hp
  const message = buildOwnerContactTenantTemplate({
    tenantName: item.pengguna?.nama || 'Calon Penyewa',
    kosName: item.kos?.nama || 'Kos',
    roomName: item.kamar?.nama_kamar || '',
    bookingId: item.id,
  })
  openWhatsAppChat(phone, message)
}

onMounted(() => {
  fetchBookings()
})
</script>
