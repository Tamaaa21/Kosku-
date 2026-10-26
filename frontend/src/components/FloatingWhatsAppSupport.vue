<template>
  <div v-if="!isHidden" class="fixed bottom-6 right-6 z-40 select-none" ref="widgetRef">
    <!-- Popover Card -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-3 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-3 scale-95"
    >
      <div
        v-if="isOpen"
        @click.stop
        class="absolute bottom-16 sm:bottom-20 right-0 w-[calc(100vw-3rem)] max-w-sm sm:w-88 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 p-4 text-white">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="relative w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white border border-white/30 shrink-0 overflow-hidden">
                <img :src="csLogo" alt="Customer Service" class="w-full h-full object-cover object-center" />
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-emerald-700"></span>
              </div>
              <div>
                <h3 class="text-sm font-bold leading-tight">Customer Service Kostara</h3>
                <div class="flex items-center gap-1.5 text-[11px] text-emerald-100 mt-0.5 font-medium">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                  <span>Online &bull; Siap Membantu</span>
                </div>
              </div>
            </div>

            <button
              @click="isOpen = false"
              type="button"
              class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition"
              title="Tutup"
            >
              <PhX :size="20" weight="bold" />
            </button>
          </div>
          <p class="text-[11px] text-emerald-100/90 mt-2.5 leading-snug">
            Butuh bantuan terkait pencarian kos, kelola properti, atau kendala transaksi? Tim kami siap melayani Anda.
          </p>
        </div>

        <!-- Options Menu -->
        <div class="p-3 space-y-1.5 bg-slate-50/70">
          <button
            v-for="item in supportTopics"
            :key="item.id"
            @click="handleSelectTopic(item.id)"
            type="button"
            class="w-full text-left p-3 rounded-xl bg-white hover:bg-emerald-50/60 border border-slate-100 hover:border-emerald-200 transition group flex items-start gap-3 shadow-xs"
          >
            <div
              :class="[
                'w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors',
                item.iconBg
              ]"
            >
              <component :is="item.iconComponent" :size="18" weight="bold" />
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-xs font-bold text-on-surface group-hover:text-emerald-700 transition-colors">
                {{ item.title }}
              </h4>
              <p class="text-[11px] text-on-surface-variant line-clamp-1 mt-0.5">
                {{ item.desc }}
              </p>
            </div>
            <PhCaretRight :size="16" weight="bold" class="text-slate-300 group-hover:text-emerald-600 transition-transform group-hover:translate-x-0.5 mt-1" />
          </button>
        </div>

        <!-- Footer Direct Action -->
        <div class="p-3 bg-white border-t border-slate-100">
          <button
            @click="handleSelectTopic('umum')"
            type="button"
            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm active:scale-[0.99]"
          >
            <PhWhatsappLogo :size="18" weight="fill" />
            <span>Mulai Chat via WhatsApp</span>
          </button>
          <span class="text-[10px] text-slate-400 text-center block mt-1.5 font-medium">
            Respon cepat Senin - Minggu (08.00 - 22.00 WIB)
          </span>
        </div>
      </div>
    </transition>

    <!-- Floating Trigger Button (Langsung klik logo untuk pop up) -->
    <div class="relative flex items-center justify-end">
      <!-- Main Trigger Button -->
      <button
        @click.stop="toggleOpen"
        type="button"
        class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-white shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-emerald-400/40 active:scale-95 group overflow-hidden relative border-2 border-emerald-500/20 cursor-pointer"
        :aria-label="isOpen ? 'Tutup bantuan CS' : 'Buka customer service WhatsApp'"
        :title="isOpen ? 'Tutup' : 'Customer Service WhatsApp'"
      >
        <template v-if="!isOpen">
          <img
            :src="csLogo"
            alt="Customer Service"
            class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform"
          />
          <span class="absolute top-0.5 right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
        </template>
        <PhX v-else :size="24" weight="bold" class="text-slate-700" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import csLogo from '../assets/image-cs.png'
import { openWhatsAppChat, buildCustomerServiceTemplate } from '../utils/whatsapp'
import {
  PhHeadset,
  PhX,
  PhMagnifyingGlass,
  PhBuildings,
  PhWarningCircle,
  PhCaretRight,
  PhWhatsappLogo
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const route = useRoute()
const isOpen = ref(false)
const widgetRef = ref(null)

const isHidden = computed(() => {
  return auth.isAdmin || route?.path?.startsWith('/dashboard/admin') || route?.name === 'admin-dashboard'
})

// Default Official Kostara Customer Service WhatsApp number
const csPhoneNumber = '081234567890'

const supportTopics = [
  {
    id: 'pencari',
    title: 'Bantuan Pencari Kos',
    desc: 'Tanya ketersediaan, booking kamar, & survei',
    iconComponent: PhMagnifyingGlass,
    iconBg: 'bg-emerald-100 text-emerald-700',
  },
  {
    id: 'pemilik',
    title: 'Mitra Pemilik Kos',
    desc: 'Bantuan tambah properti, kamar, & pesanan',
    iconComponent: PhBuildings,
    iconBg: 'bg-primary-fixed text-primary',
  },
  {
    id: 'kendala',
    title: 'Lapor Kendala / Pertanyaan',
    desc: 'Keluhan teknis, akun, & pertanyaan umum',
    iconComponent: PhWarningCircle,
    iconBg: 'bg-amber-100 text-amber-700',
  },
]

const toggleOpen = () => {
  isOpen.value = !isOpen.value
}

const handleSelectTopic = (topicId) => {
  const userName = auth.user?.nama || ''
  const message = buildCustomerServiceTemplate(topicId, userName)
  openWhatsAppChat(csPhoneNumber, message)
  isOpen.value = false
}

// Close when clicking outside
const handleClickOutside = (e) => {
  if (!isOpen.value) return
  if (!widgetRef.value) return
  const path = e.composedPath ? e.composedPath() : []
  if (path.includes(widgetRef.value)) return
  if (e.target && !document.contains(e.target)) return
  if (!widgetRef.value.contains(e.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
