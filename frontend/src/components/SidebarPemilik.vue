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
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white shadow-sm">
              <span class="material-symbols-outlined text-[22px]">cottage</span>
            </div>
            <div class="flex flex-col">
              <div class="flex items-center gap-1.5 leading-none">
                <span class="text-lg font-bold text-white tracking-tight">KosKu</span>
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
            <span class="material-symbols-outlined text-[20px]">close</span>
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
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="flex-1">Dashboard</span>
          </router-link>

          <!-- 2. Data Kos -->
          <router-link
            to="/dashboard/pemilik/kos"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/kos' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">apartment</span>
            <span class="flex-1">Data Kos</span>
          </router-link>

          <!-- 3. Kelola Kamar -->
          <router-link
            to="/dashboard/pemilik/kamar"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/kamar' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">meeting_room</span>
            <span class="flex-1">Kelola Kamar</span>
          </router-link>

          <!-- 4. Permintaan Pemesanan -->
          <router-link
            to="/dashboard/pemilik/pemesanan"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/dashboard/pemilik/pemesanan' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">event_available</span>
            <span class="flex-1">Pemesanan Masuk</span>
          </router-link>

          <!-- 5. Profil Pemilik -->
          <router-link
            to="/profil"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition"
            :class="$route.path === '/profil' ? 'bg-primary text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <span class="material-symbols-outlined text-[20px]">person</span>
            <span class="flex-1">Profil Saya</span>
          </router-link>

          <div class="pt-3 pb-1 px-2">
            <div class="h-px bg-slate-800"></div>
          </div>

          <!-- 6. Pintasan Publik: Cari Kos -->
          <router-link
            to="/kos"
            @click="isMobileOpen = false"
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition"
          >
            <span class="material-symbols-outlined text-[20px]">public</span>
            <span class="flex-1">Lihat Katalog Publik</span>
          </router-link>
        </nav>
      </div>

      <!-- Bottom User Profile Card -->
      <div class="p-3 border-t border-slate-800 bg-slate-950/50">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-800/60 transition">
          <div class="w-9 h-9 rounded-full bg-primary/20 text-primary-fixed border border-primary/30 flex items-center justify-center font-bold text-xs uppercase shrink-0">
            <span class="material-symbols-outlined text-[18px]">storefront</span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-bold text-white truncate leading-tight">{{ auth.user?.nama }}</p>
            <p class="text-[10px] text-slate-400 truncate leading-tight mt-0.5">Pemilik Kos</p>
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
          <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>

        <!-- Breadcrumb / Location Indicator -->
        <div class="flex items-center gap-1.5 text-xs text-on-surface-variant font-medium">
          <span class="material-symbols-outlined text-[18px] text-primary">storefront</span>
          <span class="hidden sm:inline">Panel Kelola Properti</span>
          <span class="material-symbols-outlined text-[16px] text-outline">chevron_right</span>
          <span class="font-bold text-on-surface">Indonesia</span>
        </div>
      </div>

      <!-- Right Header Actions -->
      <div class="flex items-center gap-3">
        <!-- Notification Dropdown -->
        <NotificationDropdown />

        <router-link
          to="/dashboard/pemilik/kos"
          class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-container text-white hover:bg-primary transition shadow-xs"
        >
          <span class="material-symbols-outlined text-[16px]">add</span>
          <span>Tambah Kos</span>
        </router-link>

        <router-link
          to="/profil"
          class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-surface-container transition group"
          title="Profil Pemilik"
        >
          <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary flex items-center justify-center font-bold text-xs uppercase overflow-hidden">
            <span class="material-symbols-outlined text-[18px]">person</span>
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

const auth = useAuthStore()
const router = useRouter()
const isMobileOpen = ref(false)

const handleLogout = async () => {
  isMobileOpen.value = false
  await auth.logout()
  router.push({ name: 'home' })
}
</script>
