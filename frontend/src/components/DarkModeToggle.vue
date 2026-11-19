<template>
  <button
    type="button"
    @click="themeStore.toggleTheme"
    class="relative p-2 rounded-xl transition-all duration-200 cursor-pointer flex items-center justify-center select-none"
    :class="[
      isTransparent
        ? 'text-white hover:bg-white/20'
        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
    ]"
    :title="themeStore.isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
    :aria-label="themeStore.isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
  >
    <transition name="rotate-icon" mode="out-in">
      <PhSun
        v-if="themeStore.isDark"
        :size="size"
        weight="bold"
        class="text-amber-400 drop-shadow-xs"
        key="sun"
      />
      <PhMoon
        v-else
        :size="size"
        weight="bold"
        class="transition-transform group-hover:-rotate-12"
        :class="isTransparent ? 'text-white' : 'text-slate-700'"
        key="moon"
      />
    </transition>
  </button>
</template>

<script setup>
import { useThemeStore } from '../stores/theme'
import { PhSun, PhMoon } from '@phosphor-icons/vue'

defineProps({
  size: {
    type: Number,
    default: 20
  },
  isTransparent: {
    type: Boolean,
    default: false
  }
})

const themeStore = useThemeStore()
</script>

<style scoped>
.rotate-icon-enter-active,
.rotate-icon-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.rotate-icon-enter-from {
  opacity: 0;
  transform: rotate(-90deg) scale(0.6);
}

.rotate-icon-leave-to {
  opacity: 0;
  transform: rotate(90deg) scale(0.6);
}
</style>
