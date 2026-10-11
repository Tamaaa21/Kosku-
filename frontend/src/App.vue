<template>
  <div class="min-h-screen flex flex-col bg-background text-on-surface antialiased font-sans">
    <Navbar />

    <main
      class="flex-grow transition-all duration-300"
      :class="{ 'lg:pl-64': auth.isPemilik || auth.isAdmin }"
    >
      <router-view />
    </main>

    <!-- Footer (Hidden on full-screen map view or when in owner/admin sidebar mode) -->
    <footer
      v-if="$route.path !== '/peta' && !auth.isPemilik && !auth.isAdmin"
      class="bg-surface-container-lowest border-t border-slate-200/80 py-8 text-center text-xs text-on-surface-variant mt-12"
    >
      <div class="max-w-7xl mx-auto px-margin flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="font-bold text-primary text-sm">KosKu Indonesia</span>
          <span>&mdash; Platform Pencarian & Sewa Kos Terintegrasi di Seluruh Indonesia</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] text-secondary">
          <span>Laravel 11 REST API</span>
          <span>&bull;</span>
          <span>Vue 3 SPA & Leaflet</span>
          <span>&bull;</span>
          <span>OpenStreetMap Nominatim</span>
        </div>
      </div>
    </footer>

    <!-- Floating Customer Service WhatsApp Support (Disembunyikan di Panel Admin) -->
    <FloatingWhatsAppSupport v-if="showFloatingSupport" />
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/Navbar.vue'
import FloatingWhatsAppSupport from './components/FloatingWhatsAppSupport.vue'
import { useAuthStore } from './stores/auth'

const auth = useAuthStore()
const route = useRoute()

const showFloatingSupport = computed(() => {
  if (auth.isAdmin) return false
  if (route.path.startsWith('/dashboard/admin') || route.name === 'admin-dashboard') return false
  return true
})

onMounted(async () => {
  if (auth.token) {
    await auth.fetchMe()
  }
})
</script>
