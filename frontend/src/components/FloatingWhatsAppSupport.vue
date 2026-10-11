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
        class="absolute bottom-16 right-0 w-80 sm:w-88 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-4 text-white">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="relative w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white border border-white/30 shrink-0">
                <span class="material-symbols-outlined text-[24px]">support_agent</span>
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-400 ring-2 ring-emerald-700"></span>
              </div>
              <div>
                <h3 class="text-sm font-bold leading-tight">Customer Service KosKu</h3>
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
              <span class="material-symbols-outlined text-[20px]">close</span>
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
              <span class="material-symbols-outlined text-[18px]">{{ item.icon }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-xs font-bold text-on-surface group-hover:text-emerald-700 transition-colors">
                {{ item.title }}
              </h4>
              <p class="text-[11px] text-on-surface-variant line-clamp-1 mt-0.5">
                {{ item.desc }}
              </p>
            </div>
            <span class="material-symbols-outlined text-[16px] text-slate-300 group-hover:text-emerald-600 transition-transform group-hover:translate-x-0.5 mt-1">
              chevron_right
            </span>
          </button>
        </div>

        <!-- Footer Direct Action -->
        <div class="p-3 bg-white border-t border-slate-100">
          <button
            @click="handleSelectTopic('umum')"
            type="button"
            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm active:scale-[0.99]"
          >
            <span class="material-symbols-outlined text-[18px]">chat</span>
            <span>Mulai Chat via WhatsApp</span>
          </button>
          <span class="text-[10px] text-slate-400 text-center block mt-1.5 font-medium">
            Respon cepat Senin - Minggu (08.00 - 22.00 WIB)
          </span>
        </div>
      </div>
    </transition>

    <!-- Floating Trigger Button -->
    <div class="relative flex items-center justify-end">
      <!-- Tooltip Pill -->
      <transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 translate-x-2"
        enter-to-class="opacity-100 translate-x-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-x-0"
        leave-to-class="opacity-0 translate-x-2"
      >
        <div
          v-if="!isOpen && showPill"
          @click="isOpen = true"
          class="hidden sm:flex items-center gap-2 mr-3 px-3 py-1.5 bg-white text-on-surface font-semibold text-xs rounded-full shadow-lg border border-slate-100 cursor-pointer hover:bg-slate-50 transition"
        >
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Butuh Bantuan? <strong>Chat CS</strong></span>
        </div>
      </transition>

      <!-- Main Trigger Button -->
      <button
        @click="toggleOpen"
        type="button"
        class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-[#25D366] hover:bg-[#20BA5A] text-white shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-emerald-400/40 active:scale-95 group"
        :aria-label="isOpen ? 'Tutup bantuan CS' : 'Buka customer service WhatsApp'"
        :title="isOpen ? 'Tutup' : 'Customer Service WhatsApp'"
      >
        <!-- WhatsApp Icon SVG -->
        <svg
          v-if="!isOpen"
          class="w-7 h-7 sm:w-8 sm:h-8 fill-current drop-shadow-xs transition-transform group-hover:scale-110"
          viewBox="0 0 24 24"
        >
          <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.699c.974.551 1.838.814 2.796.814 3.181 0 5.767-2.586 5.768-5.766.001-3.18-2.585-5.766-5.768-5.766zm6.657 5.765c-.002 3.67-2.986 6.654-6.658 6.654-.997 0-2.025-.262-2.915-.758l-3.328.873.888-3.245c-.569-.974-.869-2.046-.867-3.524.002-3.67 2.986-6.654 6.658-6.654 3.672 0 6.659 2.984 6.662 6.654z"/>
        </svg>

        <span v-else class="material-symbols-outlined text-[28px]">close</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { openWhatsAppChat, buildCustomerServiceTemplate } from '../utils/whatsapp'

const auth = useAuthStore()
const route = useRoute()
const isOpen = ref(false)
const showPill = ref(true)
const widgetRef = ref(null)

const isHidden = computed(() => {
  return auth.isAdmin || route?.path?.startsWith('/dashboard/admin') || route?.name === 'admin-dashboard'
})

// Default Official KosKu Customer Service WhatsApp number
const csPhoneNumber = '081234567890'

const supportTopics = [
  {
    id: 'pencari',
    title: 'Bantuan Pencari Kos',
    desc: 'Tanya ketersediaan, booking kamar, & survei',
    icon: 'search',
    iconBg: 'bg-emerald-100 text-emerald-700',
  },
  {
    id: 'pemilik',
    title: 'Mitra Pemilik Kos',
    desc: 'Bantuan tambah properti, kamar, & pesanan',
    icon: 'storefront',
    iconBg: 'bg-sky-100 text-sky-700',
  },
  {
    id: 'kendala',
    title: 'Lapor Kendala / Pertanyaan',
    desc: 'Keluhan teknis, akun, & pertanyaan umum',
    icon: 'report_problem',
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
  if (widgetRef.value && !widgetRef.value.contains(e.target)) {
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
