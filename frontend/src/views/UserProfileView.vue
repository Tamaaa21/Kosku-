<template>
  <div class="w-full max-w-7xl mx-auto px-margin py-space-xl pt-20">
    <!-- Header Page Meta -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-sm mb-8">
      <div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Profil Saya</h1>
        <p class="text-xs text-on-surface-variant mt-0.5">
          Kelola informasi akun pengguna, kontak, foto profil, dan keamanan akses sistem KosKu.
        </p>
      </div>

      <!-- Quick Status Badge -->
      <div class="inline-flex items-center gap-2 bg-surface-container-lowest border border-slate-100 px-4 py-2 rounded-full shadow-sm">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
        <span class="text-xs text-on-surface font-semibold capitalize">{{ auth.role }} Terverifikasi</span>
        <PhShieldCheck :size="18" weight="fill" class="text-primary" />
      </div>
    </div>

    <!-- Alert / Toast -->
    <div
      v-if="toastMsg"
      class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-medium border border-emerald-200 flex items-center justify-between shadow-xs transition-all"
    >
      <div class="flex items-center gap-2">
        <PhCheckCircle :size="18" weight="fill" class="text-emerald-600" />
        <span>{{ toastMsg }}</span>
      </div>
      <button @click="toastMsg = ''" class="text-emerald-700 font-bold ml-4 text-base hover:opacity-75">&times;</button>
    </div>

    <!-- 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
      <!-- Kolom Kiri: Sidebar Profil (~360px) -->
      <div class="lg:col-span-4 xl:col-span-4 flex flex-col gap-space-md">
        <!-- Profile Card -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-slate-100 flex flex-col items-center text-center relative overflow-hidden">
          <!-- Avatar dengan Fitur Unggah Foto Interaktif -->
          <div class="relative mt-2 mb-3 group">
            <div class="w-28 h-28 rounded-full overflow-hidden shadow-md ring-4 ring-white bg-surface-container flex items-center justify-center relative">
              <img
                v-if="avatarPreview || auth.user?.foto"
                :src="avatarPreview || auth.user.foto"
                :alt="auth.user?.nama"
                class="w-full h-full object-cover"
              />
              <PhUser v-else :size="54" class="text-primary select-none" />

              <!-- Overlay loader saat mengunggah foto -->
              <div
                v-if="uploadingAvatar"
                class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white"
              >
                <PhCircleNotch :size="24" class="animate-spin" />
                <span class="text-[10px] mt-1 font-semibold">Mengunggah...</span>
              </div>
            </div>

            <!-- Tombol Kamera untuk Mengganti Foto -->
            <button
              type="button"
              @click="triggerFileInput"
              :disabled="uploadingAvatar"
              class="absolute bottom-0 right-0 w-9 h-9 rounded-full bg-primary hover:bg-primary-container text-white shadow-md flex items-center justify-center transition border-2 border-white focus:outline-none focus:ring-2 focus:ring-primary/40 disabled:opacity-50"
              title="Ganti Foto Profil"
            >
              <PhCamera :size="18" weight="bold" />
            </button>
            <input
              ref="fileInputRef"
              type="file"
              accept="image/jpeg,image/png,image/webp,image/jpg"
              class="hidden"
              @change="onAvatarSelected"
            />
          </div>

          <p class="text-[11px] text-on-surface-variant mb-3">
            Klik ikon kamera untuk mengganti foto profil (Maks. 2MB)
          </p>

          <h2 class="text-lg font-bold text-on-surface">{{ auth.user?.nama }}</h2>
          <p class="text-xs text-on-surface-variant mt-0.5">{{ auth.user?.email }}</p>

          <div class="mt-2 flex items-center justify-center gap-1.5">
            <span
              :class="[
                'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                auth.isPemilik ? 'bg-primary-container text-white' :
                auth.isAdmin ? 'bg-slate-900 text-white' :
                'bg-surface-container-high text-primary'
              ]"
            >
              {{ auth.role }}
            </span>
          </div>

          <!-- Quick Summary Stats -->
          <div class="w-full grid grid-cols-2 gap-2 pt-space-md mt-space-md bg-surface-container-low/50 rounded-xl p-3 text-center border border-slate-100">
            <div>
              <span class="text-base font-bold text-primary block">
                {{ auth.isPemilik ? myKosCount + ' Unit' : myBookingCount + ' Pesanan' }}
              </span>
              <span class="text-[11px] text-on-surface-variant">
                {{ auth.isPemilik ? 'Properti Kos' : 'Total Riwayat' }}
              </span>
            </div>
            <div>
              <span class="text-base font-bold text-emerald-600 block">
                {{ auth.isPemilik ? myRoomCount + ' Kamar' : 'Aktif' }}
              </span>
              <span class="text-[11px] text-on-surface-variant">
                {{ auth.isPemilik ? 'Total Kapasitas' : 'Status Akun' }}
              </span>
            </div>
          </div>

          <!-- Quick Logout Action -->
          <div class="w-full pt-4 mt-4 border-t border-slate-100">
            <button
              @click="handleLogout"
              class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 transition text-xs font-semibold"
              type="button"
            >
              <PhSignOut :size="18" weight="bold" />
              <span>Keluar dari Akun</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Main Profile Form & Ganti Password (~800px) -->
      <main class="lg:col-span-8 xl:col-span-8 space-y-6">
        <!-- Personal Info Card -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-slate-100">
          <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
              <h3 class="text-lg font-bold text-on-surface">Informasi Akun Pribadi</h3>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Data profil Anda digunakan untuk identitas transaksi dan komunikasi hunian kos.
              </p>
            </div>
            <button
              @click="refreshData"
              class="px-3.5 py-1.5 bg-surface-container hover:bg-surface-container-high text-primary text-xs font-semibold rounded-lg transition flex items-center gap-1.5"
            >
              <PhArrowClockwise :size="16" weight="bold" />
              <span>Segarkan</span>
            </button>
          </div>

          <form @submit.prevent="saveProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nama -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Nama Lengkap</label>
                <div class="relative flex items-center">
                  <PhUser :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                  <input
                    v-model="profileForm.nama"
                    type="text"
                    required
                    class="w-full h-11 pl-9 pr-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                  />
                </div>
              </div>

              <!-- Email -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Alamat Email</label>
                <div class="relative flex items-center">
                  <PhEnvelope :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                  <input
                    :value="auth.user?.email"
                    type="email"
                    disabled
                    class="w-full h-11 pl-9 pr-3.5 bg-slate-100 rounded-lg text-xs text-slate-500 cursor-not-allowed"
                  />
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nomor Telepon -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Nomor WhatsApp / Telepon</label>
                <div class="relative flex items-center">
                  <PhPhone :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                  <input
                    v-model="profileForm.no_hp"
                    type="tel"
                    placeholder="Contoh: 081234567890"
                    class="w-full h-11 pl-9 pr-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                  />
                </div>
              </div>

              <!-- Peran Akun -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Peran / Hak Akses Sistem</label>
                <div class="relative flex items-center">
                  <PhIdentificationBadge :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                  <input
                    :value="auth.role?.toUpperCase()"
                    type="text"
                    disabled
                    class="w-full h-11 pl-9 pr-3.5 bg-slate-100 rounded-lg text-xs font-bold text-primary tracking-wide cursor-not-allowed"
                  />
                </div>
              </div>
            </div>

            <!-- Simpan Perubahan Button -->
            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
              <span class="text-[11px] text-on-surface-variant flex items-center gap-1">
                <PhLock :size="15" class="text-emerald-600" />
                Data tersinkronisasi dengan REST API KosKu
              </span>
              <button
                type="submit"
                :disabled="saving"
                class="h-10 px-6 rounded-lg bg-primary-container text-white text-xs font-semibold hover:bg-primary transition shadow-sm disabled:opacity-50 flex items-center gap-2"
              >
                <PhCircleNotch v-if="saving" :size="16" class="animate-spin" />
                <span>{{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Ganti Kata Sandi Card -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-slate-100">
          <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
            <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
              <PhLockKey :size="20" weight="bold" />
            </div>
            <div>
              <h3 class="text-base font-bold text-on-surface">Ganti Kata Sandi</h3>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Amankan akun Anda dengan memperbarui kata sandi secara berkala.
              </p>
            </div>
          </div>

          <!-- Alert Kata Sandi Sukses -->
          <div
            v-if="passwordSuccess"
            class="mb-4 p-3.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-medium border border-emerald-200 flex items-center justify-between"
          >
            <div class="flex items-center gap-2">
              <PhCheckCircle :size="18" weight="fill" class="text-emerald-600" />
              <span>{{ passwordSuccess }}</span>
            </div>
            <button @click="passwordSuccess = ''" class="text-emerald-700 font-bold ml-4 text-base">&times;</button>
          </div>

          <!-- Alert Kata Sandi Error -->
          <div
            v-if="passwordError"
            class="mb-4 p-3.5 rounded-xl bg-rose-50 text-rose-800 text-xs font-medium border border-rose-200 flex items-center justify-between"
          >
            <div class="flex items-center gap-2">
              <PhWarningCircle :size="18" weight="fill" class="text-rose-600" />
              <span>{{ passwordError }}</span>
            </div>
            <button @click="passwordError = ''" class="text-rose-700 font-bold ml-4 text-base">&times;</button>
          </div>

          <form @submit.prevent="savePassword" class="space-y-4">
            <!-- Password Lama -->
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-on-surface">Kata Sandi Saat Ini</label>
              <div class="relative flex items-center">
                <PhKey :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                <input
                  v-model="passwordForm.current_password"
                  :type="showCurrentPassword ? 'text' : 'password'"
                  placeholder="Masukkan kata sandi lama Anda"
                  required
                  class="w-full h-11 pl-9 pr-10 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
                <button
                  type="button"
                  @click="showCurrentPassword = !showCurrentPassword"
                  class="absolute right-3 text-slate-400 hover:text-on-surface flex items-center"
                >
                  <PhEyeSlash v-if="showCurrentPassword" :size="18" />
                  <PhEye v-else :size="18" />
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Password Baru -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Kata Sandi Baru</label>
                <div class="relative flex items-center">
                  <PhLock :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                  <input
                    v-model="passwordForm.password"
                    :type="showNewPassword ? 'text' : 'password'"
                    placeholder="Minimal 8 karakter"
                    required
                    minlength="8"
                    class="w-full h-11 pl-9 pr-10 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                  />
                  <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute right-3 text-slate-400 hover:text-on-surface flex items-center"
                  >
                    <PhEyeSlash v-if="showNewPassword" :size="18" />
                    <PhEye v-else :size="18" />
                  </button>
                </div>
                <span class="text-[10px] text-on-surface-variant">Kombinasikan huruf dan angka untuk keamanan optimal</span>
              </div>

              <!-- Konfirmasi Password Baru -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Ulangi Kata Sandi Baru</label>
                <div class="relative flex items-center">
                  <PhLockKey :size="18" class="absolute left-3 text-slate-400 pointer-events-none" />
                  <input
                    v-model="passwordForm.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    placeholder="Ulangi kata sandi baru"
                    required
                    minlength="8"
                    class="w-full h-11 pl-9 pr-10 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                  />
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3 text-slate-400 hover:text-on-surface flex items-center"
                  >
                    <PhEyeSlash v-if="showConfirmPassword" :size="18" />
                    <PhEye v-else :size="18" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Update Password Submit Button -->
            <div class="pt-4 flex items-center justify-end border-t border-slate-100">
              <button
                type="submit"
                :disabled="passwordLoading"
                class="h-10 px-6 rounded-lg bg-primary hover:bg-primary-container text-white text-xs font-semibold transition shadow-sm disabled:opacity-50 flex items-center gap-2"
              >
                <PhCircleNotch v-if="passwordLoading" :size="16" class="animate-spin" />
                <span>{{ passwordLoading ? 'Menyimpan Sandi...' : 'Perbarui Kata Sandi' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Security & Status Card -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-slate-100">
          <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-4">
            <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
              <PhShieldCheck :size="20" weight="bold" />
            </div>
            <div>
              <h4 class="text-sm font-bold text-on-surface">Keamanan & Sesi Akun</h4>
              <p class="text-[11px] text-on-surface-variant">Status otentikasi sesi token aktif (Laravel Sanctum).</p>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-surface-container-low/50 border border-slate-100 text-xs">
            <div class="flex items-center gap-3">
              <PhShieldCheck :size="24" weight="fill" class="text-emerald-600" />
              <div>
                <p class="font-bold text-on-surface">Akun Terlindungi</p>
                <p class="text-[11px] text-slate-500">Token otentikasi valid dan tersimpan aman di peramban Anda.</p>
              </div>
            </div>
            <button
              @click="handleLogout"
              class="px-4 py-2 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 transition font-semibold text-xs border border-rose-200"
            >
              Keluar dari Sesi
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUiStore } from '../stores/ui'
import {
  PhShieldCheck,
  PhCheckCircle,
  PhUser,
  PhCircleNotch,
  PhCamera,
  PhSignOut,
  PhArrowClockwise,
  PhEnvelope,
  PhPhone,
  PhIdentificationBadge,
  PhLock,
  PhLockKey,
  PhWarningCircle,
  PhKey,
  PhEyeSlash,
  PhEye,
} from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const auth = useAuthStore()
const router = useRouter()

const toastMsg = ref('')
const myKosCount = ref(0)
const myRoomCount = ref(0)
const myBookingCount = ref(0)

const profileForm = reactive({
  nama: auth.user?.nama || '',
  no_hp: auth.user?.no_hp || auth.user?.telepon || '',
})

// Avatar upload state
const fileInputRef = ref(null)
const uploadingAvatar = ref(false)
const avatarPreview = ref(null)

// Password form state
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const passwordLoading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const loadStats = async () => {
  if (auth.isPemilik) {
    try {
      const res = await api.get('/kos', { params: { pemilik_id: auth.user?.id } })
      const list = res.data?.data || []
      myKosCount.value = list.length
      myRoomCount.value = list.reduce((acc, k) => acc + (k.kamar?.length || 0), 0)
    } catch (e) {
      // ignore
    }
  } else {
    try {
      const res = await api.get('/bookings')
      myBookingCount.value = (res.data?.data || []).length
    } catch (e) {
      // ignore
    }
  }
}

onMounted(() => {
  if (auth.user) {
    profileForm.nama = auth.user.nama || ''
    profileForm.no_hp = auth.user.no_hp || auth.user.telepon || ''
  }
  loadStats()
})

const triggerFileInput = () => {
  fileInputRef.value?.click()
}

const onAvatarSelected = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']
  if (!validTypes.includes(file.type)) {
    toastMsg.value = 'Format foto harus berupa JPG, PNG, atau WEBP.'
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    toastMsg.value = 'Ukuran berkas foto profil maksimal 2MB.'
    return
  }

  avatarPreview.value = URL.createObjectURL(file)
  uploadingAvatar.value = true

  try {
    await auth.updateAvatar(file)
    toastMsg.value = 'Foto profil berhasil diperbarui!'
  } catch (err) {
    avatarPreview.value = null
    toastMsg.value = err.response?.data?.message || 'Gagal mengunggah foto profil.'
  } finally {
    uploadingAvatar.value = false
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

const refreshData = async () => {
  await auth.fetchMe()
  if (auth.user) {
    profileForm.nama = auth.user.nama || ''
    profileForm.no_hp = auth.user.no_hp || auth.user.telepon || ''
  }
  await loadStats()
  toastMsg.value = 'Data akun berhasil disinkronkan!'
}

const saving = ref(false)

const saveProfile = async () => {
  saving.value = true
  try {
    await auth.updateProfile({
      nama: profileForm.nama,
      no_hp: profileForm.no_hp,
      telepon: profileForm.no_hp,
    })
    toastMsg.value = 'Informasi profil dan nomor WhatsApp berhasil disimpan ke database!'
  } catch (err) {
    toastMsg.value = err.response?.data?.message || 'Gagal menyimpan profil, periksa kembali data Anda.'
  } finally {
    saving.value = false
  }
}

const savePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (!passwordForm.current_password) {
    passwordError.value = 'Kata sandi saat ini wajib diisi.'
    return
  }
  if (!passwordForm.password) {
    passwordError.value = 'Kata sandi baru wajib diisi.'
    return
  }
  if (passwordForm.password.length < 8) {
    passwordError.value = 'Kata sandi baru minimal 8 karakter.'
    return
  }
  if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordError.value = 'Konfirmasi kata sandi baru tidak cocok.'
    return
  }

  passwordLoading.value = true
  try {
    const res = await auth.updatePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation,
    })
    passwordSuccess.value = res.message || 'Kata sandi berhasil diperbarui.'
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Gagal memperbarui kata sandi. Periksa kembali kata sandi Anda.'
  } finally {
    passwordLoading.value = false
  }
}

const ui = useUiStore()

const handleLogout = async () => {
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar Akun',
    message: 'Apakah Anda yakin ingin keluar dari akun KosKu Anda?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  await auth.logout()
  ui.success('Anda telah berhasil keluar.')
  router.push({ name: 'home' })
}
</script>
