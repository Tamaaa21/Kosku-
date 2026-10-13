<template>
  <div>
    <!-- Mobile Backdrop Overlay -->
    <div
      v-if="isMobileOpen"
      @click="isMobileOpen = false"
      class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 lg:hidden transition-opacity"
    ></div>

    <!-- Left Sidebar (Desktop Fixed & Mobile Drawer) -->
    <aside
      :class="[
        'fixed top-0 bottom-0 left-0 w-64 bg-slate-950 text-slate-100 z-50 flex flex-col justify-between shadow-2xl transition-transform duration-300 select-none border-r border-slate-800/80',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <div class="flex flex-col flex-1 min-h-0">
        <!-- Sidebar Brand Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800/80">
          <router-link to="/dashboard/admin" class="flex items-center gap-2.5" @click="isMobileOpen = false">
            <img
              :src="logoUrl"
              alt="KosKu Logo"
              class="w-9 h-9 object-contain rounded-xl bg-white/10 p-0.5 shadow-sm"
            />
            <div class="flex flex-col">
              <div class="flex items-center gap-1.5 leading-none">
                <span class="text-lg font-bold text-white tracking-tight">KosKu</span>
                <span class="text-[9px] uppercase font-bold text-white bg-primary px-1.5 py-0.5 rounded tracking-wider">
                  Admin
                </span>
              </div>
              <span class="text-[10px] text-slate-400 mt-0.5 font-medium">Panel Administrator</span>
            </div>
          </router-link>

          <!-- Mobile Close Button -->
          <button
            @click="isMobileOpen = false"
            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
            type="button"
          >
            <span class="material-symbols-outlined text-[20px]">close</span>
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
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="flex-1">Ringkasan & Analitik</span>
          </router-link>

          <!-- 2. Manajemen Pengguna (Terpadu: Mitra Pemilik & Pencari) -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pengguna' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && ($route.query.tab === 'pengguna' || $route.query.tab === 'pencari' || $route.query.tab === 'pemilik')) ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
            <span class="flex-1">Manajemen Pengguna</span>
          </router-link>

          <!-- 3. Data & Moderasi Kos -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'kos' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'kos') ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">home_work</span>
            <span class="flex-1">Data & Moderasi Kos</span>
          </router-link>

          <!-- 4. Audit Log Pemesanan -->
          <router-link
            :to="{ path: '/dashboard/admin', query: { tab: 'pemesanan' } }"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="($route.path === '/dashboard/admin' && $route.query.tab === 'pemesanan') ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            <span class="flex-1">Audit Log Pemesanan</span>
          </router-link>

          <!-- 5. Profil Admin -->
          <router-link
            to="/profil"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/profil' ? 'bg-primary-container text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">shield_person</span>
            <span class="flex-1">Profil Administrator</span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom User Profile Card -->
      <div class="p-3 border-t border-slate-800 bg-slate-900/60">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-800/60 transition">
          <div class="w-9 h-9 rounded-full bg-slate-800 text-primary border border-slate-700 flex items-center justify-center font-bold text-xs uppercase shrink-0 overflow-hidden">
            <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
            <span v-else class="material-symbols-outlined text-[18px]">shield_person</span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-bold text-white truncate leading-tight">{{ auth.user?.nama }}</p>
            <p class="text-[10px] text-slate-400 truncate leading-tight mt-0.5">System Admin</p>
          </div>
          <button
            @click="handleLogout"
            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition"
            title="Keluar / Logout"
            type="button"
          >
            <span class="material-symbols-outlined text-[18px]">logout</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Top Bar Header for Admin (Spans right of sidebar) -->
    <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface-container-lowest/95 backdrop-blur-md border-b border-surface-container-high/40 z-30 px-4 sm:px-6 flex items-center justify-between shadow-xs">
      <div class="flex items-center gap-3 min-w-0">
        <!-- Hamburger Button (Mobile Only) -->
        <button
          @click="isMobileOpen = true"
          class="lg:hidden p-2 rounded-lg text-on-surface hover:bg-surface-container transition shrink-0"
          type="button"
          aria-label="Buka Menu Sidebar"
        >
          <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>

        <!-- Title & Subtitle in Header -->
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="flex items-center gap-2 shrink-0">
            <h1 class="text-sm sm:text-base font-bold text-on-surface tracking-tight leading-tight">
              Dashboard Administrator
            </h1>
            <span class="bg-primary-fixed text-on-primary-fixed text-[10px] font-extrabold px-2 py-0.5 rounded-full shrink-0">
              System Admin
            </span>
          </div>
          <span class="hidden xl:inline text-slate-300 font-bold">&bull;</span>
          <p class="hidden xl:inline text-xs text-slate-500 truncate max-w-xl">
            Ringkasan eksekutif, kelola akun pencari, kemitraan pemilik kos, master fasilitas, dan audit transaksi.
          </p>
        </div>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-3 shrink-0">
        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <router-link
          to="/profil"
          class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-surface-container transition group"
          title="Profil Administrator"
        >
          <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs uppercase overflow-hidden">
            <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
            <span v-else class="material-symbols-outlined text-[18px]">manage_accounts</span>
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import NotificationDropdown from './NotificationDropdown.vue'
import logoUrl from '../assets/KosKu Housing Pin Logo.png'

const auth = useAuthStore()
const router = useRouter()
const isMobileOpen = ref(false)

const handleLogout = async () => {
  isMobileOpen.value = false
  await auth.logout()
  router.push({ name: 'home' })
}
</script>
