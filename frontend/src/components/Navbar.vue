<template>
  <header class="fixed top-0 left-0 right-0 z-50 h-16 bg-surface-container-lowest/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-b border-surface-container-high/40">
    <div class="h-16 max-w-7xl mx-auto px-margin flex items-center justify-between gap-space-md">
      <!-- Logo Brand -->
      <router-link to="/" class="flex items-center gap-space-sm group">
        <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white shadow-sm group-hover:bg-primary-container transition">
          <span class="material-symbols-outlined text-[22px]">home_work</span>
        </div>
        <div class="flex items-center gap-1.5">
          <span class="text-xl font-bold text-primary tracking-tight">KosKu</span>
          <span class="hidden sm:inline-block text-[11px] font-semibold text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">Tegal</span>
        </div>
      </router-link>

      <!-- Main Navigation Menu -->
      <nav class="hidden md:flex items-center gap-space-lg">
        <template v-if="auth.isPemilik">
          <router-link
            to="/dashboard/pemilik"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/dashboard/pemilik' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Dashboard
          </router-link>
          <router-link
            to="/dashboard/pemilik/kos"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/dashboard/pemilik/kos' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Data Kos
          </router-link>
          <router-link
            to="/dashboard/pemilik/kamar"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/dashboard/pemilik/kamar' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Kamar
          </router-link>
          <router-link
            to="/dashboard/pemilik/pemesanan"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/dashboard/pemilik/pemesanan' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Pemesanan
          </router-link>
          <router-link
            to="/profil"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/profil' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Profil
          </router-link>
        </template>

        <template v-else>
          <router-link
            to="/"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Beranda
          </router-link>
          <router-link
            to="/kos"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/kos' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Cari Kos
          </router-link>
          <router-link
            to="/peta"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/peta' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Peta Lokasi
          </router-link>
          <router-link
            v-if="auth.isAuthenticated"
            to="/pesanan"
            class="text-sm font-medium transition-colors"
            :class="$route.path.startsWith('/pesanan') ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Riwayat Pesanan
          </router-link>
          <router-link
            v-if="auth.isAuthenticated"
            to="/profil"
            class="text-sm font-medium transition-colors"
            :class="$route.path === '/profil' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
          >
            Profil
          </router-link>
        </template>
      </nav>

      <!-- Right Action / User Profile -->
      <div class="flex items-center gap-space-sm">
        <template v-if="auth.isAuthenticated">
          <!-- Admin Panel Shortcut Only -->
          <router-link
            v-if="auth.isAdmin"
            to="/dashboard/admin"
            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-primary-fixed text-on-primary-fixed hover:bg-primary-fixed-dim transition"
          >
            <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
            <span>Panel Admin</span>
          </router-link>

          <!-- User Avatar & Dropdown / Profile Link -->
          <router-link
            to="/profil"
            class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-surface-container transition group"
            title="Profil Pengguna"
          >
            <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary flex items-center justify-center font-bold text-xs uppercase overflow-hidden">
              <span class="material-symbols-outlined text-[18px]">person</span>
            </div>
            <span class="text-xs font-semibold text-on-surface hidden lg:inline max-w-[100px] truncate">
              {{ auth.user?.nama }}
            </span>
          </router-link>

          <button
            @click="handleLogout"
            class="text-xs text-error hover:bg-error-container/40 p-2 rounded-lg font-medium transition flex items-center justify-center"
            title="Keluar"
          >
            <span class="material-symbols-outlined text-[18px]">logout</span>
          </button>
        </template>

        <template v-else>
          <router-link
            to="/login"
            class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-on-surface-variant hover:text-primary transition"
          >
            Masuk
          </router-link>
          <router-link
            to="/register"
            class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-primary-container hover:bg-primary text-white text-sm font-semibold transition shadow-sm"
          >
            Daftar
          </router-link>
        </template>
      </div>
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
