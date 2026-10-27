<template>
  <div class="min-h-screen bg-[#F8FAFC] pb-16 pt-20 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
      <router-link to="/dashboard/pemilik" class="hover:text-primary transition flex items-center gap-1">
        <PhSquaresFour :size="15" weight="bold" />
        <span>Dashboard</span>
      </router-link>
      <PhCaretRight :size="12" weight="bold" class="text-slate-400" />
      <span class="text-slate-900 font-bold">Verifikasi Kepemilikan Properti</span>
    </div>

    <!-- Alert jika dialihkan karena mencoba akses fitur terkunci -->
    <div
      v-if="$route.query.locked"
      class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-3 shadow-xs"
    >
      <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
        <PhLockKey :size="20" weight="bold" />
      </div>
      <div>
        <h3 class="text-sm font-bold text-amber-950">Akses Fitur Masih Terkunci</h3>
        <p class="text-xs text-amber-800/90 mt-0.5 leading-relaxed">
          Fitur pengelolaan properti kos, kelola kamar, dan konfirmasi pemesanan sewa baru hanya terbuka bagi mitra pemilik kos yang telah menyelesaikan verifikasi kepemilikan.
        </p>
      </div>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
          <span>Verifikasi Kepemilikan Properti</span>
          <PhShieldCheck :size="24" weight="fill" class="text-blue-600" />
        </h1>
        <p class="text-xs text-slate-500 mt-1">
          Langkah resmi untuk memastikan kepemilikan sah kos dan membuka seluruh akses fitur kelola kos di Kostara.
        </p>
      </div>

      <!-- Status Badge Header -->
      <div>
        <span
          :class="[
            'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold capitalize shadow-xs',
            currentStatus === 'verified' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
            currentStatus === 'pending' ? 'bg-blue-100 text-blue-800 border border-blue-200' :
            currentStatus === 'rejected' ? 'bg-rose-100 text-rose-800 border border-rose-200' :
            'bg-amber-100 text-amber-800 border border-amber-200'
          ]"
        >
          <span
            :class="[
              'w-2 h-2 rounded-full',
              currentStatus === 'verified' ? 'bg-emerald-500' :
              currentStatus === 'pending' ? 'bg-blue-500 animate-pulse' :
              currentStatus === 'rejected' ? 'bg-rose-500' : 'bg-amber-500'
            ]"
          ></span>
          <span>Status: {{ statusLabel }}</span>
        </span>
      </div>
    </div>

    <!-- STATUS 1: SUDAH TERVERIFIKASI -->
    <div
      v-if="currentStatus === 'verified'"
      class="bg-white rounded-3xl p-6 sm:p-8 border border-emerald-100 shadow-xs relative overflow-hidden text-center sm:text-left flex flex-col sm:flex-row items-center gap-6"
    >
      <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-sm border border-emerald-100">
        <PhShieldCheck :size="44" weight="duotone" />
      </div>
      <div class="flex-1 min-w-0">
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 inline-block mb-1">
          Lencana Terverifikasi Aktif
        </span>
        <h2 class="text-lg font-bold text-slate-900">Selamat! Akun Pemilik Kos Anda Telah Terverifikasi</h2>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Seluruh properti kos Anda mendapatkan lencana kepercayaan <strong>"Terverifikasi 🛡️"</strong>. Anda dapat mengelola kamar, menambah listing baru, dan menyetujui calon penyewa tanpa batasan.
        </p>
        <div class="mt-4 flex flex-wrap items-center gap-3">
          <router-link
            to="/dashboard/pemilik/kos"
            class="px-5 py-2.5 rounded-xl bg-primary hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5"
          >
            <span>Kelola Properti Kos</span>
            <PhArrowRight :size="14" weight="bold" />
          </router-link>
          <router-link
            to="/dashboard/pemilik"
            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition"
          >
            Kembali ke Dashboard
          </router-link>
        </div>
      </div>
    </div>

    <!-- STATUS 2: SEDANG DITINJAU (PENDING) -->
    <div
      v-else-if="currentStatus === 'pending'"
      class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-100 shadow-xs relative overflow-hidden"
    >
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
          <PhClock :size="26" weight="duotone" />
        </div>
        <div>
          <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 inline-block mb-1">
            Dalam Peninjauan Admin
          </span>
          <h2 class="text-base font-bold text-slate-900">Dokumen Verifikasi Anda Sedang Ditinjau Tim Kostara</h2>
          <p class="text-xs text-slate-500 mt-1 leading-relaxed">
            Pengajuan verifikasi kepemilikan Anda telah kami terima pada {{ formattedUpdatedDate }}. Tim admin kami sedang memvalidasi kesesuaian dokumen identitas dan properti (estimasi maksimal 1x24 jam).
          </p>
        </div>
      </div>

      <!-- Preview Dokumen yang Dikirim -->
      <div class="mt-6 pt-6 border-t border-slate-100">
        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Dokumen yang Diajukan:</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- KTP -->
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[11px] font-bold text-slate-600 block mb-1">Foto e-KTP</span>
            <p class="text-[11px] text-slate-400 font-mono mb-2">NIK: {{ formData.nik || '-' }}</p>
            <div class="aspect-video rounded-xl overflow-hidden bg-slate-200">
              <img :src="formData.foto_ktp" alt="KTP Preview" class="w-full h-full object-cover" />
            </div>
          </div>

          <!-- PBB / Listrik -->
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[11px] font-bold text-slate-600 block mb-1">Bukti PBB / Rekening Listrik</span>
            <p class="text-[11px] text-slate-400 mb-2 truncate">Bukti legalitas bangunan</p>
            <div class="aspect-video rounded-xl overflow-hidden bg-slate-200">
              <img :src="formData.bukti_properti" alt="PBB/Listrik Preview" class="w-full h-full object-cover" />
            </div>
          </div>

          <!-- Foto Properti -->
          <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
            <span class="text-[11px] font-bold text-slate-600 block mb-1">Foto di Lokasi Kos</span>
            <p class="text-[11px] text-slate-400 mb-2 truncate">Tampak depan gedung kos</p>
            <div class="aspect-video rounded-xl overflow-hidden bg-slate-200">
              <img :src="formData.foto_properti" alt="Foto Properti Preview" class="w-full h-full object-cover" />
            </div>
          </div>
        </div>

        <div v-if="formData.catatan_verifikasi" class="mt-4 p-3 bg-slate-50 rounded-xl text-xs text-slate-600 border border-slate-200">
          <strong class="font-bold text-slate-800">Catatan Pemilik:</strong> {{ formData.catatan_verifikasi }}
        </div>
      </div>
    </div>

    <!-- STATUS 3 & 4: FORM PENGAJUAN (Belum Mengajukan atau Ditolak) -->
    <div v-else class="space-y-6">
      <!-- Penolakan Alert jika status rejected -->
      <div
        v-if="currentStatus === 'rejected'"
        class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3"
      >
        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
          <PhWarningCircle :size="20" weight="bold" />
        </div>
        <div>
          <h3 class="text-sm font-bold text-rose-950">Pengajuan Verifikasi Sebelumnya Ditolak</h3>
          <p class="text-xs text-rose-800/90 mt-1 leading-relaxed">
            <strong>Catatan Evaluasi Admin:</strong> {{ auth.user?.alasan_penolakan || 'Dokumen belum memenuhi kualifikasi.' }}
          </p>
          <p class="text-xs text-rose-700 mt-1 font-medium">
            Silakan perbaiki foto atau informasi dokumen di bawah ini, lalu ajukan verifikasi ulang.
          </p>
        </div>
      </div>

      <!-- Form Container -->
      <form @submit.prevent="handleSubmit" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-xs space-y-6">
        <div>
          <h3 class="text-base font-bold text-slate-900">Formulir Dokumen Verifikasi</h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Lengkapi 3 dokumen utama di bawah ini dengan foto jelas dan tidak buram.
          </p>
        </div>

        <!-- Bagian 1: Identitas Pemilik -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
          <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-primary flex items-center justify-center text-[10px]">1</span>
            <span>Identitas Kependudukan (KTP)</span>
          </div>

          <!-- NIK -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="formData.nik"
              type="text"
              maxlength="16"
              placeholder="Contoh: 3328012345678901 (16 digit angka)"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
            />
            <p class="text-[11px] text-slate-400 mt-1">NIK sesuai pada kartu tanda penduduk resmi Anda.</p>
          </div>

          <!-- Upload Foto KTP -->
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Foto e-KTP Asli <span class="text-rose-500">*</span>
            </label>
            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
              <div v-if="formData.foto_ktp" class="w-32 h-20 rounded-xl overflow-hidden bg-slate-200 border border-slate-300 shrink-0">
                <img :src="formData.foto_ktp" alt="KTP" class="w-full h-full object-cover" />
              </div>
              <div class="flex-1 text-center sm:text-left">
                <p class="text-xs font-bold text-slate-800">
                  {{ formData.foto_ktp ? 'Foto KTP Siap Dikirim' : 'Pilih atau Ambil Foto KTP' }}
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Pastikan tulisan NIK dan nama terbaca jelas, format JPG/PNG max 3MB.</p>
                <label class="mt-2.5 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:border-slate-400 text-slate-700 text-xs font-bold shadow-xs cursor-pointer transition">
                  <PhUploadSimple :size="14" weight="bold" />
                  <span>{{ formData.foto_ktp ? 'Ganti Foto KTP' : 'Unggah Foto KTP' }}</span>
                  <input type="file" accept="image/*" class="hidden" @change="(e) => handleFileUpload(e, 'foto_ktp')" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Bagian 2: Bukti Tagihan Properti (PBB / Rekening Listrik PLN) -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
          <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-primary flex items-center justify-center text-[10px]">2</span>
            <span>Bukti Tagihan Properti (PBB / Rekening Listrik)</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Foto Lembar PBB Terbaru atau Tagihan Listrik PLN <span class="text-rose-500">*</span>
            </label>
            <p class="text-[11px] text-slate-500 mb-2">
              Dapat berupa lembar Pajak Bumi & Bangunan (PBB) tahun berjalan ATAU struk pembayaran tagihan listrik PLN/PDAM dengan alamat kos yang sama.
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
              <div v-if="formData.bukti_properti" class="w-32 h-20 rounded-xl overflow-hidden bg-slate-200 border border-slate-300 shrink-0">
                <img :src="formData.bukti_properti" alt="Bukti Properti" class="w-full h-full object-cover" />
              </div>
              <div class="flex-1 text-center sm:text-left">
                <p class="text-xs font-bold text-slate-800">
                  {{ formData.bukti_properti ? 'Bukti Tagihan Siap Dikirim' : 'Pilih Foto PBB / Rekening Listrik' }}
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Pastikan alamat properti tertera dengan jelas, format JPG/PNG max 3MB.</p>
                <label class="mt-2.5 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:border-slate-400 text-slate-700 text-xs font-bold shadow-xs cursor-pointer transition">
                  <PhUploadSimple :size="14" weight="bold" />
                  <span>{{ formData.bukti_properti ? 'Ganti Dokumen' : 'Unggah Dokumen' }}</span>
                  <input type="file" accept="image/*" class="hidden" @change="(e) => handleFileUpload(e, 'bukti_properti')" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Bagian 3: Foto di Lokasi / Tampak Depan Properti Kos -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
          <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
            <span class="w-5 h-5 rounded-full bg-blue-100 text-primary flex items-center justify-center text-[10px]">3</span>
            <span>Foto di Lokasi Properti Kos</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Foto Tampak Depan Properti Kos <span class="text-rose-500">*</span>
            </label>
            <p class="text-[11px] text-slate-500 mb-2">
              Foto Anda atau tampak depan bangunan kos dengan plang nama kos atau nomor rumah yang jelas.
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
              <div v-if="formData.foto_properti" class="w-32 h-20 rounded-xl overflow-hidden bg-slate-200 border border-slate-300 shrink-0">
                <img :src="formData.foto_properti" alt="Foto Properti" class="w-full h-full object-cover" />
              </div>
              <div class="flex-1 text-center sm:text-left">
                <p class="text-xs font-bold text-slate-800">
                  {{ formData.foto_properti ? 'Foto Lokasi Kos Siap Dikirim' : 'Pilih Foto Depan Kos' }}
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Membuktikan akses fisik langsung Anda ke lokasi kos.</p>
                <label class="mt-2.5 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:border-slate-400 text-slate-700 text-xs font-bold shadow-xs cursor-pointer transition">
                  <PhUploadSimple :size="14" weight="bold" />
                  <span>{{ formData.foto_properti ? 'Ganti Foto Kos' : 'Unggah Foto Kos' }}</span>
                  <input type="file" accept="image/*" class="hidden" @change="(e) => handleFileUpload(e, 'foto_properti')" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Bagian 4: Catatan Tambahan (Opsional) -->
        <div class="pt-4 border-t border-slate-100">
          <label class="block text-xs font-bold text-slate-700 mb-1">
            Catatan Tambahan (Opsional)
          </label>
          <textarea
            v-model="formData.catatan_verifikasi"
            rows="2"
            placeholder="Contoh: PBB atas nama orang tua / suami; nomor rumah tertera di pintu depan."
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
          ></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
          <router-link
            to="/dashboard/pemilik"
            class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 transition"
          >
            Batal
          </router-link>

          <button
            type="submit"
            :disabled="submitting || uploading"
            class="px-6 py-2.5 rounded-xl bg-primary hover:bg-blue-700 text-white font-bold text-xs shadow-md transition disabled:opacity-50 flex items-center gap-2 cursor-pointer"
          >
            <PhSpinner v-if="submitting || uploading" :size="16" class="animate-spin" />
            <span>{{ submitting ? 'Mengirim Dokumen...' : 'Kirim Dokumen Verifikasi' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  PhShieldCheck,
  PhSquaresFour,
  PhCaretRight,
  PhLockKey,
  PhArrowRight,
  PhClock,
  PhWarningCircle,
  PhUploadSimple,
  PhSpinner
} from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import api from '../services/api'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()

const loading = ref(false)
const submitting = ref(false)
const uploading = ref(false)

const currentStatus = computed(() => auth.user?.status_verifikasi || 'unverified')

const statusLabel = computed(() => {
  switch (currentStatus.value) {
    case 'verified': return 'Terverifikasi'
    case 'pending': return 'Menunggu Tinjauan'
    case 'rejected': return 'Perlu Perbaikan'
    default: return 'Belum Verifikasi'
  }
})

const formattedUpdatedDate = computed(() => {
  if (!auth.user?.updated_at) return 'hari ini'
  return new Date(auth.user.updated_at).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
})

const formData = ref({
  nik: '',
  foto_ktp: '',
  bukti_properti: '',
  foto_properti: '',
  catatan_verifikasi: '',
})

// Upload single document helper
const handleFileUpload = async (event, fieldName) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 3 * 1024 * 1024) {
    ui.error('Ukuran berkas maksimal 3MB.')
    return
  }

  const uploadData = new FormData()
  uploadData.append('file', file)

  uploading.value = true
  try {
    const res = await api.post('/pemilik/verification/upload', uploadData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    formData.value[fieldName] = res.data.url
    ui.success('Berkas berhasil diunggah.')
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal mengunggah berkas.')
  } finally {
    uploading.value = false
  }
}

