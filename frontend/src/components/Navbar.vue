<template>
  <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-2 group">
        <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md group-hover:bg-emerald-700 transition">
          K
        </div>
        <div>
          <span class="font-extrabold text-xl text-slate-800 tracking-tight">Kos<span class="text-emerald-600">Hub</span></span>
          <span class="hidden sm:inline-block text-xs bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded ml-2">REST API v1</span>
        </div>
      </router-link>

      <!-- Nav Links -->
      <nav class="flex items-center gap-3 sm:gap-6">
        <router-link to="/" class="text-slate-600 hover:text-emerald-600 font-medium transition text-sm sm:text-base">
          Cari Kos
        </router-link>

        <template v-if="auth.isAuthenticated">
          <!-- Role Dashboard Link -->
          <router-link
            :to="dashboardRoute"
            class="text-sm font-semibold px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition"
          >
            Dashboard {{ formatRole(auth.role) }}
          </router-link>

          <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-slate-200 text-sm text-slate-600">
            <span class="font-medium text-slate-800">{{ auth.user?.nama }}</span>
          </div>

          <button
            @click="handleLogout"
            class="text-sm text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-3 py-1.5 rounded-lg font-medium transition border border-transparent hover:border-rose-200"
          >
            Keluar
          </button>
        </template>

        <template v-else>
          <router-link
            to="/login"
            class="text-sm font-semibold text-slate-700 hover:text-emerald-600 px-3 py-1.5 rounded-lg transition"
          >
            Masuk
          </router-link>
          <router-link
            to="/register"
            class="text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700 px-4 py-2 rounded-xl shadow-sm transition"
          >
            Daftar
          </router-link>
        </template>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const dashboardRoute = computed(() => {
  if (auth.isPemilik) return '/dashboard/pemilik'
  if (auth.isAdmin) return '/dashboard/admin'
  return '/dashboard/pencari'
})

const formatRole = (role) => {
  if (!role) return ''
  return role.charAt(0).toUpperCase() + role.slice(1)
}

const handleLogout = async () => {
  await auth.logout()
  router.push({ name: 'home' })
}
</script>
