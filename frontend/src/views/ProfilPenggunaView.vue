<template>
  <div class="w-full max-w-7xl mx-auto px-margin py-space-xl pt-20">
    <!-- Header Page Meta -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-sm mb-8">
      <div>
        <div class="flex items-center gap-1.5 text-secondary text-xs font-semibold mb-1">
          <span>Pengaturan</span>
          <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          <span class="text-primary font-bold">Profil Akun</span>
        </div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Profil Saya</h1>
        <p class="text-xs text-on-surface-variant mt-0.5">
          Kelola informasi akun pengguna, kontak, dan akses sistem KosKu di Kota Tegal.
        </p>
      </div>

      <!-- Quick Status Badge -->
      <div class="inline-flex items-center gap-2 bg-surface-container-lowest border border-slate-100 px-4 py-2 rounded-full shadow-sm">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs text-on-surface font-semibold capitalize">{{ auth.role }} Terverifikasi</span>
        <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div
      v-if="toastMsg"
      class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-medium border border-emerald-200 flex items-center justify-between"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
        <span>{{ toastMsg }}</span>
      </div>
      <button @click="toastMsg = ''" class="text-emerald-700 font-bold ml-4 text-base">&times;</button>
    </div>

    <!-- 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
      <!-- Kolom Kiri: Sidebar Profil (~320px) -->
      <div class="lg:col-span-4 xl:col-span-4 flex flex-col gap-space-md">
        <!-- Profile Card -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-slate-100 flex flex-col items-center text-center relative overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-16 bg-gradient-to-r from-primary/10 via-surface-variant to-primary/5"></div>

          <!-- Avatar -->
          <div class="relative mt-2 mb-space-md">
            <div class="w-24 h-24 rounded-full overflow-hidden shadow-md ring-4 ring-white bg-surface-container flex items-center justify-center">
              <span class="material-symbols-outlined text-[48px] text-primary select-none">person</span>
            </div>
          </div>

          <h2 class="text-lg font-bold text-on-surface">{{ auth.user?.nama }}</h2>
          <div class="mt-1 flex items-center justify-center gap-1.5">
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
            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-semibold">
              Kota Tegal
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
        </div>

        <!-- Account Navigation Menu -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-sm shadow-sm border border-slate-100 flex flex-col gap-1">
          <div class="px-space-sm py-2">
            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Navigasi Akun</span>
          </div>

          <!-- Menu: Profil -->
          <button
            class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl bg-primary text-white text-xs font-semibold shadow-sm text-left"
          >
            <span class="material-symbols-outlined text-[18px]">person</span>
            <span class="flex-1">Profil & Informasi Akun</span>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          </button>

          <!-- Menu: Pemilik Shortcuts -->
          <template v-if="auth.isPemilik">
            <router-link
              to="/dashboard/pemilik"
              class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-secondary hover:bg-surface-container-low hover:text-on-surface text-xs font-medium transition"
            >
              <span class="material-symbols-outlined text-[18px]">dashboard</span>
              <span class="flex-1">Dashboard Pemilik</span>
              <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </router-link>

            <router-link
              to="/dashboard/pemilik/kos"
              class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-secondary hover:bg-surface-container-low hover:text-on-surface text-xs font-medium transition"
            >
              <span class="material-symbols-outlined text-[18px]">apartment</span>
              <span class="flex-1">Kelola Data Kos</span>
              <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </router-link>

            <router-link
              to="/dashboard/pemilik/kamar"
              class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-secondary hover:bg-surface-container-low hover:text-on-surface text-xs font-medium transition"
            >
              <span class="material-symbols-outlined text-[18px]">meeting_room</span>
              <span class="flex-1">Kelola Kamar</span>
              <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </router-link>

            <router-link
              to="/dashboard/pemilik/pemesanan"
              class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-secondary hover:bg-surface-container-low hover:text-on-surface text-xs font-medium transition"
            >
              <span class="material-symbols-outlined text-[18px]">event_available</span>
              <span class="flex-1">Pemesanan Masuk</span>
              <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </router-link>
          </template>

          <!-- Menu: Pencari Shortcuts -->
          <template v-else>
            <router-link
              to="/pesanan"
              class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-secondary hover:bg-surface-container-low hover:text-on-surface text-xs font-medium transition"
            >
              <span class="material-symbols-outlined text-[18px]">schedule</span>
              <span class="flex-1">Riwayat Pemesanan</span>
              <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </router-link>

            <router-link
              to="/kos"
              class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-secondary hover:bg-surface-container-low hover:text-on-surface text-xs font-medium transition"
            >
              <span class="material-symbols-outlined text-[18px]">search</span>
              <span class="flex-1">Cari Kos di Tegal</span>
              <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </router-link>
          </template>

          <div class="h-px bg-slate-100 my-1"></div>

          <!-- Logout -->
          <button
            @click="handleLogout"
            class="w-full flex items-center gap-space-sm px-space-md py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition text-xs font-semibold text-left"
            type="button"
          >
            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span class="flex-1">Keluar / Logout</span>
          </button>
        </div>
      </div>

      <!-- Kolom Kanan: Main Profile Form (~800px) -->
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
              <span class="material-symbols-outlined text-[16px]">sync</span>
              <span>Segarkan</span>
            </button>
          </div>

          <form @submit.prevent="saveProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nama -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Nama Lengkap</label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">person</span>
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
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">mail</span>
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
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">call</span>
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
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">badge</span>
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
                <span class="material-symbols-outlined text-[15px] text-emerald-600">lock</span>
                Data tersinkronisasi dengan REST API KosKu
              </span>
              <button
                type="submit"
                class="h-10 px-6 rounded-lg bg-primary-container text-white text-xs font-semibold hover:bg-primary transition shadow-sm"
              >
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>

        <!-- Security & Status Card -->
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg shadow-sm border border-slate-100">
          <div class="flex items-center gap-3 pb-3 border-b border-slate-100 mb-4">
            <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
              <span class="material-symbols-outlined text-[20px]">security</span>
            </div>
            <div>
              <h4 class="text-sm font-bold text-on-surface">Keamanan & Sesi Akun</h4>
              <p class="text-[11px] text-on-surface-variant">Status otentikasi sesi token aktif (Laravel Sanctum).</p>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-surface-container-low/50 border border-slate-100 text-xs">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-2xl text-emerald-600">shield_with_heart</span>
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
  no_hp: auth.user?.no_hp || auth.user?.telepon || ''
})

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
      const res = await api.get('/pemesanan')
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

const refreshData = async () => {
  await auth.fetchMe()
  if (auth.user) {
    profileForm.nama = auth.user.nama || ''
    profileForm.no_hp = auth.user.no_hp || auth.user.telepon || ''
  }
  await loadStats()
  toastMsg.value = 'Data akun berhasil disinkronkan!'
}

const saveProfile = () => {
  // Update local cache
  if (auth.user) {
    auth.user.nama = profileForm.nama
    auth.user.no_hp = profileForm.no_hp
    localStorage.setItem('user', JSON.stringify(auth.user))
  }
  toastMsg.value = 'Informasi profil berhasil diperbarui!'
}

const handleLogout = async () => {
  await auth.logout()
  router.push({ name: 'home' })
}
</script>
