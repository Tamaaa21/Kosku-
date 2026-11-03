<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div
      v-if="showWarningModal"
      class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm select-none"
    >
      <div
        class="bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 max-w-md w-full text-center relative overflow-hidden animate-scale-up"
      >
        <!-- Top Visual Indicator Bar -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#017E91] via-[#E68202] to-[#017E91]"></div>

        <!-- Warning Icon & Pulse -->
        <div class="relative w-16 h-16 rounded-2xl bg-amber-50 text-[#E68202] border border-amber-200/80 flex items-center justify-center mx-auto mb-4">
          <PhHourglassHigh :size="32" weight="bold" class="animate-pulse" />
          <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-red-500 ring-2 ring-white animate-ping"></span>
        </div>

        <!-- Title & Role Info -->
        <h3 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
          Sesi Anda Akan Segera Berakhir
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Masuk sebagai <strong class="text-slate-800 capitalize">{{ formatRole(auth.role) }}</strong> &bull; {{ auth.user?.nama || 'Pengguna' }}
        </p>

        <!-- Countdown Display Box -->
        <div class="my-5 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col items-center justify-center">
          <span class="text-xs text-slate-500 font-medium">Sistem akan keluar otomatis dalam:</span>
          <div class="flex items-baseline gap-1 mt-1">
            <span class="text-4xl font-extrabold text-[#E68202] tracking-tight tabular-nums">
              {{ countdownSeconds }}
            </span>
            <span class="text-sm font-bold text-slate-600">detik</span>
          </div>
          <span class="text-[11px] text-slate-400 mt-1">
            Tidak ada aktivitas terdeteksi pada perangkat Anda
          </span>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed mb-6">
          Untuk menjaga keamanan data dan privasi akun Anda di Kostara, sesi akan ditutup otomatis bila tidak ada konfirmasi lanjutan.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center gap-2.5">
          <button
            @click="handleLogoutImmediately"
            type="button"
            class="w-full sm:flex-1 py-3 px-4 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition cursor-pointer order-2 sm:order-1"
          >
            Keluar Sekarang
          </button>
          <button
            @click="extendSession"
            type="button"
            class="w-full sm:flex-[2] py-3 px-5 rounded-xl bg-[#017E91] hover:bg-[#016877] text-white text-xs font-bold transition shadow-md hover:shadow-lg active:scale-98 flex items-center justify-center gap-2 cursor-pointer order-1 sm:order-2"
          >
            <PhShieldCheck :size="16" weight="bold" />
            <span>Lanjutkan Sesi Saya</span>
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { PhHourglassHigh, PhShieldCheck } from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const route = useRoute()
const router = useRouter()

const STORAGE_KEY = 'kostara_last_activity'
const WARNING_WINDOW_SECONDS = 60 // Tampilkan peringatan 60 detik sebelum habis
const sessionStartTime = Date.now()

const showWarningModal = ref(false)
const countdownSeconds = ref(60)

let pollInterval = null
let lastThrottledTime = 0

// Timeout durasi per role (dalam detik)
const getRoleTimeoutSeconds = (role) => {
  switch (role) {
    case 'admin':
      return 15 * 60 // 15 Menit untuk Admin (High Security)
    case 'pemilik':
      return 30 * 60 // 30 Menit untuk Mitra Pemilik
    case 'pencari':
    default:
      return 60 * 60 // 60 Menit untuk Pencari Kos
  }
}

const formatRole = (role) => {
  if (role === 'admin') return 'Administrator'
  if (role === 'pemilik') return 'Mitra Pemilik'
  if (role === 'pencari') return 'Pencari Kos'
  return 'Pengguna'
}

// Catat waktu aktivitas terbaru (sinkron ke localStorage untuk multi-tab)
const recordActivity = (force = false) => {
  const now = Date.now()
  if (!force && now - lastThrottledTime < 2000) {
    return // Throttle 2 detik untuk hemat resource
  }
  lastThrottledTime = now
  try {
    localStorage.setItem(STORAGE_KEY, now.toString())
  } catch (e) {
    // Abaikan jika storage penuh
  }
}

// User activity event handler
const onUserActivity = () => {
  if (!auth.isAuthenticated) return
  if (!showWarningModal.value) {
    recordActivity()
  }
}

// Perpanjang sesi saat tombol ditekan
const extendSession = async () => {
  recordActivity(true)
  showWarningModal.value = false
  ui.success('Sesi Anda berhasil diperpanjang.')
  try {
    if (auth.token) {
      await auth.fetchMe()
    }
  } catch (e) {
    // Abaikan error fetch
  }
}

// Logout saat tombol keluar diklik
const handleLogoutImmediately = async () => {
  showWarningModal.value = false
  await auth.logout()
  ui.info('Anda telah keluar dari akun.')
  router.push({ name: 'login' })
}

// Logout otomatis saat countdown habis
const handleAutoTimeout = async () => {
  stopTimer()
  showWarningModal.value = false
  const redirectPath = route.fullPath !== '/login' ? route.fullPath : '/'
  await auth.logout()
  ui.warning('Sesi Anda telah berakhir demi keamanan akun. Silakan masuk kembali.')
  router.push({ name: 'login', query: { redirect: redirectPath } })
}

// Check interval loop
const checkSessionTimeout = () => {
  if (!auth.isAuthenticated) {
    showWarningModal.value = false
    stopTimer()
    return
  }

  const rawLast = localStorage.getItem(STORAGE_KEY)
  const lastTime = rawLast ? parseInt(rawLast, 10) : null

  if (!lastTime || isNaN(lastTime) || lastTime < sessionStartTime) {
    recordActivity(true)
    return
  }

  const now = Date.now()
  const elapsedSeconds = Math.floor((now - lastTime) / 1000)

  const roleTimeoutSeconds = getRoleTimeoutSeconds(auth.role)
  const remainingSeconds = roleTimeoutSeconds - elapsedSeconds

  if (remainingSeconds <= 0) {
    // Waktu habis, eksekusi auto-logout
    handleAutoTimeout()
  } else if (remainingSeconds <= WARNING_WINDOW_SECONDS) {
    // Masuk zona peringatan (60 detik terakhir)
    countdownSeconds.value = Math.max(1, remainingSeconds)
    showWarningModal.value = true
  } else {
    // Masih dalam batas aman
    showWarningModal.value = false
  }
}

function stopTimer() {
  if (pollInterval) {
    clearInterval(pollInterval)
    pollInterval = null
  }
}

function startTimer() {
  stopTimer()
  pollInterval = setInterval(checkSessionTimeout, 1000)
}

// Monitor perubahan status login
watch(
  () => auth.isAuthenticated,
  (isLoggedIn) => {
    if (isLoggedIn) {
      recordActivity(true)
      startTimer()
    } else {
      stopTimer()
      showWarningModal.value = false
    }
  },
  { immediate: true }
)

const userEvents = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart']

onMounted(() => {
  if (auth.isAuthenticated) {
    recordActivity(true)
    startTimer()
  }

  userEvents.forEach((evt) => {
    window.addEventListener(evt, onUserActivity, { passive: true })
  })
})

onUnmounted(() => {
  stopTimer()
  userEvents.forEach((evt) => {
    window.removeEventListener(evt, onUserActivity)
  })
})
</script>
