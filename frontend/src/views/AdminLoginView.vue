<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between py-10 px-4 sm:px-6 lg:px-8 relative overflow-hidden select-none">
    <!-- Ambient Background Lighting -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Navigation Header -->
    <header class="w-full max-w-5xl mx-auto flex items-center justify-between z-10">
      <router-link
        to="/"
        class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition cursor-pointer"
      >
        <PhArrowLeft :size="16" weight="bold" />
        <span>Kembali ke Beranda Publik</span>
      </router-link>

      <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900 border border-slate-800 text-[11px] font-mono text-emerald-400">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>SISTEM AKTIF</span>
      </div>
    </header>

    <!-- Main Login Card -->
    <main class="w-full max-w-md mx-auto my-auto z-10 pt-4 pb-6">
      <div class="bg-slate-900/90 backdrop-blur-md rounded-3xl border border-slate-800 shadow-2xl p-6 sm:p-8">
        <!-- Brand Header -->
        <div class="text-center mb-6">
          <div class="w-16 h-16 rounded-2xl bg-slate-800/80 border border-slate-700/60 p-3 mx-auto mb-4 flex items-center justify-center shadow-lg">
            <img :src="logoUrl" alt="Kostara Logo" class="w-full h-full object-contain" />
          </div>

          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-400 text-[10px] font-bold uppercase tracking-wider mb-2">
            <PhShieldCheck :size="14" weight="bold" />
            <span>Internal Administrator Portal</span>
          </div>

          <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
            Panel Administrator
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            Masuk dengan kredensial administrator terverifikasi
          </p>
        </div>

        <!-- Security Warning Notice -->
        <div class="mb-5 p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400 leading-relaxed flex items-start gap-2.5">
          <PhLockKey :size="16" weight="bold" class="text-amber-400 shrink-0 mt-0.5" />
          <span>
            Area terbatas. Akses tanpa wewenang dilarang keras dan dicatat pada audit log keamanan server.
          </span>
        </div>

        <!-- Error Alert -->
        <div
          v-if="errorMessage"
          class="mb-4 p-3.5 rounded-xl bg-rose-500/15 border border-rose-500/40 text-rose-300 text-xs font-semibold flex items-start gap-2.5"
        >
          <PhWarningCircle :size="18" weight="bold" class="shrink-0 mt-0.5 text-rose-400" />
          <div class="flex-1 leading-snug">
            {{ errorMessage }}
          </div>
        </div>

        <!-- Form Login -->
        <form @submit.prevent="handleAdminLogin" class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5 uppercase tracking-wider">
              Email Administrator
            </label>
            <div class="relative">
              <input
                v-model="form.email"
                type="email"
                required
                autocomplete="username"
                placeholder="admin@kos.test"
                class="w-full text-xs font-medium bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-3 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition"
              />
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                Kata Sandi
              </label>
            </div>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full text-xs font-medium bg-slate-950 border border-slate-800 rounded-xl pl-3.5 pr-10 py-3 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 inset-y-0 flex items-center text-slate-500 hover:text-slate-300 cursor-pointer"
              >
                <PhEyeSlash v-if="showPassword" :size="16" />
                <PhEye v-else :size="16" />
              </button>
            </div>
          </div>

          <button
            type="submit"
            :disabled="auth.loading"
            class="w-full mt-2 py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-white font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-primary/20 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <PhCircleNotch v-if="auth.loading" :size="18" class="animate-spin" />
            <PhSignIn v-else :size="18" weight="bold" />
            <span>{{ auth.loading ? 'Memverifikasi Administrator...' : 'Masuk ke Panel Administrator' }}</span>
          </button>
        </form>

        <!-- Footer Notice -->
        <div class="mt-6 pt-4 border-t border-slate-800 text-center">
          <p class="text-[11px] text-slate-500">
            Bukan Administrator?
            <router-link to="/login" class="text-primary hover:underline font-bold ml-1">
              Halaman Login Pengguna Biasa &rarr;
            </router-link>
          </p>
        </div>
      </div>
    </main>

    <!-- Footer Security Banner -->
    <footer class="w-full max-w-5xl mx-auto text-center text-[11px] text-slate-600 z-10">
      <span>&copy; {{ new Date().getFullYear() }} Kostara Enterprise &bull; Enkripsi TLS 1.3 &bull; Sesi Dilindungi Sanctum</span>
    </footer>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import logoUrl from '../assets/logo.png'
import {
  PhArrowLeft,
  PhShieldCheck,
  PhLockKey,
  PhSignIn,
  PhCircleNotch,
  PhEye,
  PhEyeSlash,
  PhWarningCircle,
} from '@phosphor-icons/vue'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()
const ui = useUiStore()

const showPassword = ref(false)
const errorMessage = ref('')

const form = reactive({
  email: '',
  password: '',
})

const handleAdminLogin = async () => {
  errorMessage.value = ''
  try {
    const user = await auth.loginAdmin(form)
    ui.success(`Selamat datang, Administrator ${user.nama}!`)

    const redirectUrl = route.query.redirect
    if (redirectUrl && redirectUrl.startsWith('/dashboard/admin')) {
      router.push(redirectUrl)
    } else {
      router.push('/dashboard/admin')
    }
  } catch (err) {
    errorMessage.value = auth.error || 'Akses ditolak. Gagal masuk ke panel administrator.'
  }
}
</script>
