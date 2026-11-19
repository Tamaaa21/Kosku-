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
                <span class="text-lg font-extrabold tracking-tight">
                  <span class="text-[#017E91]">Kos</span><span class="text-[#E68202]">tara</span>
                </span>
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

        <!-- Navigation Links Grouped by Categories -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto pb-4">
          <!-- MENU UTAMA -->
          <div class="px-3 pt-3 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Menu Utama
            </span>
          </div>

          <!-- Dashboard -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'ringkasan' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && (!$route.query.tab || $route.query.tab === 'ringkasan')) ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSquaresFour :size="20" weight="bold" />
            <span class="flex-1">Dashboard</span>
          </router-link>

          <!-- Manajemen Pengguna -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pengguna' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && ($route.query.tab === 'pengguna' || $route.query.tab === 'pencari' || $route.query.tab === 'pemilik')) ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhUsersThree :size="20" weight="bold" />
            <span class="flex-1">Manajemen Pengguna</span>
          </router-link>

          <!-- MANAJEMEN PROPERTI -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Manajemen Properti
            </span>
          </div>

          <!-- Verifikasi Properti -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'verifikasi' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'verifikasi') ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhShieldCheck :size="20" weight="bold" />
            <span class="flex-1">Verifikasi Properti</span>
          </router-link>

          <!-- Data Kos -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'kos' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'kos') ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhBuildings :size="20" weight="bold" />
            <span class="flex-1">Data Kos</span>
          </router-link>

          <!-- Kelola Kamar -->
          <router-link
            to="/dashboard/pemilik/kamar"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/kamar' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhBed :size="20" weight="bold" />
            <span class="flex-1">Kelola Kamar</span>
          </router-link>

          <!-- OPERASIONAL -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Operasional
            </span>
          </div>

          <!-- Pemesanan -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pemesanan' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'pemesanan') ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhCalendarCheck :size="20" weight="bold" />
            <span class="flex-1">Pemesanan</span>
          </router-link>

          <!-- Daftar Penghuni -->
          <router-link
            to="/dashboard/pemilik/penghuni"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/penghuni' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhUsers :size="20" weight="bold" />
            <span class="flex-1">Daftar Penghuni</span>
          </router-link>

          <!-- KEUANGAN -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Keuangan
            </span>
          </div>

          <!-- Dompet & Saldo -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pencairan' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'pencairan') ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhWallet :size="20" weight="bold" />
            <span class="flex-1">Dompet & Saldo</span>
          </router-link>

          <!-- LAINNYA -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Lainnya
            </span>
          </div>

          <!-- Profil Saya -->
          <router-link
            to="/profil"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/profil' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhUser :size="20" weight="bold" />
            <span class="flex-1">Profil Saya</span>
          </router-link>

          <!-- Pusat Bantuan & FAQ -->
          <router-link
            to="/faq"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/faq' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhQuestion :size="20" weight="bold" />
            <span class="flex-1">Pusat Bantuan & FAQ</span>
          </router-link>

          <!-- Kelola Hero Slider & Banner -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'banner' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && ($route.query.tab === 'banner' || $route.query.tab === 'hero-slider')) ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSlideshow :size="20" weight="bold" />
            <span class="flex-1">Banner & Hero Slider</span>
          </router-link>

          <!-- API Tester Playground -->
          <router-link
            to="/api-test"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path.startsWith('/api-test') ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhCode :size="20" weight="bold" />
            <span class="flex-1">API Tester & Docs</span>
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

    <!-- Top Bar Header for Admin -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs">
      <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <!-- Hamburger Button (Visible on both desktop & mobile) -->
        <button
          @click="isMobileOpen = !isMobileOpen"
          class="p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0 cursor-pointer"
          type="button"
          aria-label="Buka / Tutup Menu Navigasi"
          title="Buka / Tutup Menu"
        >
          <PhList :size="22" weight="bold" />
        </button>

        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:inline-block">Panel Administrator</span>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <!-- Dark Mode Toggle Button -->
        <DarkModeToggle />

        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <!-- Admin Profile Avatar Link -->
        <router-link
          to="/profil"
          class="flex items-center focus:outline-none cursor-pointer group"
          :title="auth.user?.nama || 'Profil Administrator'"
        >
          <div class="w-9 h-9 rounded-full bg-primary/10 text-primary border-2 border-primary/30 flex items-center justify-center font-bold text-xs uppercase overflow-hidden transition-all shadow-xs group-hover:scale-105">
            <img
              v-if="auth.user?.foto"
              :src="auth.user.foto"
              :alt="auth.user?.nama"
              class="w-full h-full object-cover"
            />
            <span v-else-if="auth.user?.nama" class="text-sm font-black">
              {{ auth.user.nama.charAt(0).toUpperCase() }}
            </span>
            <span v-else class="text-sm font-black">A</span>
          </div>
        </router-link>
      </div>
    </header>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import NotificationDropdown from './NotificationDropdown.vue'
import DarkModeToggle from './DarkModeToggle.vue'
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
  PhWallet,
  PhCode,
  PhQuestion,
  PhBed,
  PhCalendarCheck,
  PhUsers,
  PhUser
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()
const isMobileOpen = ref(false)

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
