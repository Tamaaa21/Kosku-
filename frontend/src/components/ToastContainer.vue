<template>
  <div
    class="fixed top-20 right-4 sm:right-6 z-[9999] flex flex-col gap-2.5 max-w-sm w-[calc(100vw-2rem)] pointer-events-none"
    aria-live="polite"
  >
    <transition-group
      enter-active-class="transform transition ease-out duration-300"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-for="toast in ui.toasts"
        :key="toast.id"
        class="pointer-events-auto flex items-start gap-3 p-3.5 sm:p-4 rounded-xl shadow-lg border backdrop-blur-md transition-all text-xs sm:text-sm font-medium"
        :class="getToastClasses(toast.type)"
        role="alert"
      >
        <span
          class="material-symbols-outlined text-[20px] shrink-0 mt-0.5"
          :class="getIconClasses(toast.type)"
          style="font-variation-settings: 'FILL' 1;"
        >
          {{ getIconName(toast.type) }}
        </span>

        <div class="flex-1 min-w-0 pr-1 leading-relaxed text-on-surface">
          {{ toast.message }}
        </div>

        <button
          type="button"
          @click="ui.removeToast(toast.id)"
          class="text-on-surface-variant/60 hover:text-on-surface shrink-0 p-1 -mr-1 -mt-1 rounded-lg transition"
          aria-label="Tutup notifikasi"
        >
          <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useUiStore } from '../stores/ui'

const ui = useUiStore()

function getToastClasses(type) {
  switch (type) {
    case 'success':
      return 'bg-surface-container-lowest/95 border-emerald-200/80 shadow-emerald-950/5'
    case 'error':
      return 'bg-surface-container-lowest/95 border-rose-200/80 shadow-rose-950/5'
    case 'warning':
      return 'bg-surface-container-lowest/95 border-amber-200/80 shadow-amber-950/5'
    case 'info':
    default:
      return 'bg-surface-container-lowest/95 border-primary/20 shadow-primary/5'
  }
}

function getIconClasses(type) {
  switch (type) {
    case 'success':
      return 'text-emerald-600'
    case 'error':
      return 'text-rose-600'
    case 'warning':
      return 'text-amber-600'
    case 'info':
    default:
      return 'text-primary'
  }
}

function getIconName(type) {
  switch (type) {
    case 'success':
      return 'check_circle'
    case 'error':
      return 'cancel'
    case 'warning':
      return 'warning'
    case 'info':
    default:
      return 'info'
  }
}
</script>
