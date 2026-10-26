<template>
  <div>
    <!-- Mobile Backdrop Overlay -->
    <div
      v-if="isMobileOpen"
      @click="isMobileOpen = false"
      class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 lg:hidden transition-opacity"
    ></div>

    <!-- Left Sidebar (Desktop Fixed & Mobile Drawer) -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 w-64 bg-slate-900 text-slate-100 z-50 flex flex-col justify-between shadow-xl transition-transform duration-300 select-none border-r border-slate-800',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <div class="flex flex-col flex-1 min-h-0">
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80">
          <router-link to="/dashboard/pemilik" class="flex items-center gap-2.5" @click="isMobileOpen = false">
            <img
              :src="logoUrl"
              alt="Kostara Logo"
              class="w-9 h-9 object-contain"
            />
            <div class="flex flex-col">
              <div class="flex items-center gap-1.5 leading-none">
                <span class="text-lg font-bold text-white tracking-tight">Kostara</span>
                <span class="text-[9px] uppercase font-bold text-primary bg-primary-fixed px-1.5 py-0.5 rounded tracking-wider">
                  Partner
                </span>
              </div>
              <span class="text-[10px] text-slate-400 mt-0.5 font-medium">Mitra Pemilik Kos</span>
            </div>
          </router-link>

          <!-- Mobile Close Button -->
          <button
            @click="isMobileOpen = false"
            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
            type="button"
          >
            <PhX :size="20" weight="bold" />
          </button>
        </div>

        <!-- Section Label -->
        <div class="px-5 pt-4 pb-2">
          <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
            Menu Pengelola
          </span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
          <!-- 1. Dashboard -->
          <router-link
            to="/dashboard/pemilik"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSquaresFour :size="20" weight="bold" />
            <span class="flex-1">Dashboard</span>
          </router-link>

          <!-- 2. Data Kos -->
          <router-link
            to="/dashboard/pemilik/kos"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/kos' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhBuildings :size="20" weight="bold" />
            <span class="flex-1">Data Kos</span>
          </router-link>

          <!-- 3. Kelola Kamar -->
          <router-link
            to="/dashboard/pemilik/kamar"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/kamar' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhBed :size="20" weight="bold" />
            <span class="flex-1">Kelola Kamar</span>
          </router-link>

          <!-- 4. Permintaan Pemesanan -->
          <router-link
            to="/dashboard/pemilik/pemesanan"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/pemesanan' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhCalendarCheck :size="20" weight="bold" />
            <span class="flex-1">Pemesanan Masuk</span>
          </router-link>

          <!-- 5. Profil Pemilik -->
          <router-link
            to="/profil"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/profil' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhUser :size="20" weight="bold" />
            <span class="flex-1">Profil Saya</span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom Action: Log Out -->
      <div class="p-3 border-t border-slate-800 bg-slate-950/50">
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2.5 px-4 py-2.5 rounded-xl text-xs font-bold text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-600 transition shadow-xs cursor-pointer"
          type="button"
          title="Keluar / Log Out"
        >
          <PhSignOut :size="18" weight="bold" />
          <span>Log Out</span>
        </button>
      </div>
    </aside>

    <!-- Top Bar Header for Pemilik (Spans right of sidebar) -->
    <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface-container-lowest/95 backdrop-blur-md border-b border-surface-container-high/40 z-30 px-4 sm:px-6 flex items-center justify-between shadow-xs">
      <div class="flex items-center gap-3">
        <!-- Hamburger Button (Mobile Only) -->
        <button
          @click="isMobileOpen = true"
          class="lg:hidden p-2 rounded-lg text-on-surface hover:bg-surface-container transition"
          type="button"
          aria-label="Buka Menu Sidebar"
        >
          <PhList :size="22" weight="bold" />
        </button>

        <!-- Location Indicator -->
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <PhBuildings :size="18" weight="bold" />
          </div>
          <span class="font-bold text-sm sm:text-base text-on-surface">{{ currentPageTitle }}</span>
        </div>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-3">
        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <router-link
          to="/profil"
          class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-surface-container transition group"
          title="Profil Pemilik"
        >
          <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary flex items-center justify-center font-bold text-xs uppercase overflow-hidden">
            <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
            <PhUser v-else :size="18" weight="bold" />
          </div>
          <span class="text-xs font-semibold text-on-surface hidden md:inline max-w-[120px] truncate">
            {{ auth.user?.nama }}
          </span>
        </router-link>
      </div>
    </header>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import NotificationDropdown from './NotificationDropdown.vue'
import logoUrl from '../assets/logo.png'
import {
  PhX,
  PhSquaresFour,
  PhBuildings,
  PhBed,
  PhCalendarCheck,
  PhUser,
  PhSignOut,
  PhList
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const route = useRoute()
const router = useRouter()
const isMobileOpen = ref(false)

const currentPageTitle = computed(() => {
  if (route.path.includes('/dashboard/pemilik/kos')) return 'Kelola Data Kos'
  if (route.path.includes('/dashboard/pemilik/kamar')) return 'Kelola Data Kamar'
  if (route.path.includes('/dashboard/pemilik/pemesanan')) return 'Permintaan Sewa'
  if (route.path === '/dashboard/pemilik') return 'Ringkasan Eksekutif'
  return 'Portal Mitra Pemilik'
})

const handleLogout = async () => {
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari akun?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  isMobileOpen.value = false
  await auth.logout()
  ui.success('Anda telah berhasil keluar.')
  router.push({ name: 'home' })
}
</script>
