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
      <!-- Logo Brand (Kostara) -->
      <router-link to="/" class="flex items-center gap-2 group">
        <img
          :src="logoUrl"
          alt="Kostara Logo"
          class="w-9 h-9 object-contain group-hover:scale-105 transition-transform"
        />
        <div class="flex items-center text-xl font-extrabold tracking-tight transition-colors">
          <span :class="isTransparent ? 'text-white' : 'text-[#0B478B]'">Kos</span>
          <span :class="isTransparent ? 'text-amber-400' : 'text-amber-500'">tara</span>
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
      

        <template v-if="auth.isAuthenticated">
          <!-- Notification Dropdown -->
          <NotificationDropdown />

          <!-- Profile Avatar Dropdown (Desktop Only) -->
          <div ref="profileDropdownRef" class="relative hidden md:block">
            <!-- Avatar Button (Avatar only, no user name) -->
            <button
              @click="isProfileDropdownOpen = !isProfileDropdownOpen"
              type="button"
              class="flex items-center justify-center p-0.5 rounded-full transition-all cursor-pointer focus:outline-none"
              :class="
                isTransparent
                  ? 'hover:ring-2 hover:ring-white/40 active:scale-95'
                  : 'hover:ring-2 hover:ring-primary/30 active:scale-95'
              "
              aria-label="Menu Profil Akun"
              title="Akun Saya"
            >
              <div
                class="w-9 h-9 rounded-full border-2 flex items-center justify-center font-bold text-xs uppercase overflow-hidden transition-all shadow-xs"
                :class="
                  isTransparent
                    ? 'bg-white/20 border-white/60 text-white backdrop-blur-xs'
                    : 'bg-primary/10 border-primary/30 text-primary'
                "
              >
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

            <!-- Dropdown Menu Card -->
            <transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <div
                v-if="isProfileDropdownOpen"
                class="absolute right-0 mt-2.5 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden text-left"
              >
                <!-- Mini User Info Header -->
                <div class="px-4 py-3 bg-gradient-to-br from-slate-50 to-blue-50/40 border-b border-slate-100 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-sm uppercase overflow-hidden shrink-0 shadow-2xs">
                    <img v-if="auth.user?.foto" :src="auth.user.foto" :alt="auth.user?.nama" class="w-full h-full object-cover" />
                    <span v-else-if="auth.user?.nama">{{ auth.user.nama.charAt(0).toUpperCase() }}</span>
                    <PhUser v-else :size="20" weight="bold" />
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-900 truncate leading-tight">{{ auth.user?.nama }}</p>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ auth.user?.email }}</p>
                    <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100/80 text-blue-700">
                      Pencari Kos
                    </span>
                  </div>
                </div>

                <!-- Menu Items -->
                <div class="py-1">
                  <!-- 1. Profil Akun -->
                  <router-link
                    to="/profil"
                    @click="isProfileDropdownOpen = false"
                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition group"
                  >
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                      <PhUser :size="16" weight="bold" />
                    </div>
                    <span>Profil Akun</span>
                  </router-link>

                  <!-- 2. Riwayat Pesanan -->
                  <router-link
                    to="/pesanan"
                    @click="isProfileDropdownOpen = false"
                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition group"
                  >
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                      <PhReceipt :size="16" weight="bold" />
                    </div>
                    <span>Riwayat Pesanan</span>
                  </router-link>

                  <!-- 3. Kos Favorit -->
                  <router-link
                    to="/favorit"
                    @click="isProfileDropdownOpen = false"
                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition group"
                  >
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-rose-50 group-hover:text-rose-600 transition-colors">
                      <PhHeart :size="16" weight="bold" />
                    </div>
                    <span class="flex-1">Kos Favorit</span>
                    <span
                      v-if="favoritesStore.count > 0"
                      class="px-1.5 py-0.2 text-[10px] font-bold bg-rose-500 text-white rounded-full"
                    >
                      {{ favoritesStore.count }}
                    </span>
                  </router-link>
                </div>

                <!-- Divider -->
                <div class="border-t border-slate-100 my-1"></div>

                <!-- 4. Logout Button -->
                <div class="px-1.5 py-0.5">
                  <button
                    @click="handleProfileLogout"
                    type="button"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                  >
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                      <PhSignOut :size="16" weight="bold" />
                    </div>
                    <span>Keluar (Logout)</span>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        
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
          to="/favorit"
          @click="mobileMenuOpen = false"
          class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center justify-between"
          :class="($route.path === '/favorit' || $route.path === '/tersimpan') ? 'bg-primary text-white font-bold' : 'text-slate-700 hover:bg-slate-100'"
        >
          <div class="flex items-center gap-2">
            <PhHeart :size="18" :weight="favoritesStore.count > 0 ? 'fill' : 'bold'" :class="favoritesStore.count > 0 ? 'text-rose-500' : ''" />
            <span>Kos Favorit</span>
          </div>
          <span
            v-if="favoritesStore.count > 0"
            class="px-2 py-0.5 text-[11px] font-bold bg-rose-500 text-white rounded-full"
          >
            {{ favoritesStore.count }}
          </span>
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
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import { useUiStore } from '../stores/ui'
import NotificationDropdown from './NotificationDropdown.vue'
import logoUrl from '../assets/logo.png'
import {
  PhUser,
  PhReceipt,
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

const isProfileDropdownOpen = ref(false)
const profileDropdownRef = ref(null)

const handleClickOutside = (e) => {
  if (profileDropdownRef.value && !profileDropdownRef.value.contains(e.target)) {
    isProfileDropdownOpen.value = false
  }
}

watch(
  () => route.path,
  () => {
    isProfileDropdownOpen.value = false
    mobileMenuOpen.value = false
  }
)

// Scroll detection for transparent to solid white transition
const isScrolled = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  handleScroll()
  window.addEventListener('scroll', handleScroll, { passive: true })
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', handleClickOutside)
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

const handleProfileLogout = () => {
  isProfileDropdownOpen.value = false
  handleLogout()
}

const handleLogout = async () => {
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari akun Kostara?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  mobileMenuOpen.value = false
  isProfileDropdownOpen.value = false
  await auth.logout()
  ui.success('Anda telah berhasil keluar.')
  router.push({ name: 'home' })
}
</script>
