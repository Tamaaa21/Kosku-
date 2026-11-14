<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-between font-sans text-slate-800">
    <TenantNavbar />

    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-24">
      <div class="max-w-md w-full text-center">
        <!-- 403 Badge & Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-rose-50 text-rose-600 border border-rose-200/60 shadow-sm mb-6">
          <PhShieldWarning :size="40" weight="duotone" />
        </div>

        <p class="text-sm font-bold tracking-wider uppercase text-rose-600 mb-2">Error 403</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">
          Akses Tidak Diizinkan
        </h1>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8">
          Halaman ini dibatasi hanya untuk peran akun tertentu (misalnya Mitra Pemilik Kos atau Administrator). Akun Anda saat ini tidak memiliki otorisasi untuk mengakses rute ini.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <button
            @click="navigateHomeOrDashboard"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-semibold text-sm shadow-sm hover:bg-primary-container transition active:scale-95 cursor-pointer"
          >
            <PhArrowLeft :size="18" weight="bold" />
            Kembali ke Halaman Aman
          </button>
          <router-link
            to="/profil"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition active:scale-95"
          >
            <PhUser :size="18" weight="bold" />
            Lihat Profil
          </router-link>
        </div>
      </div>
    </main>

    <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-100 bg-white">
      © 2026 Kostara &middot; Keamanan Akun & Otorisasi Berbasis Peran (RBAC)
    </footer>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import TenantNavbar from '../components/TenantNavbar.vue'
import { PhShieldWarning, PhArrowLeft, PhUser } from '@phosphor-icons/vue'

const router = useRouter()
const authStore = useAuthStore()

function navigateHomeOrDashboard() {
  if (authStore.isAdmin) {
    router.push({ name: 'admin-dashboard' })
  } else if (authStore.isPemilik) {
    router.push({ name: 'pemilik-dashboard' })
  } else {
    router.push({ name: 'home' })
  }
}
</script>
