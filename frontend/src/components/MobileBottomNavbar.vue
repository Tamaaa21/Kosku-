<template>
  <nav
    class="fixed bottom-0 left-0 right-0 z-[1040] md:hidden bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] pb-[max(0.35rem,env(safe-area-inset-bottom))] pt-1"
    aria-label="Navigasi Bawah Mobile"
  >
    <div class="grid grid-cols-5 h-14 items-center max-w-lg mx-auto px-1">
      <!-- 1. Beranda -->
      <router-link
        to="/"
        class="flex flex-col items-center justify-center h-full py-1 text-center select-none transition-all active:scale-95 group relative"
        :class="isHomeActive ? 'text-[#017E91]' : 'text-slate-400 hover:text-slate-600'"
      >
        <span
          v-if="isHomeActive"
          class="absolute top-0 w-8 h-0.5 rounded-full bg-[#017E91] animate-in fade-in duration-200"
        ></span>
        <PhHouse :size="22" :weight="isHomeActive ? 'fill' : 'regular'" class="transition-transform group-hover:scale-105" />
        <span class="text-[10px] tracking-tight mt-0.5 leading-none" :class="isHomeActive ? 'font-bold' : 'font-medium'">
          Beranda
        </span>
      </router-link>

      <!-- 2. Cari Kos -->
      <router-link
        to="/kos"
        class="flex flex-col items-center justify-center h-full py-1 text-center select-none transition-all active:scale-95 group relative"
        :class="isKosActive ? 'text-[#017E91]' : 'text-slate-400 hover:text-slate-600'"
      >
        <span
          v-if="isKosActive"
          class="absolute top-0 w-8 h-0.5 rounded-full bg-[#017E91] animate-in fade-in duration-200"
        ></span>
        <PhMagnifyingGlass :size="22" :weight="isKosActive ? 'bold' : 'regular'" class="transition-transform group-hover:scale-105" />
        <span class="text-[10px] tracking-tight mt-0.5 leading-none" :class="isKosActive ? 'font-bold' : 'font-medium'">
          Cari Kos
        </span>
      </router-link>

      <!-- 3. Peta Lokasi -->
      <router-link
        to="/peta"
        class="flex flex-col items-center justify-center h-full py-1 text-center select-none transition-all active:scale-95 group relative"
        :class="isPetaActive ? 'text-[#017E91]' : 'text-slate-400 hover:text-slate-600'"
      >
        <span
          v-if="isPetaActive"
          class="absolute top-0 w-8 h-0.5 rounded-full bg-[#017E91] animate-in fade-in duration-200"
        ></span>
        <PhMapTrifold :size="22" :weight="isPetaActive ? 'fill' : 'regular'" class="transition-transform group-hover:scale-105" />
        <span class="text-[10px] tracking-tight mt-0.5 leading-none" :class="isPetaActive ? 'font-bold' : 'font-medium'">
          Peta
        </span>
      </router-link>

      <!-- 4. Pesanan -->
      <router-link
        :to="auth.isAuthenticated ? '/pesanan' : '/login?redirect=/pesanan'"
        class="flex flex-col items-center justify-center h-full py-1 text-center select-none transition-all active:scale-95 group relative"
        :class="isPesananActive ? 'text-[#017E91]' : 'text-slate-400 hover:text-slate-600'"
      >
        <span
          v-if="isPesananActive"
          class="absolute top-0 w-8 h-0.5 rounded-full bg-[#017E91] animate-in fade-in duration-200"
        ></span>
        <div class="relative inline-flex items-center justify-center">
          <PhReceipt :size="22" :weight="isPesananActive ? 'fill' : 'regular'" class="transition-transform group-hover:scale-105" />
        </div>
        <span class="text-[10px] tracking-tight mt-0.5 leading-none" :class="isPesananActive ? 'font-bold' : 'font-medium'">
          Pesanan
        </span>
      </router-link>

      <!-- 5. Profil Akun -->
      <router-link
        :to="auth.isAuthenticated ? '/profil' : '/login'"
        class="flex flex-col items-center justify-center h-full py-1 text-center select-none transition-all active:scale-95 group relative"
        :class="isProfilActive ? 'text-[#017E91]' : 'text-slate-400 hover:text-slate-600'"
      >
        <span
          v-if="isProfilActive"
          class="absolute top-0 w-8 h-0.5 rounded-full bg-[#017E91] animate-in fade-in duration-200"
        ></span>
        <div class="relative inline-flex items-center justify-center">
          <div
            v-if="auth.isAuthenticated && auth.user?.foto"
            class="w-5 h-5 rounded-full overflow-hidden border"
            :class="isProfilActive ? 'border-[#017E91] ring-1 ring-[#017E91]' : 'border-slate-300'"
          >
            <img :src="auth.user.foto" :alt="auth.user.nama" class="w-full h-full object-cover" />
          </div>
          <PhUser v-else :size="22" :weight="isProfilActive ? 'fill' : 'regular'" class="transition-transform group-hover:scale-105" />
        </div>
        <span class="text-[10px] tracking-tight mt-0.5 leading-none" :class="isProfilActive ? 'font-bold' : 'font-medium'">
          Profil
        </span>
      </router-link>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import {
  PhHouse,
  PhMagnifyingGlass,
  PhMapTrifold,
  PhReceipt,
  PhUser,
} from '@phosphor-icons/vue'

const route = useRoute()
const auth = useAuthStore()

const isHomeActive = computed(() => route.path === '/')
const isKosActive = computed(() => route.path === '/kos' || route.name === 'daftar-kos' || route.name === 'kos-detail')
const isPetaActive = computed(() => route.path === '/peta' || route.name === 'peta-lokasi')
const isPesananActive = computed(() => route.path.startsWith('/pesanan') || route.name === 'riwayat-pemesanan')
const isProfilActive = computed(() => route.path.startsWith('/profil') || route.name?.startsWith('profil'))
</script>
