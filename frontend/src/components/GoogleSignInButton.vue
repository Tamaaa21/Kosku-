<template>
  <div class="w-full">
    <!-- Google Sign-In Main Button -->
    <button
      type="button"
      @click="openModal"
      :disabled="auth.loading"
      class="w-full flex items-center justify-center gap-3 py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-100 font-semibold text-xs sm:text-sm shadow-2xs transition active:scale-[0.99] cursor-pointer disabled:opacity-50"
    >
      <!-- Google SVG Icon -->
      <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
        <path
          fill="#4285F4"
          d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
        />
        <path
          fill="#34A853"
          d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
        />
        <path
          fill="#FBBC05"
          d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"
        />
        <path
          fill="#EA4335"
          d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"
        />
      </svg>
      <span>{{ label || 'Lanjutkan dengan Google' }}</span>
    </button>

    <!-- Modal Konfirmasi Peran & Login Google -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
        @click.self="isOpen = false"
      >
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl relative text-left transition-colors">
          <!-- Close button -->
          <button
            @click="isOpen = false"
            type="button"
            class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
          >
            <PhX :size="18" weight="bold" />
          </button>

          <!-- Modal Header -->
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path
                  fill="#4285F4"
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                />
                <path
                  fill="#34A853"
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                />
                <path
                  fill="#FBBC05"
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"
                />
                <path
                  fill="#EA4335"
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-white">
                Masuk dengan Akun Google
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Pilih peran Anda di platform Kostara
              </p>
            </div>
          </div>

          <!-- Error Alert -->
          <div
            v-if="errorMessage"
            class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800"
          >
            {{ errorMessage }}
          </div>

          <!-- Role Selector -->
          <div class="mb-5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider">
              Daftar / Masuk Sebagai:
            </label>
            <div class="grid grid-cols-2 gap-3">
              <button
                type="button"
                @click="selectedRole = 'pencari'"
                :class="[
                  'p-3 rounded-xl border text-left transition cursor-pointer flex flex-col justify-between',
                  selectedRole === 'pencari'
                    ? 'border-primary bg-primary/10 text-primary dark:bg-primary/20 ring-2 ring-primary/30'
                    : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300'
                ]"
              >
                <div class="flex items-center justify-between mb-1">
                  <span class="text-xs font-bold">Pencari Kos</span>
                  <PhCheckCircle v-if="selectedRole === 'pencari'" :size="16" weight="fill" class="text-primary" />
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">
                  Sewa kos & bayar DP 20%
                </span>
              </button>

              <button
                type="button"
                @click="selectedRole = 'pemilik'"
                :class="[
                  'p-3 rounded-xl border text-left transition cursor-pointer flex flex-col justify-between',
                  selectedRole === 'pemilik'
                    ? 'border-primary bg-primary/10 text-primary dark:bg-primary/20 ring-2 ring-primary/30'
                    : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300'
                ]"
              >
                <div class="flex items-center justify-between mb-1">
                  <span class="text-xs font-bold">Mitra Pemilik</span>
                  <PhCheckCircle v-if="selectedRole === 'pemilik'" :size="16" weight="fill" class="text-primary" />
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight">
                  Kelola kos & terima sewa
                </span>
              </button>
            </div>

            <!-- Notice for Mitra Pemilik -->
            <div
              v-if="selectedRole === 'pemilik'"
              class="mt-3 p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-[11px] text-amber-800 dark:text-amber-300 flex items-start gap-2"
            >
              <PhInfo :size="16" weight="bold" class="shrink-0 mt-0.5 text-amber-600 dark:text-amber-400" />
              <span>
                <strong>Catatan Pemilik:</strong> Setelah login dengan Google, Anda akan diarahkan untuk melengkapi formulir verifikasi KTP & kepemilikan kos sebelum mengelola kamar.
              </span>
            </div>
          </div>

          <!-- Quick Demo Buttons (Uji Coba Pengembang) -->
          <div class="mb-4 pt-3 border-t border-slate-100 dark:border-slate-800">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
              Uji Coba Cepat (Akun Google Demo):
            </span>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="handleDemoGoogle('budi.pencari@gmail.com', 'Budi Santoso (Google)', 'pencari')"
                :disabled="loading"
                class="px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200 text-center transition cursor-pointer"
              >
                Google: Pencari
              </button>
              <button
                type="button"
                @click="handleDemoGoogle('juragan.pemilik@gmail.com', 'Haji Mahmud (Google)', 'pemilik')"
                :disabled="loading"
                class="px-2.5 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200 text-center transition cursor-pointer"
              >
                Google: Pemilik
              </button>
            </div>
          </div>

          <!-- Action Button -->
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="handleRealGoogleLogin"
              :disabled="loading"
              class="flex-1 py-2.5 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs cursor-pointer disabled:opacity-50"
            >
              <PhCircleNotch v-if="loading" :size="16" class="animate-spin" />
              <span>{{ loading ? 'Menghubungkan...' : 'Lanjutkan Akun Google' }}</span>
            </button>

            <button
              type="button"
              @click="isOpen = false"
              :disabled="loading"
              class="py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import { PhX, PhCheckCircle, PhInfo, PhCircleNotch } from '@phosphor-icons/vue'

