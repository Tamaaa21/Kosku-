<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md mb-8">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-bold text-on-surface tracking-tight">Daftar Penghuni Kos</h1>
          <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
            Mitra Pemilik
          </span>
        </div>
        <p class="text-xs text-on-surface-variant mt-0.5">
          Pantau data penghuni aktif, masa berlaku sewa, kirim pengingat WhatsApp, atau proses check-out kamar.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <button
          @click="fetchTenants"
          :disabled="loading"
          class="h-10 px-4 rounded-xl bg-surface-container-lowest border border-slate-200 text-on-surface text-xs font-semibold flex items-center gap-2 hover:bg-slate-50 transition shadow-xs disabled:opacity-50 cursor-pointer"
        >
          <PhArrowClockwise :size="16" :class="{ 'animate-spin': loading }" />
          <span>Segarkan Data</span>
        </button>
      </div>
    </div>

    <!-- Alert / Toast Notification -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="toastMessage"
        :class="[
          'mb-6 p-4 rounded-2xl text-xs font-semibold border flex items-center justify-between shadow-xs',
          toastType === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' :
          toastType === 'error' ? 'bg-rose-50 text-rose-800 border-rose-200' :
          'bg-blue-50 text-blue-800 border-blue-200'
        ]"
      >
        <div class="flex items-center gap-2.5">
          <PhCheckCircle v-if="toastType === 'success'" :size="18" weight="fill" class="text-emerald-600 shrink-0" />
          <PhWarningCircle v-else-if="toastType === 'error'" :size="18" weight="fill" class="text-rose-600 shrink-0" />
          <PhInfo v-else :size="18" weight="fill" class="text-blue-600 shrink-0" />
          <span>{{ toastMessage }}</span>
        </div>
        <button @click="toastMessage = ''" class="font-bold text-base px-1 hover:opacity-70">&times;</button>
      </div>
    </transition>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <!-- 1. Total Penghuni Aktif -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <PhUsers :size="24" weight="bold" />
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Penghuni Aktif</p>
          <p class="text-2xl font-black text-slate-900 mt-0.5">{{ summary.total_penghuni || 0 }}</p>
          <span class="text-[10px] text-emerald-600 font-medium">Sedang menempati kamar</span>
        </div>
      </div>

      <!-- 2. Kamar Terisi -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <PhBed :size="24" weight="bold" />
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kamar Terisi</p>
          <p class="text-2xl font-black text-slate-900 mt-0.5">{{ summary.kamar_terisi || 0 }}</p>
          <span class="text-[10px] text-blue-600 font-medium">Total unit terhuni</span>
        </div>
      </div>

      <!-- 3. Siap Masuk / Serah Kunci -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
          <PhKey :size="24" weight="bold" />
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Siap Masuk</p>
          <p class="text-2xl font-black text-slate-900 mt-0.5">{{ summary.total_siap_masuk || 0 }}</p>
          <span class="text-[10px] text-indigo-600 font-medium">DP terbayar / Lunas</span>
        </div>
      </div>

      <!-- 4. Jatuh Tempo Segera (H-7) -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 border border-slate-100 shadow-xs flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <PhHourglassLow :size="24" weight="bold" />
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jatuh Tempo (H-7)</p>
          <p class="text-2xl font-black text-amber-700 mt-0.5">{{ summary.total_jatuh_tempo || 0 }}</p>
          <span class="text-[10px] text-amber-600 font-medium">Perlu pengingat perpanjangan</span>
        </div>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-xs border border-slate-100 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <!-- Filter Tabs -->
      <div class="flex flex-wrap items-center gap-1.5">
        <button
          v-for="tab in filterTabs"
          :key="tab.id"
          @click="activeFilter = tab.id"
          :class="[
            'px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-1.5',
            activeFilter === tab.id
              ? 'bg-primary text-white shadow-xs'
              : 'text-slate-600 hover:bg-slate-100'
          ]"
        >
          <span>{{ tab.label }}</span>
          <span
            v-if="tab.count !== null"
            :class="[
              'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
              activeFilter === tab.id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'
            ]"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>

      <!-- Search Input -->
      <div class="relative w-full md:w-72">
        <PhMagnifyingGlass :size="16" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari penghuni, kamar, kos..."
          class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
        />
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold"
        >
          &times;
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="py-16 text-center bg-white rounded-3xl border border-slate-100 shadow-xs">
      <div class="inline-block animate-spin text-primary mb-3">
        <PhArrowClockwise :size="32" />
      </div>
      <p class="text-xs text-slate-500 font-semibold">Memuat daftar penghuni kos...</p>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredTenants.length === 0"
      class="py-16 text-center bg-white rounded-3xl border border-slate-100 shadow-xs p-6"
    >
      <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-4">
        <PhUsers :size="32" />
      </div>
      <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Penghuni Ditemukan</h3>
      <p class="text-xs text-slate-500 max-w-md mx-auto mb-4">
        {{ searchQuery ? 'Tidak ada penghuni yang cocok dengan kata kunci pencarian Anda.' : 'Belum ada penghuni aktif pada kategori ini. Calon penghuni yang telah membayar DP atau melunasi sewa akan muncul di sini secara otomatis.' }}
      </p>
      <button
        v-if="searchQuery || activeFilter !== 'all'"
        @click="resetFilters"
        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition"
      >
        Reset Filter
      </button>
    </div>

    <!-- Tenants Grid / List -->
    <div v-else class="space-y-4">
      <div
        v-for="tenant in filteredTenants"
        :key="tenant.id"
        class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition duration-200 flex flex-col lg:flex-row lg:items-center justify-between gap-5"
      >
        <!-- Tenant & Room Details -->
        <div class="flex items-start gap-4 flex-1 min-w-0">
          <!-- Avatar -->
          <div class="w-13 h-13 rounded-2xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-black text-sm shrink-0 overflow-hidden">
            <img
              v-if="tenant.pengguna?.foto"
              :src="tenant.pengguna.foto"
              :alt="tenant.pengguna.nama"
              class="w-full h-full object-cover"
            />
            <span v-else>
              {{ tenant.pengguna?.nama ? tenant.pengguna.nama.charAt(0).toUpperCase() : 'P' }}
            </span>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
              <h3 class="text-sm font-bold text-slate-900 truncate">
                {{ tenant.pengguna?.nama || 'Calon Penghuni' }}
              </h3>
              <!-- Status Badge -->
              <span
                :class="[
                  'px-2.5 py-0.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1 border',
                  getBadgeClass(tenant)
                ]"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="getBadgeDotClass(tenant)"></span>
                {{ tenant.status_label }}
              </span>
            </div>

            <!-- Property & Room -->
            <p class="text-xs font-semibold text-primary">
              {{ tenant.kos?.nama }} &bull; <span class="text-slate-700 font-bold">Kamar {{ tenant.kamar?.nama_kamar }}</span>
            </p>

            <!-- Dates & Duration -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-[11px] text-slate-500">
              <div class="flex items-center gap-1">
                <PhCalendar :size="13" class="text-slate-400" />
                <span>Masuk: <strong class="text-slate-700 font-semibold">{{ formatDate(tenant.tanggal_pemesanan) }}</strong></span>
              </div>
              <div class="flex items-center gap-1">
                <PhCalendarCheck :size="13" class="text-slate-400" />
                <span>Selesai: <strong class="text-slate-700 font-semibold">{{ formatDate(tenant.tanggal_selesai) }}</strong></span>
              </div>
              <div class="flex items-center gap-1">
                <PhClock :size="13" class="text-slate-400" />
                <span>Durasi: <strong class="text-slate-700 font-semibold capitalize">{{ tenant.durasi_jumlah }} {{ tenant.durasi_tipe }}</strong></span>
              </div>
            </div>

            <!-- Lease Countdown Warning -->
            <div v-if="tenant.status === 'aktif' && tenant.sisa_hari !== null" class="mt-2.5">
              <div
                v-if="tenant.sisa_hari < 0"
                class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200"
              >
                <PhWarningCircle :size="13" weight="fill" />
                <span>Masa sewa telah berakhir lewat {{ Math.abs(tenant.sisa_hari) }} hari</span>
              </div>
              <div
                v-else-if="tenant.sisa_hari <= 7"
                class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200"
              >
                <PhClock :size="13" weight="bold" />
                <span>Sisa waktu tinggal {{ tenant.sisa_hari }} hari lagi (Jatuh tempo segera)</span>
              </div>
              <div
                v-else
                class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200"
              >
                <PhCheckCircle :size="13" weight="bold" />
                <span>Sisa masa sewa: {{ tenant.sisa_hari }} hari</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 border-t lg:border-t-0 pt-3 lg:pt-0 shrink-0">
          <!-- Hubungi WA -->
          <button
            @click="sendWhatsAppReminder(tenant)"
            class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
            title="Kirim pesan WhatsApp atau pengingat sewa"
          >
            <PhWhatsappLogo :size="16" weight="fill" class="text-emerald-600" />
            <span>Hubungi WA</span>
          </button>

          <!-- Serah Kunci (if status siap_serah_terima) -->
          <button
            v-if="tenant.status === 'siap_serah_terima'"
            @click="handoverKey(tenant)"
            :disabled="actionLoading === tenant.id"
            class="px-3.5 py-2 rounded-xl bg-primary hover:bg-primary-container text-white text-xs font-semibold flex items-center gap-1.5 transition shadow-xs cursor-pointer disabled:opacity-50"
            title="Konfirmasi serah terima kunci kamar dan klaim saldo sewa"
          >
            <PhKey :size="16" weight="bold" />
            <span>{{ actionLoading === tenant.id ? 'Memproses...' : 'Serah Kunci & Klaim Saldo' }}</span>
          </button>

          <!-- Check-out / Selesai Sewa -->
          <button
            v-if="tenant.status === 'aktif' || tenant.status === 'siap_serah_terima' || tenant.status === 'dp_dibayar'"
            @click="openCheckoutModal(tenant)"
            :disabled="actionLoading === tenant.id"
            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 text-slate-700 border border-slate-200 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer disabled:opacity-50"
            title="Check-out penghuni dan jadikan kamar kembali berstatus tersedia"
          >
            <PhSignOut :size="16" weight="bold" />
            <span>Selesai / Check-out</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Check-Out -->
    <div
      v-if="showCheckoutModal && selectedTenant"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
    >
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
          <PhSignOut :size="24" weight="bold" />
        </div>
        <h3 class="text-base font-bold text-slate-900 mb-1">Konfirmasi Check-Out Penghuni</h3>
        <p class="text-xs text-slate-500 leading-relaxed mb-4">
          Apakah Anda yakin ingin menyelesaikan masa sewa untuk
          <strong class="text-slate-800">{{ selectedTenant.pengguna?.nama }}</strong> pada kamar
          <strong class="text-slate-800">{{ selectedTenant.kamar?.nama_kamar }}</strong> ({{ selectedTenant.kos?.nama }})?
          <br /><br />
          Tindakan ini akan mengubah status pemesanan menjadi <strong>Selesai</strong> dan kamar otomatis berstatus <strong>Tersedia</strong> untuk calon penyewa lain.
        </p>

        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
          <button
            @click="showCheckoutModal = false"
            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
          >
            Batal
          </button>
          <button
            @click="confirmCheckout"
            :disabled="actionLoading === selectedTenant.id"
            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-xs disabled:opacity-50"
          >
            <PhCheck :size="15" weight="bold" />
            <span>{{ actionLoading === selectedTenant.id ? 'Memproses...' : 'Ya, Check-Out Sekarang' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import { openWhatsAppChat } from '../utils/whatsapp'
import {
  PhUsers,
  PhBed,
  PhKey,
  PhHourglassLow,
  PhArrowClockwise,
  PhMagnifyingGlass,
  PhCalendar,
  PhCalendarCheck,
  PhClock,
  PhWhatsappLogo,
  PhSignOut,
  PhCheckCircle,
  PhWarningCircle,
  PhInfo,
  PhCheck,
} from '@phosphor-icons/vue'

const loading = ref(false)
const actionLoading = ref(null)
const tenants = ref([])
const summary = ref({
  total_penghuni: 0,
  total_siap_masuk: 0,
  total_jatuh_tempo: 0,
  kamar_terisi: 0,
})

const activeFilter = ref('all')
const searchQuery = ref('')
const toastMessage = ref('')
const toastType = ref('success')

const showCheckoutModal = ref(false)
const selectedTenant = ref(null)

const filterTabs = computed(() => [
  { id: 'all', label: 'Semua', count: tenants.value.length },
  { id: 'aktif', label: 'Aktif Menempati', count: summary.value.total_penghuni },
  { id: 'siap_masuk', label: 'Siap Masuk', count: summary.value.total_siap_masuk },
  { id: 'jatuh_tempo', label: 'Jatuh Tempo (H-7)', count: summary.value.total_jatuh_tempo },
])

const filteredTenants = computed(() => {
  return tenants.value.filter((tenant) => {
    // 1. Tab filter
    if (activeFilter.value === 'aktif' && tenant.status !== 'aktif') return false
    if (activeFilter.value === 'siap_masuk' && !['dp_dibayar', 'siap_serah_terima', 'disetujui'].includes(tenant.status)) return false
    if (activeFilter.value === 'jatuh_tempo') {
      if (tenant.status !== 'aktif' || tenant.sisa_hari === null || tenant.sisa_hari > 7) return false
    }

    // 2. Search query filter
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase()
      const nama = tenant.pengguna?.nama?.toLowerCase() || ''
      const email = tenant.pengguna?.email?.toLowerCase() || ''
      const telepon = tenant.pengguna?.telepon?.toLowerCase() || ''
      const kos = tenant.kos?.nama?.toLowerCase() || ''
      const kamar = tenant.kamar?.nama_kamar?.toLowerCase() || ''
      const match = nama.includes(q) || email.includes(q) || telepon.includes(q) || kos.includes(q) || kamar.includes(q)
      if (!match) return false
    }

    return true
  })
})

const fetchTenants = async () => {
  loading.value = true
  try {
    const res = await api.get('/pemilik/penghuni')
    tenants.value = res.data?.data || []
    if (res.data?.summary) {
      summary.value = res.data.summary
    }
  } catch (err) {
    showToast(err.response?.data?.message || 'Gagal memuat data penghuni kos.', 'error')
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  activeFilter.value = 'all'
  searchQuery.value = ''
}

const showToast = (msg, type = 'success') => {
  toastMessage.value = msg
  toastType.value = type
  setTimeout(() => {
    if (toastMessage.value === msg) {
      toastMessage.value = ''
    }
  }, 4000)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const getBadgeClass = (tenant) => {
  if (tenant.status === 'aktif') {
    if (tenant.sisa_hari !== null && tenant.sisa_hari < 0) {
      return 'bg-rose-50 text-rose-700 border-rose-200'
    }
    if (tenant.sisa_hari !== null && tenant.sisa_hari <= 7) {
      return 'bg-amber-50 text-amber-700 border-amber-200'
    }
    return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  }
  if (tenant.status === 'siap_serah_terima') {
    return 'bg-indigo-50 text-indigo-700 border-indigo-200'
  }
  if (tenant.status === 'dp_dibayar') {
    return 'bg-blue-50 text-blue-700 border-blue-200'
  }
  return 'bg-slate-100 text-slate-700 border-slate-200'
}

const getBadgeDotClass = (tenant) => {
  if (tenant.status === 'aktif') {
    if (tenant.sisa_hari !== null && tenant.sisa_hari < 0) return 'bg-rose-500'
    if (tenant.sisa_hari !== null && tenant.sisa_hari <= 7) return 'bg-amber-500'
    return 'bg-emerald-500'
  }
  if (tenant.status === 'siap_serah_terima') return 'bg-indigo-500'
  if (tenant.status === 'dp_dibayar') return 'bg-blue-500'
  return 'bg-slate-400'
}

const sendWhatsAppReminder = (tenant) => {
  const phone = tenant.pengguna?.telepon
  if (!phone) {
    showToast('Nomor telepon/WhatsApp penghuni tidak terdaftar.', 'error')
    return
  }

  let message = ''
  if (tenant.status === 'aktif') {
    if (tenant.sisa_hari !== null && tenant.sisa_hari <= 7) {
      message = `Halo Kak ${tenant.pengguna.nama}, salam dari pengelola ${tenant.kos?.nama}.\n\nMengingatkan masa sewa Anda untuk kamar ${tenant.kamar?.nama_kamar} akan berakhir pada ${formatDate(tenant.tanggal_selesai)} (${tenant.sisa_hari} hari lagi).\n\nApakah Kakak ingin memperpanjang masa sewa? Mohon konfirmasinya ya Kak. Terima kasih! 🙏`
    } else {
      message = `Halo Kak ${tenant.pengguna.nama}, salam dari pengelola ${tenant.kos?.nama}.\n\nBagaimana kenyamanan kamar ${tenant.kamar?.nama_kamar}? Jika ada kendala fasilitas atau pertanyaan, silakan beri tahu kami. Terima kasih!`
    }
  } else if (tenant.status === 'siap_serah_terima') {
    message = `Halo Kak ${tenant.pengguna.nama}, pembayaran sewa kos ${tenant.kos?.nama} kamar ${tenant.kamar?.nama_kamar} sudah lunas sepenuhnya.\n\nKunci kamar sudah siap diserahkan. Kapan rencana Kakak untuk datang ke lokasi kos? Terima kasih!`
  } else if (tenant.status === 'dp_dibayar') {
    message = `Halo Kak ${tenant.pengguna.nama}, DP untuk kamar ${tenant.kamar?.nama_kamar} di ${tenant.kos?.nama} sudah kami terima.\n\nUnit kamar telah resmi dikunci untuk Kakak. Mohon informasikan perkiraan kedatangan untuk proses pelunasan dan serah terima kunci ya. Terima kasih!`
  } else {
    message = `Halo Kak ${tenant.pengguna.nama}, salam dari pengelola ${tenant.kos?.nama} kamar ${tenant.kamar?.nama_kamar}. Ada yang bisa kami bantu?`
  }

  openWhatsAppChat(phone, message)
}

const handoverKey = async (tenant) => {
  actionLoading.value = tenant.id
  try {
    await api.post(`/bookings/${tenant.id}/handover-key`)
    showToast('Kunci kamar berhasil diserahkan! Saldo pendapatan sewa telah dikreditkan ke dompet Anda.', 'success')
    await fetchTenants()
  } catch (err) {
    showToast(err.response?.data?.message || 'Gagal menyerahkan kunci kamar.', 'error')
  } finally {
    actionLoading.value = null
  }
}

const openCheckoutModal = (tenant) => {
  selectedTenant.value = tenant
  showCheckoutModal.value = true
}

const confirmCheckout = async () => {
  if (!selectedTenant.value) return
  actionLoading.value = selectedTenant.value.id
  try {
    await api.post(`/pemilik/penghuni/${selectedTenant.value.id}/checkout`)
    showToast('Penghuni berhasil di-checkout dan kamar kembali berstatus tersedia.', 'success')
    showCheckoutModal.value = false
    selectedTenant.value = null
    await fetchTenants()
  } catch (err) {
    showToast(err.response?.data?.message || 'Gagal memproses check-out.', 'error')
  } finally {
    actionLoading.value = null
  }
}

onMounted(() => {
  fetchTenants()
})
</script>