// Fetch current verification status from API
const fetchVerificationStatus = async () => {
  loading.value = true
  try {
    const res = await api.get('/pemilik/verification')
    const data = res.data.data
    formData.value.nik = data.nik || ''
    formData.value.foto_ktp = data.foto_ktp || ''
    formData.value.bukti_properti = data.bukti_properti || ''
    formData.value.foto_properti = data.foto_properti || ''
    formData.value.catatan_verifikasi = data.catatan_verifikasi || ''

    if (auth.user) {
      auth.user.status_verifikasi = data.status_verifikasi
      auth.user.alasan_penolakan = data.alasan_penolakan
    }
  } catch (err) {
    console.error('Gagal mengambil status verifikasi:', err)
  } finally {
    loading.value = false
  }
}

// Submit Verification Form
const handleSubmit = async () => {
  if (!formData.value.nik || formData.value.nik.length < 16) {
    ui.error('Harap masukkan NIK 16 digit yang valid.')
    return
  }
  if (!formData.value.foto_ktp) {
    ui.error('Harap unggah foto e-KTP.')
    return
  }
  if (!formData.value.bukti_properti) {
    ui.error('Harap unggah bukti tagihan PBB atau rekening listrik.')
    return
  }
  if (!formData.value.foto_properti) {
    ui.error('Harap unggah foto tampak depan / di lokasi kos.')
    return
  }

  submitting.value = true
  try {
    const res = await api.post('/pemilik/verification', formData.value)
    ui.success(res.data.message || 'Pengajuan verifikasi berhasil dikirim.')
    if (res.data.user) {
      auth.user = res.data.user
      localStorage.setItem('user', JSON.stringify(res.data.user))
    }
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal mengirim formulir verifikasi.')
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchVerificationStatus()
})
</script>
