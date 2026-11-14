<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-between font-sans text-slate-800">
    <TenantNavbar />

    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-24">
      <div class="max-w-md w-full text-center">
        <!-- 404 Badge & Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-amber-50 text-[#E68202] border border-amber-200/60 shadow-sm mb-6">
          <PhCompass :size="40" weight="duotone" />
        </div>

        <p class="text-sm font-bold tracking-wider uppercase text-[#E68202] mb-2">Error 404</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">
          Halaman Tidak Ditemukan
        </h1>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8">
          Maaf, halaman yang Anda tuju tidak tersedia atau telah dipindahkan. Periksa kembali tautan yang Anda masukkan.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
          <router-link
            to="/"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-semibold text-sm shadow-sm hover:bg-primary-container transition active:scale-95"
          >
            <PhHouse :size="18" weight="bold" />
            Kembali ke Beranda
          </router-link>
          <router-link
            to="/kos"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition active:scale-95"
          >
            <PhMagnifyingGlass :size="18" weight="bold" />
            Jelajahi Kos
          </router-link>
        </div>
      </div>
    </main>

    <footer class="py-6 text-center text-xs text-slate-400 border-t border-slate-100 bg-white">
      © 2026 Kostara &middot; Platform Pencarian & Reservasi Kos Terintegrasi
    </footer>
  </div>
</template>

<script setup>
import TenantNavbar from '../components/TenantNavbar.vue'
import { PhCompass, PhHouse, PhMagnifyingGlass } from '@phosphor-icons/vue'
</script>
