<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12 pt-20">
    <div class="bg-surface-container-lowest dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-md max-w-md w-full transition-colors">
      <div class="text-center mb-6">
        <img :src="logoUrl" alt="Kostara Logo" class="w-14 h-14 object-contain mx-auto mb-3" />
        <h2 class="text-2xl font-bold text-on-surface dark:text-white tracking-tight">
          Masuk ke <span class="text-[#017E91]">Kos</span><span class="text-[#E68202]">tara</span>
        </h2>
        <p class="text-xs text-on-surface-variant dark:text-slate-400 mt-1">
          Pintu masuk penyewa dan mitra pemilik kos terpercaya.
        </p>
      </div>

      <!-- Error Alert with Admin Redirect Hint -->
      <div
        v-if="auth.error"
        class="mb-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs font-semibold border border-rose-200 dark:border-rose-800/80"
      >
        <p>{{ auth.error }}</p>
        <div v-if="auth.error.includes('Administrator')" class="mt-2 pt-2 border-t border-rose-200/60 dark:border-rose-800/60">
          <router-link
            to="/admin/login"
            class="inline-flex items-center gap-1.5 text-primary dark:text-primary-container font-bold hover:underline"
          >
            <PhShieldCheck :size="15" weight="bold" />
            <span>Buka Portal Login Administrator &rarr;</span>
          </router-link>
        </div>
      </div>

      <!-- Form Login Manual (Email & Password) -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-on-surface dark:text-slate-200 mb-1">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            autocomplete="email"
            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary p-3"
            placeholder="nama@example.com"
          />
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-semibold text-on-surface dark:text-slate-200">Kata Sandi</label>
            <router-link
              to="/forgot-password"
              class="text-[11px] font-semibold text-primary hover:underline hover:text-primary-hover transition"
            >
              Lupa Kata Sandi?
            </router-link>
          </div>
          <input
            type="password"
            v-model="form.password"
            required
            autocomplete="current-password"
            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-primary focus:border-primary p-3"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 rounded-xl transition text-xs shadow-md disabled:opacity-50 mt-2 flex items-center justify-center gap-1.5 cursor-pointer"
        >
          <PhSignIn :size="16" weight="bold" />
          <span>{{ auth.loading ? 'Memproses...' : 'Masuk Sekarang' }}</span>
        </button>
      </form>

      <!-- Divider "ATAU" -->
      <div class="relative flex items-center justify-center my-6">
        <div class="border-t border-slate-200 dark:border-slate-800 w-full"></div>
        <span class="bg-surface-container-lowest dark:bg-slate-900 px-3 text-[11px] uppercase tracking-wider font-bold text-slate-400 shrink-0">
          atau masuk dengan
        </span>
        <div class="border-t border-slate-200 dark:border-slate-800 w-full"></div>
      </div>

      <!-- Google Sign-In Button (Di Bawah Form) -->
      <div class="mb-2">
        <GoogleSignInButton label="Masuk dengan Google" default-role="pencari" />
      </div>

      <p class="text-center text-xs text-on-surface-variant dark:text-slate-400 mt-6">
        Belum memiliki akun?
        <router-link to="/register" class="text-primary font-bold hover:underline">
          Daftar Sekarang
        </router-link>
      </p>

      <!-- Portal Administrator Link at Footer -->
      <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
        <router-link
          to="/admin/login"
          class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition"
        >
          <PhShield :size="14" weight="bold" />
          <span>Portal Khusus Administrator &rarr;</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { PhSignIn, PhShield, PhShieldCheck } from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import GoogleSignInButton from '../components/GoogleSignInButton.vue'
import logoUrl from '../assets/logo.png'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const handleLogin = async () => {
  try {
    const user = await auth.login(form)
    const redirectUrl = route.query.redirect

    if (redirectUrl) {
      router.push(redirectUrl)
    } else if (user.role === 'pemilik') {
      router.push('/dashboard/pemilik')
    } else {
      router.push('/pesanan')
    }
  } catch (err) {
    // Error ditangani di store
  }
}
</script>
