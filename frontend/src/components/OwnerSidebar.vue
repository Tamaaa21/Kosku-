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
        v-if="isSidebarOpen"
        @click="closeSidebar"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 transition-opacity cursor-pointer"
      ></div>
    </transition>

    <!-- Sidebar Drawer (Left Fixed Drawer) -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 w-64 bg-slate-900 text-slate-100 z-50 flex flex-col justify-between shadow-2xl transition-transform duration-300 ease-in-out select-none border-r border-slate-800',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="flex flex-col flex-1 min-h-0">
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80">
          <router-link to="/dashboard/pemilik" class="flex items-center gap-2.5" @click="closeSidebar">
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

          <!-- Close Button -->
          <button
            @click="closeSidebar"
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
            Menu Pengelola
          </span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto">
          <!-- 1. Dashboard -->
          <router-link
            to="/dashboard/pemilik"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSquaresFour :size="20" weight="bold" />
            <span class="flex-1">Dashboard</span>
          </router-link>

          <!-- 2. Verifikasi Kepemilikan Properti -->
          <router-link
            to="/dashboard/pemilik/verifikasi"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/verifikasi' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhShieldCheck :size="20" weight="bold" />
            <span class="flex-1">Verifikasi Properti</span>
            <span
              v-if="!auth.isVerified"
              class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
              :class="auth.user?.status_verifikasi === 'pending' ? 'bg-blue-500/20 text-blue-400' : 'bg-amber-500/20 text-amber-400'"
            >
              {{ auth.user?.status_verifikasi === 'pending' ? 'Review' : 'Wajib' }}
            </span>
            <span v-else class="text-emerald-400">
              <PhCheckCircle :size="16" weight="fill" />
            </span>
          </router-link>

          <!-- 3. Data Kos -->
          <router-link
            :to="auth.isVerified ? '/dashboard/pemilik/kos' : '/dashboard/pemilik/verifikasi?locked=1'"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="[
              $route.path === '/dashboard/pemilik/kos' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
              !auth.isVerified ? 'opacity-70' : ''
            ]"
          >
            <PhBuildings :size="20" weight="bold" />
            <span class="flex-1">Data Kos</span>
            <PhLockSimple v-if="!auth.isVerified" :size="15" weight="bold" class="text-slate-500" />
          </router-link>

          <!-- 4. Kelola Kamar -->
          <router-link
            :to="auth.isVerified ? '/dashboard/pemilik/kamar' : '/dashboard/pemilik/verifikasi?locked=1'"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="[
              $route.path === '/dashboard/pemilik/kamar' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
              !auth.isVerified ? 'opacity-70' : ''
            ]"
          >
            <PhBed :size="20" weight="bold" />
            <span class="flex-1">Kelola Kamar</span>
            <PhLockSimple v-if="!auth.isVerified" :size="15" weight="bold" class="text-slate-500" />
          </router-link>

          <!-- 5. Permintaan Pemesanan -->
          <router-link
            :to="auth.isVerified ? '/dashboard/pemilik/pemesanan' : '/dashboard/pemilik/verifikasi?locked=1'"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="[
              $route.path === '/dashboard/pemilik/pemesanan' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
              !auth.isVerified ? 'opacity-70' : ''
            ]"
          >
            <PhCalendarCheck :size="20" weight="bold" />
            <span class="flex-1">Pemesanan Masuk</span>
            <PhLockSimple v-if="!auth.isVerified" :size="15" weight="bold" class="text-slate-500" />
          </router-link>

          <!-- 6. Profil Pemilik -->
          <router-link
            to="/profil"
            @click="closeSidebar"
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

    <!-- Top Bar Header for Pemilik matching ui-dasboard-pemilik.png -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs">
      <!-- Left side: Hamburger & Search Bar -->
      <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0 pr-3">
        <!-- Hamburger Button -->
        <button
          @click="toggleSidebar"
          class="p-2 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer shrink-0"
          type="button"
          aria-label="Menu Navigasi"
          title="Buka Menu"
        >
          <PhList :size="22" weight="bold" />
        </button>

        <!-- Search Bar with Pill Style -->
        <div class="relative w-full max-w-xs sm:max-w-sm md:max-w-md">
          <PhMagnifyingGlass
            :size="18"
            weight="bold"
            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
          />
          <input
            v-model="ui.dashboardSearchQuery"
            type="text"
            placeholder="Cari kos, kamar, atau nama penyewa..."
            class="w-full pl-10 pr-4 py-2 bg-slate-100/70 hover:bg-slate-100 focus:bg-white border border-slate-200/80 focus:border-blue-500 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
          />
        </div>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-2.5 sm:gap-3.5 shrink-0">
        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <!-- Date Range Filter Pill with Dropdown -->
        <div class="relative hidden sm:block" ref="dateRangeRef">
          <button
            @click="isDateRangeOpen = !isDateRangeOpen"
            class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 transition cursor-pointer"
            type="button"
          >
            <PhCalendarBlank :size="16" weight="bold" class="text-slate-500" />
            <span>{{ ui.selectedDateRange }}</span>
            <PhCaretDown :size="13" weight="bold" class="text-slate-400 transition-transform" :class="{ 'rotate-180': isDateRangeOpen }" />
          </button>

          <!-- Date Range Dropdown Menu -->
          <div
            v-if="isDateRangeOpen"
            class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs overflow-hidden"
          >
            <div class="px-3.5 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
              Pilih Rentang Waktu
            </div>
            <button
              v-for="range in dateRangeOptions"
              :key="range"
              @click="selectDateRange(range)"
              class="w-full text-left px-3.5 py-2 hover:bg-slate-50 flex items-center justify-between font-medium text-slate-700 transition cursor-pointer"
              :class="{ 'text-primary font-bold bg-primary/5': ui.selectedDateRange === range }"
            >
              <span>{{ range }}</span>
              <PhCheck v-if="ui.selectedDateRange === range" :size="14" weight="bold" class="text-primary" />
            </button>
          </div>
        </div>

        <!-- User Profile Pill with Dropdown -->
        <div class="relative" ref="profileRef">
          <button
            @click="isProfileMenuOpen = !isProfileMenuOpen"
            class="flex items-center gap-2 pl-1.5 pr-2.5 py-1 bg-white border border-slate-200/90 hover:border-slate-300 rounded-full text-xs font-bold text-slate-800 shadow-xs hover:bg-slate-50 transition cursor-pointer group"
            type="button"
          >
            <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs overflow-hidden shrink-0">
              <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
              <PhUser v-else :size="15" weight="bold" />
            </div>
            <span class="hidden md:inline max-w-[130px] truncate group-hover:text-primary transition">
              {{ auth.user?.nama || 'Pemilik Kos' }}
            </span>
            <PhCaretDown :size="13" weight="bold" class="text-slate-400 transition-transform" :class="{ 'rotate-180': isProfileMenuOpen }" />
          </button>

          <!-- Profile Dropdown Menu -->
          <div
            v-if="isProfileMenuOpen"
            class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs overflow-hidden animate-in fade-in zoom-in-95 duration-100"
          >
            <div class="px-4 py-2.5 border-b border-slate-100">
              <p class="font-bold text-slate-900 truncate">{{ auth.user?.nama || 'Pemilik Kos' }}</p>
              <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ auth.user?.email || 'pemilik@kos.test' }}</p>
            </div>

            <div class="py-1">
              <router-link
                to="/profil"
                @click="isProfileMenuOpen = false"
                class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 transition"
              >
                <PhUser :size="16" weight="bold" class="text-slate-400" />
                <span>Profil Saya</span>
              </router-link>
              <router-link
                to="/dashboard/pemilik/kos"
                @click="isProfileMenuOpen = false"
                class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 transition"
              >
                <PhBuildings :size="16" weight="bold" class="text-slate-400" />
                <span>Kelola Data Kos</span>
              </router-link>
              <router-link
                to="/dashboard/pemilik/kamar"
                @click="isProfileMenuOpen = false"
                class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 transition"
              >
                <PhBed :size="16" weight="bold" class="text-slate-400" />
                <span>Kelola Kamar</span>
              </router-link>
              <router-link
                to="/dashboard/pemilik/pemesanan"
                @click="isProfileMenuOpen = false"
                class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 transition"
              >
                <PhCalendarCheck :size="16" weight="bold" class="text-slate-400" />
                <span>Permintaan Sewa</span>
              </router-link>
            </div>

            <div class="border-t border-slate-100 pt-1">
              <button
                @click="handleLogout"
                class="w-full flex items-center gap-2.5 px-4 py-2 text-rose-600 hover:bg-rose-50 transition cursor-pointer font-semibold"
                type="button"
              >
                <PhSignOut :size="16" weight="bold" />
                <span>Keluar / Log Out</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
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
  PhCalendarBlank,
  PhUser,
  PhSignOut,
  PhList,
  PhMagnifyingGlass,
  PhCaretDown,
  PhCheck,
  PhShieldCheck,
  PhLockSimple,
  PhCheckCircle
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()

