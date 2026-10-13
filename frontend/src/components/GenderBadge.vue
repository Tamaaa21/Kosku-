<template>
  <span
    :class="[
      'inline-flex items-center gap-1 font-semibold rounded-full select-none transition-colors shrink-0',
      size === 'sm' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs',
      badgeStyles
    ]"
  >
    <span
      v-if="showIcon"
      class="material-symbols-outlined shrink-0"
      :class="size === 'sm' ? 'text-[12px]' : 'text-[14px]'"
    >
      {{ iconName }}
    </span>
    <span>{{ prefix }}{{ label }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  jenis: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'sm' // 'sm' | 'md'
  },
  showIcon: {
    type: Boolean,
    default: false
  },
  prefix: {
    type: String,
    default: ''
  }
})

const normalized = computed(() => (props.jenis || '').toLowerCase().trim())

const label = computed(() => {
  switch (normalized.value) {
    case 'putri':
      return 'Putri'
    case 'putra':
      return 'Putra'
    case 'campur':
      return 'Campur'
    default:
      return props.jenis || 'Umum'
  }
})

const iconName = computed(() => {
  switch (normalized.value) {
    case 'putri':
      return 'female'
    case 'putra':
      return 'male'
    case 'campur':
      return 'group'
    default:
      return 'home'
  }
})

const badgeStyles = computed(() => {
  switch (normalized.value) {
    case 'putri':
      return 'bg-pink-50 text-pink-700 border border-pink-200/80'
    case 'putra':
      return 'bg-sky-50 text-sky-800 border border-sky-200/80'
    case 'campur':
      return 'bg-violet-50 text-violet-800 border border-violet-200/80'
    default:
      return 'bg-surface-container text-on-surface-variant border border-slate-200'
  }
})
</script>
