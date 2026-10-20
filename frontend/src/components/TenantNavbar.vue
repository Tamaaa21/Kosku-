<template>
  <header class="fixed top-0 left-0 right-0 z-50 h-16 bg-surface-container-lowest/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-b border-surface-container-high/40">
    <div class="h-16 max-w-7xl mx-auto px-margin flex items-center justify-between gap-space-md">
      <!-- Logo Brand (Pencari / Publik) -->
      <router-link to="/" class="flex items-center gap-space-sm group">
        <img
          :src="logoUrl"
          alt="KosKu Logo"
          class="w-12 h-12 object-contain rounded-lg shadow-xs group-hover:scale-105 transition-transform"
        />
       
      </router-link>

      <!-- Desktop Navigation Menu -->
      <nav class="hidden md:flex items-center gap-space-lg">
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
          to="/favorit"
          class="text-sm font-medium transition-colors flex items-center gap-1.5"
          :class="($route.path === '/favorit' || $route.path === '/tersimpan') ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-on-surface'"
        >
          <PhHeart :size="16" :weight="($route.path === '/favorit' || $route.path === '/tersimpan' || favoritesStore.count > 0) ? 'fill' : 'bold'" :class="favoritesStore.count > 0 ? 'text-error' : ''" />
          <span>Favorit</span>
          <span
            v-if="favoritesStore.count > 0"
            class="px-1.5 py-0.5 text-[10px] font-bold bg-error text-white rounded-full leading-none"
          >
            {{ favoritesStore.count }}
          </span>
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
      </nav>

      <!-- Right Action / User Profile -->
      <div class="flex items-center gap-space-sm">
        <template v-if="auth.isAuthenticated">
          <!-- Notification Dropdown -->
          <NotificationDropdown />

          <!-- Role Badge -->
         

          <!-- Profile Link -->
          <router-link
            to="/profil"
            class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-surface-container transition group"
            title="Profil Akun"
          >
            <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 text-primary flex items-center justify-center font-bold text-xs uppercase overflow-hidden">
              <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
              <PhUser v-else :size="18" weight="bold" />
            </div>
            <span class="text-xs font-semibold text-on-surface hidden lg:inline max-w-[120px] truncate">
              {{ auth.user?.nama }}
            </span>
          </router-link>

          <!-- Logout Button -->
          <button
            @click="handleLogout"
            class="text-xs text-error hover:bg-error-container/40 p-2 rounded-lg font-medium transition flex items-center justify-center"
            title="Keluar"
          >
            <PhSignOut :size="18" weight="bold" />
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

        <!-- Mobile Menu Toggle Button -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition"
          type="button"
          aria-label="Toggle Menu"
        >
          <PhX v-if="mobileMenuOpen" :size="22" weight="bold" />
          <PhList v-else :size="22" weight="bold" />
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div
      v-if="mobileMenuOpen"
      class="md:hidden bg-surface-container-lowest border-b border-surface-container-high/40 px-margin py-3 shadow-lg flex flex-col gap-2"
    >
      <router-link
        to="/"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition"
        :class="$route.path === '/' ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container'"
      >
        Beranda
      </router-link>
      <router-link
        to="/kos"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition"
        :class="$route.path === '/kos' ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container'"
      >
        Cari Kos
      </router-link>
      <router-link
        to="/peta"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition"
        :class="$route.path === '/peta' ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container'"
      >
        Peta Lokasi
      </router-link>
      <template v-if="auth.isAuthenticated">
        <router-link
          to="/favorit"
          @click="mobileMenuOpen = false"
          class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center justify-between"
          :class="($route.path === '/favorit' || $route.path === '/tersimpan') ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container'"
        >
          <div class="flex items-center gap-2">
            <PhHeart :size="18" :weight="($route.path === '/favorit' || $route.path === '/tersimpan' || favoritesStore.count > 0) ? 'fill' : 'bold'" :class="favoritesStore.count > 0 ? 'text-error' : ''" />
            <span>Kos Tersimpan / Favorit</span>
          </div>
          <span
            v-if="favoritesStore.count > 0"
            class="px-2 py-0.5 text-[11px] font-bold bg-error text-white rounded-full"
          >
            {{ favoritesStore.count }}
          </span>
        </router-link>
        <router-link
          to="/pesanan"
          @click="mobileMenuOpen = false"
          class="px-3 py-2 rounded-lg text-sm font-medium transition"
          :class="$route.path.startsWith('/pesanan') ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container'"
        >
          Riwayat Pesanan
        </router-link>
        <router-link
          to="/profil"
          @click="mobileMenuOpen = false"
          class="px-3 py-2 rounded-lg text-sm font-medium transition"
          :class="$route.path === '/profil' ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container'"
        >
          Profil Saya
        </router-link>
        <button
          @click="handleLogout"
          class="text-left px-3 py-2 rounded-lg text-sm font-semibold text-error hover:bg-error-container/30 transition flex items-center gap-2"
        >
          <PhSignOut :size="18" weight="bold" />
          <span>Keluar</span>
        </button>
      </template>
      <template v-else>
        <div class="pt-2 border-t border-slate-100 flex gap-2">
          <router-link
            to="/login"
            @click="mobileMenuOpen = false"
            class="flex-1 text-center px-4 py-2 text-sm font-semibold text-primary bg-surface-container rounded-lg"
          >
            Masuk
          </router-link>
          <router-link
            to="/register"
            @click="mobileMenuOpen = false"
            class="flex-1 text-center px-4 py-2 text-sm font-semibold text-white bg-primary-container rounded-lg"
          >
            Daftar
          </router-link>
        </div>
      </template>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import NotificationDropdown from './NotificationDropdown.vue'
import logoUrl from '../assets/KosKu Housing Pin Logo.png'
import { PhUser, PhSignOut, PhX, PhList, PhHeart } from '@phosphor-icons/vue'

const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const router = useRouter()
const mobileMenuOpen = ref(false)

const handleLogout = async () => {
  mobileMenuOpen.value = false
  await auth.logout()
  router.push({ name: 'home' })
}
</script>
