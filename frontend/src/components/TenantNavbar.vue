<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 h-16 transition-all duration-300 ease-in-out"
    :class="
      isTransparent
        ? 'bg-transparent border-b border-transparent shadow-none'
        : 'bg-white/95 backdrop-blur-md shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-b border-slate-100'
    "
  >
    <div class="h-16 max-w-7xl mx-auto px-margin flex items-center justify-between gap-space-md">
      <!-- Logo Brand (KosKu with Pin Logo) -->
      <router-link to="/" class="flex items-center gap-2 group">
        <img
          :src="logoUrl"
          alt="KosKu Logo"
          class="w-9 h-9 object-contain rounded-lg shadow-xs group-hover:scale-105 transition-transform"
        />
        <div class="flex items-center text-xl font-extrabold tracking-tight transition-colors">
          <span :class="isTransparent ? 'text-white' : 'text-[#0B478B]'">Kos</span>
          <span :class="isTransparent ? 'text-amber-400' : 'text-amber-500'">Ku</span>
        </div>
      </router-link>

      <!-- Desktop Navigation Menu -->
      <nav class="hidden md:flex items-center gap-8">
        <router-link
          to="/"
          class="relative py-1 text-sm font-semibold transition-colors"
          :class="
            isTransparent
              ? 'text-white font-bold'
              : ($route.path === '/' ? 'text-primary font-bold' : 'text-slate-600 hover:text-slate-900')
          "
        >
          <span>Beranda</span>
          <span
            v-if="$route.path === '/'"
            class="absolute -bottom-1 left-0 right-0 h-0.5 rounded-full"
            :class="isTransparent ? 'bg-amber-400' : 'bg-primary'"
          ></span>
        </router-link>

        <router-link
          to="/kos"
          class="relative py-1 text-sm font-medium transition-colors"
          :class="
            isTransparent
              ? ($route.path === '/kos' ? 'text-white font-bold' : 'text-white/85 hover:text-white')
              : ($route.path === '/kos' ? 'text-primary font-bold' : 'text-slate-600 hover:text-slate-900')
          "
        >
          <span>Cari Kos</span>
          <span
            v-if="$route.path === '/kos'"
            class="absolute -bottom-1 left-0 right-0 h-0.5 rounded-full"
            :class="isTransparent ? 'bg-amber-400' : 'bg-primary'"
          ></span>
        </router-link>

        <router-link
          to="/peta"
          class="relative py-1 text-sm font-medium transition-colors"
          :class="
            isTransparent
              ? ($route.path === '/peta' ? 'text-white font-bold' : 'text-white/85 hover:text-white')
              : ($route.path === '/peta' ? 'text-primary font-bold' : 'text-slate-600 hover:text-slate-900')
          "
        >
          <span>Peta Lokasi</span>
          <span
            v-if="$route.path === '/peta'"
            class="absolute -bottom-1 left-0 right-0 h-0.5 rounded-full"
            :class="isTransparent ? 'bg-amber-400' : 'bg-primary'"
          ></span>
        </router-link>

     

    
        <router-link
          v-if="auth.isAuthenticated"
          to="/pesanan"
          class="relative py-1 text-sm font-medium transition-colors"
          :class="
            isTransparent
              ? ($route.path.startsWith('/pesanan') ? 'text-white font-bold' : 'text-white/85 hover:text-white')
              : ($route.path.startsWith('/pesanan') ? 'text-primary font-bold' : 'text-slate-600 hover:text-slate-900')
          "
        >
          <span>Riwayat Pesanan</span>
          <span
            v-if="$route.path.startsWith('/pesanan')"
            class="absolute -bottom-1 left-0 right-0 h-0.5 rounded-full"
            :class="isTransparent ? 'bg-amber-400' : 'bg-primary'"
          ></span>
        </router-link>
      </nav>

      <!-- Right Action / User Profile -->
      <div class="flex items-center gap-2 md:gap-3">
        <!-- Search Icon Button (Desktop Only) -->
      

        <!-- Wishlist / Favorite Icon Button (Desktop Only) -->
        <router-link
          to="/favorit"
          class="hidden md:flex relative w-9 h-9 rounded-full items-center justify-center transition cursor-pointer"
          :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-slate-700 hover:bg-slate-100'"
          title="Kos Favorit"
        >
          <PhHeart
            :size="19"
            :weight="favoritesStore.count > 0 ? 'fill' : 'bold'"
            :class="favoritesStore.count > 0 ? 'text-rose-500' : ''"
          />
          <span
            v-if="favoritesStore.count > 0"
            class="absolute -top-0.5 -right-0.5 px-1.5 py-0.2 min-w-[16px] text-[10px] font-bold bg-rose-500 text-white rounded-full leading-tight text-center"
          >
            {{ favoritesStore.count }}
          </span>
        </router-link>

        <template v-if="auth.isAuthenticated">
          <!-- Notification Dropdown (Desktop Only) -->
          <div class="hidden md:block">
            <NotificationDropdown />
          </div>

          <!-- Profile Link (Desktop Only) -->
          <router-link
            to="/profil"
            class="hidden md:flex items-center gap-2 pl-2 pr-1.5 py-1 rounded-full transition group"
            :class="isTransparent ? 'hover:bg-white/15' : 'hover:bg-slate-100'"
            title="Profil Akun"
          >
            <div
              class="w-8 h-8 rounded-full border flex items-center justify-center font-bold text-xs uppercase overflow-hidden transition-colors"
              :class="
                isTransparent
                  ? 'bg-white/20 border-white/40 text-white'
                  : 'bg-primary/10 border-primary/20 text-primary'
              "
            >
              <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
              <PhUser v-else :size="18" weight="bold" />
            </div>
            <span
              class="text-xs font-semibold hidden lg:inline max-w-[120px] truncate"
              :class="isTransparent ? 'text-white' : 'text-slate-800'"
            >
              {{ auth.user?.nama }}
            </span>
          </router-link>

          <!-- Desktop Logout Button (Desktop Only) -->
        
        </template>

        <template v-else>
          <!-- Masuk / Daftar Pill Button (Desktop Only) -->
          <router-link
            to="/login"
            class="hidden md:inline-flex items-center justify-center px-5 py-2 rounded-full bg-[#0B63B6] hover:bg-primary-container text-white text-xs sm:text-sm font-semibold transition-all shadow-sm active:scale-95 whitespace-nowrap"
          >
            Masuk / Daftar
          </router-link>
        </template>

        <!-- Mobile Menu Toggle Button (HANYA INI YANG MUNCUL DI MOBILE) -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 rounded-lg transition cursor-pointer"
          :class="isTransparent ? 'text-white hover:bg-white/15' : 'text-slate-700 hover:bg-slate-100'"
          type="button"
          aria-label="Toggle Menu"
        >
          <PhX v-if="mobileMenuOpen" :size="24" weight="bold" />
          <PhList v-else :size="24" weight="bold" />
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Menu (Always solid white for clarity) -->
    <div
      v-if="mobileMenuOpen"
      class="md:hidden bg-white border-b border-slate-200 px-margin py-3 shadow-xl flex flex-col gap-2"
    >
      <template v-if="auth.isAuthenticated">
        <!-- User Profile Quick Card in Mobile Menu -->
        <router-link
          to="/profil"
          @click="mobileMenuOpen = false"
          class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 mb-1 transition"
        >
          <div class="w-9 h-9 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xs uppercase overflow-hidden shrink-0">
            <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
            <PhUser v-else :size="18" weight="bold" />
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-bold text-slate-800 truncate">{{ auth.user?.nama }}</p>
            <p class="text-[11px] text-slate-400 truncate">{{ auth.user?.email || 'Kelola Profil' }}</p>
          </div>
        </router-link>
      </template>

      <router-link
        to="/"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition"
        :class="$route.path === '/' ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
      >
        Beranda
      </router-link>
      <router-link
        to="/kos"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition"
        :class="$route.path === '/kos' ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
      >
        Cari Kos
      </router-link>
      <router-link
        to="/peta"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition"
        :class="$route.path === '/peta' ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
      >
        Peta Lokasi
      </router-link>
      <router-link
        to="/favorit"
        @click="mobileMenuOpen = false"
        class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center justify-between"
        :class="($route.path === '/favorit' || $route.path === '/tersimpan') ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
      >
        <div class="flex items-center gap-2">
          <PhHeart :size="18" :weight="favoritesStore.count > 0 ? 'fill' : 'bold'" :class="favoritesStore.count > 0 ? 'text-rose-500' : ''" />
          <span>Favorit</span>
        </div>
        <span
          v-if="favoritesStore.count > 0"
          class="px-2 py-0.5 text-[11px] font-bold bg-rose-500 text-white rounded-full"
        >
          {{ favoritesStore.count }}
        </span>
      </router-link>

      <a
        href="#tentang"
        @click="mobileMenuOpen = false; scrollToSection('tentang')"
        class="px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition cursor-pointer"
      >
        Tentang
      </a>
      <a
        href="#bantuan"
        @click="mobileMenuOpen = false; scrollToSection('bantuan')"
        class="px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition cursor-pointer"
      >
        Bantuan
      </a>

      <template v-if="auth.isAuthenticated">
        <router-link
          to="/pesanan"
          @click="mobileMenuOpen = false"
          class="px-3 py-2 rounded-lg text-sm font-medium transition"
          :class="$route.path.startsWith('/pesanan') ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
        >
          Riwayat Pesanan
        </router-link>
        <router-link
          to="/profil"
          @click="mobileMenuOpen = false"
          class="px-3 py-2 rounded-lg text-sm font-medium transition"
          :class="$route.path === '/profil' ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
        >
          Profil Saya
        </router-link>
        <button
          @click="handleLogout"
          class="text-left px-3 py-2 rounded-lg text-sm font-semibold text-rose-600 hover:bg-rose-50 transition flex items-center gap-2 cursor-pointer"
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
            class="flex-1 text-center px-4 py-2 text-sm font-semibold text-[#0B63B6] bg-slate-100 rounded-full"
          >
            Masuk
          </router-link>
          <router-link
            to="/register"
            @click="mobileMenuOpen = false"
            class="flex-1 text-center px-4 py-2 text-sm font-semibold text-white bg-[#0B63B6] rounded-full"
          >
            Daftar
          </router-link>
        </div>
      </template>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import { useUiStore } from '../stores/ui'
import NotificationDropdown from './NotificationDropdown.vue'
import logoUrl from '../assets/KosKu Housing Pin Logo.png'
import {
  PhUser,
  PhSignOut,
  PhX,
  PhList,
  PhHeart,
  PhMagnifyingGlass
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const ui = useUiStore()
const router = useRouter()
const route = useRoute()
const mobileMenuOpen = ref(false)

// Scroll detection for transparent to solid white transition
const isScrolled = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  handleScroll()
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

// Only transparent at the top of the homepage where the dark hero background is present
const isTransparent = computed(() => {
  return route.path === '/' && !isScrolled.value
})

const scrollToSection = (sectionId) => {
  const el = document.getElementById(sectionId)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  } else {
    router.push({ path: '/', hash: `#${sectionId}` })
  }
}

const handleLogout = async () => {
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari akun KosKu?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  mobileMenuOpen.value = false
  await auth.logout()
  ui.success('Anda telah berhasil keluar.')
  router.push({ name: 'home' })
}
</script>
