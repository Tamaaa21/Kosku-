<template>
  <div class="min-h-screen flex flex-col bg-background text-on-surface antialiased font-sans">
    <Navbar />

    <main class="flex-grow">
      <router-view />
    </main>

    <!-- Footer (Hidden on full-screen map view) -->
    <footer
      v-if="$route.path !== '/peta'"
      class="bg-surface-container-lowest border-t border-slate-200/80 py-8 text-center text-xs text-on-surface-variant mt-12"
    >
      <div class="max-w-7xl mx-auto px-margin flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="font-bold text-primary text-sm">KosKu Tegal</span>
          <span>&mdash; Platform Pencarian & Sewa Kos Terintegrasi Berbasis REST API</span>
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
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import Navbar from './components/Navbar.vue'
import { useAuthStore } from './stores/auth'

const auth = useAuthStore()

onMounted(async () => {
  if (auth.token) {
    await auth.fetchMe()
  }
})
</script>
