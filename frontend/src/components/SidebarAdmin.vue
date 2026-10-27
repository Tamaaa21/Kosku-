<template>
  <div>
    <!-- Backdrop Overlay (Both Mobile & Desktop when drawer opened) -->
    <transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isMobileOpen"
        @click="isMobileOpen = false"
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 transition-opacity cursor-pointer"
      ></div>
    </transition>

    <!-- Left Sidebar (Desktop & Mobile Drawer - Collapsible for full-width view) -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 w-64 bg-slate-950 text-slate-100 z-50 flex flex-col justify-between shadow-2xl transition-transform duration-300 ease-in-out select-none border-r border-slate-800/80',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex flex-col flex-1 min-h-0">
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80">
          <router-link to="/dashboard/admin" class="flex items-center gap-2.5" @click="isMobileOpen = false">
            <img
              :src="logoUrl"
              alt="Kostara Logo"
              class="w-9 h-9 object-contain"
            />
            <div class="flex flex-col">
              <div class="flex items-center gap-1.5 leading-none">
                <span class="text-lg font-bold text-white tracking-tight">Kostara</span>
                <span class="text-[9px] uppercase font-bold text-white bg-primary px-1.5 py-0.5 rounded tracking-wider">
                  Admin
                </span>
              </div>
              <span class="text-[10px] text-slate-400 mt-0.5 font-medium">Panel Administrator</span>
            </div>
          </router-link>

          <!-- Close Button (Both Mobile & Desktop) -->
          <button
            @click="isMobileOpen = false"
            class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer"
            type="button"
            aria-label="Tutup Menu"
          >
            <PhX :size="20" weight="bold" />
          </button>
        </div>

        <!-- Section Label -->
        <div class="px-5 pt-4 pb-2">
          <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
            Navigasi Utama
          </span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
          <!-- 1. Ringkasan & Analitik -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'ringkasan' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && (!$route.query.tab || $route.query.tab === 'ringkasan')) ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSquaresFour :size="20" weight="bold" />
            <span class="flex-1">Ringkasan & Analitik</span>
          </router-link>

          <!-- 2. Manajemen Pengguna (Terpadu: Mitra Pemilik & Pencari) -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pengguna' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && ($route.query.tab === 'pengguna' || $route.query.tab === 'pencari' || $route.query.tab === 'pemilik')) ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhUsersThree :size="20" weight="bold" />
            <span class="flex-1">Manajemen Pengguna</span>
          </router-link>

          <!-- 3. Verifikasi Kepemilikan Pemilik Kos -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'verifikasi' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'verifikasi') ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhCertificate :size="20" weight="bold" />
            <span class="flex-1">Verifikasi Pemilik</span>
          </router-link>

          <!-- 4. Data & Moderasi Kos -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'kos' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'kos') ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhBuildings :size="20" weight="bold" />
            <span class="flex-1">Data & Moderasi Kos</span>
          </router-link>

          <!-- 4. Audit Log Pemesanan -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pemesanan' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'pemesanan') ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhReceipt :size="20" weight="bold" />
            <span class="flex-1">Audit Log Pemesanan</span>
          </router-link>

          <!-- 5. Kelola Hero Slider & Banner -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'banner' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && ($route.query.tab === 'banner' || $route.query.tab === 'hero-slider')) ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSlideshow :size="20" weight="bold" />
            <span class="flex-1">Banner & Hero Slider</span>
          </router-link>

          <!-- 5. Profil Admin -->
          <router-link
            to="/profil"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/profil' ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhShieldCheck :size="20" weight="bold" />
            <span class="flex-1">Profil Administrator</span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom Action: Log Out -->
      <div class="p-3 border-t border-slate-800 bg-slate-900/60">
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

    <!-- Top Bar Header for Admin (Spans full width like Owner page - matches ui-dashboard-admin.png) -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-2xs">
      <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <!-- Hamburger Button (Visible on both desktop & mobile) -->
        <button
          @click="isMobileOpen = !isMobileOpen"
          class="p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition shrink-0 cursor-pointer"
          type="button"
          aria-label="Buka / Tutup Menu Navigasi"
          title="Buka / Tutup Menu"
        >
          <PhList :size="22" weight="bold" />
        </button>

        <!-- Search Input Pill (Matching ui-dashboard-admin.png) -->
        <div class="relative w-56 sm:w-72 md:w-80">
          <PhMagnifyingGlass :size="16" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 select-none pointer-events-none" />
          <input
            v-model="adminSearchQuery"
            @keydown.enter="handleAdminSearch"
            type="text"
            placeholder="Cari pengguna, properti, atau reservasi..."
            class="w-full pl-9 pr-4 py-2 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs rounded-full border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800 placeholder:text-slate-400"
          />
        </div>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-3 sm:gap-4 shrink-0">
        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <!-- Date Range Pill Dropdown -->
        <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 select-none">
          <PhCalendarBlank :size="15" class="text-slate-500" />
          <span>{{ currentPeriodDateRange }}</span>
          <PhCaretDown :size="12" class="text-slate-400" />
        </div>

        <!-- Admin Profile Dropdown Link -->
        <router-link
          to="/profil"
          class="flex items-center gap-2.5 pl-1.5 pr-2 py-1 rounded-full hover:bg-slate-100 transition group cursor-pointer"
          title="Profil Administrator"
        >
          <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs uppercase overflow-hidden shadow-xs">
            <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
            <span v-else>A</span>
          </div>
          <span class="text-xs font-bold text-slate-800 hidden md:inline">
            Admin
          </span>
          <PhCaretDown :size="12" class="text-slate-400 hidden md:inline" />
        </router-link>
      </div>
    </header>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import NotificationDropdown from './NotificationDropdown.vue'
import logoUrl from '../assets/logo.png'
import {
  PhX,
  PhSquaresFour,
  PhUsersThree,
  PhBuildings,
  PhReceipt,
  PhSlideshow,
  PhShieldCheck,
  PhSignOut,
  PhList,
  PhCertificate,
  PhMagnifyingGlass,
  PhCalendarBlank,
  PhCaretDown
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()
const isMobileOpen = ref(false)
const adminSearchQuery = ref('')

const currentPeriodDateRange = computed(() => {
  const now = new Date()
  const start = new Date(now.getFullYear(), now.getMonth(), 1)
  const formatOpts = { day: '2-digit', month: 'short', year: 'numeric' }
  const startStr = start.toLocaleDateString('id-ID', formatOpts)
  const endStr = now.toLocaleDateString('id-ID', formatOpts)
  return `${startStr} - ${endStr}`
})

const handleAdminSearch = () => {
  if (!adminSearchQuery.value.trim()) return
  router.push({
    path: '/dashboard/admin',
    query: { tab: 'pengguna', q: adminSearchQuery.value.trim() },
  })
}

const handleLogout = async () => {
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin mengakhiri sesi dan keluar dari akun?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  isMobileOpen.value = false
  await auth.logout()
  ui.success('Sesi administrator telah diakhiri.')
  router.push({ name: 'home' })
}
</script>