const props = defineProps({
  label: {
    type: String,
    default: 'Lanjutkan dengan Google',
  },
  defaultRole: {
    type: String,
    default: 'pencari',
  },
})

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()
const ui = useUiStore()

const isOpen = ref(false)
const selectedRole = ref(props.defaultRole)
const loading = ref(false)
const errorMessage = ref('')

const openModal = () => {
  selectedRole.value = props.defaultRole
  errorMessage.value = ''
  isOpen.value = true
}

// Simulasi login Google untuk testing & lingkungan lokal
const handleDemoGoogle = async (email, name, role) => {
  loading.value = true
  errorMessage.value = ''
  try {
    const { user } = await auth.loginWithGoogle({
      idToken: 'mock-google-token',
      role: role || selectedRole.value,
      demoUser: {
        sub: 'google-sub-' + Math.abs(hashCode(email)),
        email,
        name,
        picture: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=017E91&color=fff`,
      },
    })

    ui.success(`Selamat datang, ${user.nama}!`)
    isOpen.value = false

    const redirectUrl = route.query.redirect
    if (redirectUrl) {
      router.push(redirectUrl)
    } else if (user.role === 'pemilik') {
      router.push('/dashboard/pemilik')
    } else {
      router.push('/pesanan')
    }
  } catch (err) {
    errorMessage.value = auth.error || 'Gagal masuk dengan akun Google.'
  } finally {
    loading.value = false
  }
}

// Handle login Google sungguhan (jika GSI tersedia atau fallback demo)
const handleRealGoogleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  // Jika Google GSI client sudah terpasang di window
  if (window.google?.accounts?.id) {
    try {
      window.google.accounts.id.prompt((notification) => {
        if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
          // Fallback ke login demo Google jika prompt native tidak muncul
          handleDemoGoogle(
            `user.${selectedRole.value}@gmail.com`,
            selectedRole.value === 'pemilik' ? 'Mitra Pemilik Google' : 'Pencari Kos Google',
            selectedRole.value
          )
        }
      })
    } catch (_) {
      handleDemoGoogle(
        `user.${selectedRole.value}@gmail.com`,
        selectedRole.value === 'pemilik' ? 'Mitra Pemilik Google' : 'Pencari Kos Google',
        selectedRole.value
      )
    }
  } else {
    // Mode demo pengujian langsung jika Google Identity Services belum dikonfigurasi GCP client ID-nya
    await handleDemoGoogle(
      `user.${selectedRole.value}@gmail.com`,
      selectedRole.value === 'pemilik' ? 'Mitra Pemilik Google' : 'Pencari Kos Google',
      selectedRole.value
    )
  }
}

function hashCode(str) {
  let hash = 0
  for (let i = 0; i < str.length; i++) {
    hash = (hash << 5) - hash + str.charCodeAt(i)
    hash |= 0
  }
  return hash
}
</script>
