<template>
  <teleport to="body">
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="ui.confirmModal.isOpen"
        class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-inverse-surface/60 backdrop-blur-xs"
        role="dialog"
        aria-modal="true"
        @keydown.esc="ui.handleConfirm(false)"
      >
        <div
          class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 sm:p-7 flex flex-col gap-5"
          @click.stop
        >
          <div class="flex items-start gap-4">
            <div
              class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0"
              :class="
                ui.confirmModal.confirmType === 'danger'
                  ? 'bg-rose-50 text-rose-600'
                  : 'bg-primary/10 text-primary'
              "
            >
              <PhWarning v-if="ui.confirmModal.confirmType === 'danger'" :size="24" weight="duotone" />
              <PhQuestion v-else :size="24" weight="duotone" />
            </div>

            <div class="flex-1 min-w-0">
              <h3 class="text-base sm:text-lg font-bold text-on-surface leading-snug">
                {{ ui.confirmModal.title }}
              </h3>
              <p class="text-xs sm:text-sm text-on-surface-variant mt-1.5 leading-relaxed whitespace-pre-line">
                {{ ui.confirmModal.message }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
            <button
              type="button"
              @click="ui.handleConfirm(false)"
              class="min-h-[44px] px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-on-surface-variant hover:text-on-surface bg-surface-container hover:bg-surface-container-high transition active:scale-[0.98]"
            >
              {{ ui.confirmModal.cancelText || 'Batal' }}
            </button>

            <button
              type="button"
              @click="ui.handleConfirm(true)"
              class="min-h-[44px] px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white shadow-sm transition active:scale-[0.98]"
              :class="
                ui.confirmModal.confirmType === 'danger'
                  ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20'
                  : 'bg-primary hover:bg-primary-container shadow-primary/20'
              "
              autofocus
            >
              {{ ui.confirmModal.confirmText || 'Konfirmasi' }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { useUiStore } from '../stores/ui'
import { PhWarning, PhQuestion } from '@phosphor-icons/vue'

const ui = useUiStore()
</script>
