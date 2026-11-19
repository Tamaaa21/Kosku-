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
                <span class="text-lg font-extrabold tracking-tight">
                  <span class="text-[#017E91]">Kos</span><span class="text-[#E68202]">tara</span>
                </span>
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
            to="/dashboard/pemilik"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhSquaresFour :size="20" weight="bold" />
            <span class="flex-1">Dashboard</span>
          </router-link>

          <!-- MANAJEMEN PROPERTI -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Manajemen Properti
            </span>
          </div>

          <!-- Verifikasi Properti -->
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

          <!-- Data Kos -->
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

          <!-- Kelola Kamar -->
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

          <!-- OPERASIONAL -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Operasional
            </span>
          </div>

          <!-- Pemesanan -->
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
            <span class="flex-1">Pemesanan</span>
            <PhLockSimple v-if="!auth.isVerified" :size="15" weight="bold" class="text-slate-500" />
          </router-link>

          <!-- Daftar Penghuni -->
          <router-link
            :to="auth.isVerified ? '/dashboard/pemilik/penghuni' : '/dashboard/pemilik/verifikasi?locked=1'"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="[
              $route.path === '/dashboard/pemilik/penghuni' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
              !auth.isVerified ? 'opacity-70' : ''
            ]"
          >
            <PhUsers :size="20" weight="bold" />
            <span class="flex-1">Daftar Penghuni</span>
            <PhLockSimple v-if="!auth.isVerified" :size="15" weight="bold" class="text-slate-500" />
          </router-link>

          <!-- KEUANGAN -->
          <div class="px-3 pt-4 pb-1">
            <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">
              Keuangan
            </span>
          </div>

          <!-- Dompet & Saldo -->
          <router-link
            to="/dashboard/pemilik/dompet"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/dompet' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
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
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/profil' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhUser :size="20" weight="bold" />
            <span class="flex-1">Profil Saya</span>
          </router-link>

          <!-- Pusat Bantuan & FAQ -->
          <router-link
            to="/faq"
            @click="closeSidebar"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/faq' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <PhQuestion :size="20" weight="bold" />
            <span class="flex-1">Pusat Bantuan & FAQ</span>
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

    <!-- Top Bar Header for Pemilik -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs">
      <!-- Left side: Hamburger Button & Title -->
      <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <!-- Hamburger Button -->
        <button
          @click="toggleSidebar"
          class="p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0"
          type="button"
          aria-label="Menu Navigasi"
          title="Buka Menu"
        >
          <PhList :size="22" weight="bold" />
        </button>

        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:inline-block">Panel Mitra Pemilik</span>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <!-- Dark Mode Toggle Button -->
        <DarkModeToggle />

        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <!-- User Profile Avatar with Dropdown -->
        <div class="relative" ref="profileRef">
          <button
            @click="isProfileMenuOpen = !isProfileMenuOpen"
            class="flex items-center focus:outline-none cursor-pointer group"
            type="button"
            :title="auth.user?.nama || 'Profil Pemilik Kos'"
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
              <PhUser v-else :size="18" weight="bold" />
            </div>
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
  PhUser,
  PhSignOut,
  PhList,
  PhShieldCheck,
  PhLockSimple,
  PhCheckCircle,
  PhWallet,
  PhUsers,
  PhQuestion
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()

const isSidebarOpen = computed(() => ui.ownerSidebarOpen)
const isProfileMenuOpen = ref(false)
const profileRef = ref(null)

const toggleSidebar = () => {
  ui.toggleOwnerSidebar()
}

const closeSidebar = () => {
  ui.closeOwnerSidebar()
}

const handleClickOutside = (event) => {
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
