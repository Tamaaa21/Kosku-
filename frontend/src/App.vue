<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased font-sans">
    <Navbar />

    <main class="flex-grow">
      <router-view />
    </main>

    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="font-extrabold text-slate-800">KosHub</span>
          <span>&mdash; Sistem Informasi Pencarian Kos Terintegrasi Berbasis REST API</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] text-slate-400">
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
