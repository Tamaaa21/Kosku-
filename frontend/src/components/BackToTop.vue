<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4 scale-75"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-4 scale-75"
  >
    <div
      v-show="isVisible"
      :class="[
        'fixed z-40 select-none transition-all duration-300',
        hasFloatingSupport ? 'bottom-24 right-7 sm:bottom-26 sm:right-8' : 'bottom-6 right-6'
      ]"
    >
      <button
        @click="scrollToTop"
        type="button"
        class="group relative flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white/95 backdrop-blur-md text-[#017E91] border border-slate-200/90 shadow-md hover:shadow-xl hover:bg-[#017E91] hover:text-white hover:border-[#017E91] transition-all duration-300 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#017E91]/40"
        aria-label="Kembali ke atas halaman"
        title="Kembali ke atas"
      >
        <PhCaretUp :size="20" weight="bold" class="group-hover:-translate-y-0.5 transition-transform duration-200" />

        <!-- Tooltip on hover (desktop) -->
        <span class="absolute right-full mr-2.5 px-2.5 py-1 rounded-lg bg-slate-900/90 text-white text-[10px] font-semibold tracking-wide whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-md hidden sm:block">
          Kembali ke Atas
        </span>
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { PhCaretUp } from '@phosphor-icons/vue'

const auth = useAuthStore()
const route = useRoute()

const isVisible = ref(false)

const hasFloatingSupport = computed(() => {
  if (auth.isAdmin) return false
  if (route.path?.startsWith('/dashboard/admin') || route.name === 'admin-dashboard') return false
  return true
})

const handleScroll = () => {
  isVisible.value = window.scrollY > 320
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
