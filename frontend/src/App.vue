<template>
  <div class="min-h-screen flex flex-col bg-background text-on-surface antialiased font-sans">
    <Navbar />

    <main
      class="flex-grow transition-all duration-300"
      :class="[showMobileBottomNavbar ? 'pb-20 md:pb-0' : '']"
    >
      <router-view />
    </main>

    <!-- Footer (Hanya tampil di Desktop md ke atas, disembunyikan di Mobile) -->
    <footer
      v-if="$route.path !== '/peta' && !auth.isPemilik && !auth.isAdmin"
      class="hidden md:block bg-surface-container-lowest dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 py-8 text-center text-xs text-on-surface-variant dark:text-slate-400 mt-12 transition-colors"
    >
      <div class="max-w-7xl mx-auto px-margin flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <img :src="logoUrl" alt="Kostara Logo" class="w-6 h-6 object-contain" />
          <span class="font-bold text-sm">
            <span class="text-[#017E91]">Kos</span><span class="text-[#E68202]">tara</span>
            <span class="text-on-surface dark:text-slate-100 font-semibold ml-1">Indonesia</span>
          </span>
          <span class="text-slate-500 dark:text-slate-400">&mdash; Platform Pencarian & Sewa Kos Terpercaya di Seluruh Indonesia</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] text-secondary dark:text-slate-400">
          <span>&copy; {{ new Date().getFullYear() }} <span class="font-bold"><span class="text-[#017E91]">Kos</span><span class="text-[#E68202]">tara</span></span></span>
          <span>&bull;</span>
          <router-link to="/faq" class="hover:text-primary transition underline-offset-2 hover:underline">Pusat Bantuan & FAQ</router-link>
          <span>&bull;</span>
          <router-link to="/about" class="hover:text-primary transition underline-offset-2 hover:underline">Tentang Kami</router-link>
        </div>
      </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar (5 Menu: Beranda, Cari Kos, Peta, Pesanan, Profil) -->
    <MobileBottomNavbar v-if="showMobileBottomNavbar" />

    <!-- Floating Customer Service WhatsApp Support (Disembunyikan di Panel Admin) -->
    <FloatingWhatsAppSupport v-if="showFloatingSupport" />

    <!-- Global Back to Top Button -->
    <BackToTop />

    <!-- Global Session Timeout Warning & Auto-Logout Modal -->
    <SessionTimeoutModal />

    <!-- Global Accessible Toast & Confirm Modal -->
    <ToastContainer />
    <ConfirmModal />
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Capacitor } from '@capacitor/core'
import { App as CapApp } from '@capacitor/app'
import { StatusBar, Style } from '@capacitor/status-bar'
import Navbar from './components/Navbar.vue'
import MobileBottomNavbar from './components/MobileBottomNavbar.vue'
import FloatingWhatsAppSupport from './components/FloatingWhatsAppSupport.vue'
import BackToTop from './components/BackToTop.vue'
import SessionTimeoutModal from './components/SessionTimeoutModal.vue'
import ToastContainer from './components/ToastContainer.vue'
import ConfirmModal from './components/ConfirmModal.vue'
import logoUrl from './assets/logo.png'
import { useAuthStore } from './stores/auth'
import { useThemeStore } from './stores/theme'

const auth = useAuthStore()
const themeStore = useThemeStore()
const route = useRoute()
const router = useRouter()

const showFloatingSupport = computed(() => {
  if (auth.isAdmin) return false
  if (route.path.startsWith('/dashboard/admin') || route.name === 'admin-dashboard') return false
  return true
})

const showMobileBottomNavbar = computed(() => {
  if (auth.isAdmin || auth.isPemilik) return false
  const authRoutes = ['login', 'register', 'forgot-password', 'reset-password']
  if (authRoutes.includes(route.name)) return false
  if (route.path.startsWith('/dashboard/admin') || route.path.startsWith('/dashboard/pemilik')) return false
  return true
})

onMounted(async () => {
  themeStore.initTheme()

  if (auth.token) {
    await auth.fetchMe()
  }

  // Integrasi Native Capacitor (Android)
  if (Capacitor.isNativePlatform()) {
    // 1. Pewarnaan Status Bar Android (Tema Kostara)
    try {
      await StatusBar.setStyle({ style: Style.Dark })
      await StatusBar.setBackgroundColor({ color: '#017E91' })
    } catch (_) {}

    // 2. Penanganan Tombol Back Fisik Android
    try {
      CapApp.addListener('backButton', ({ canGoBack }) => {
        // Jika sedang di halaman utama atau rute root dan tidak bisa mundur lagi
        if (route.path === '/' || !canGoBack) {
          CapApp.exitApp()
        } else {
          router.back()
        }
      })
    } catch (_) {}
  }
})
</script>