const isSidebarOpen = computed(() => ui.ownerSidebarOpen)
const isDateRangeOpen = ref(false)
const isProfileMenuOpen = ref(false)
const dateRangeRef = ref(null)
const profileRef = ref(null)

const dateRangeOptions = [
  '26 Okt 2024 - 26 Nov 2024',
  '12 Nov 2024 - 26 Nov 2024 (14 Hari)',
  '19 Nov 2024 - 26 Nov 2024 (7 Hari)',
  'Bulan Ini (Okt 2024)'
]

const toggleSidebar = () => {
  ui.toggleOwnerSidebar()
}

const closeSidebar = () => {
  ui.closeOwnerSidebar()
}

const selectDateRange = (range) => {
  ui.setDateRange(range)
  isDateRangeOpen.value = false
}

const handleClickOutside = (event) => {
  if (dateRangeRef.value && !dateRangeRef.value.contains(event.target)) {
    isDateRangeOpen.value = false
  }
  if (profileRef.value && !profileRef.value.contains(event.target)) {
    isProfileMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const handleLogout = async () => {
  isProfileMenuOpen.value = false
  closeSidebar()
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari akun?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  await auth.logout()
  ui.success('Anda telah berhasil keluar.')
  router.push({ name: 'home' })
}
</script>
