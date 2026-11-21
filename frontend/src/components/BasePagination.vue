<template>
  <div
    v-if="actualTotal > 0"
    :class="[
      'flex flex-col md:flex-row items-center justify-between gap-4 py-3.5 px-5 sm:px-6 select-none transition-all',
      card
        ? 'bg-white rounded-2xl border border-slate-100 shadow-xs'
        : 'bg-white border-t border-slate-100'
    ]"
  >
    <!-- Left: Data count info -->
    <div class="text-sm text-slate-600 font-normal order-1 md:order-1 text-center md:text-left">
      <span>
        Menampilkan
        <strong class="font-bold text-slate-800">{{ actualFrom }}–{{ actualTo }}</strong>
        dari
        <strong class="font-bold text-slate-800">{{ actualTotal }}</strong>
        {{ itemName }}
      </span>
    </div>

    <!-- Center: Page Navigation Buttons -->
    <nav
      class="inline-flex items-center gap-1.5 order-2 md:order-2"
      aria-label="Navigasi Halaman"
    >
      <!-- Previous Page -->
      <button
        type="button"
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage <= 1 || loading"
        class="w-9 h-9 min-w-9 rounded-xl flex items-center justify-center bg-slate-100/80 text-slate-600 hover:bg-slate-200/80 hover:text-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
        title="Sebelumnya"
      >
        <PhCaretLeft :size="15" weight="bold" />
      </button>

      <!-- Page Numbers with Smart Windowing -->
      <div class="inline-flex items-center gap-1.5">
        <template v-for="(item, idx) in visiblePages" :key="`page-${idx}`">
          <!-- Ellipsis -->
          <span
            v-if="item === '...'"
            class="w-7 h-9 flex items-center justify-center text-slate-400 font-bold select-none text-xs"
          >
            &hellip;
          </span>

          <!-- Page Button -->
          <button
            v-else
            type="button"
            @click="goToPage(item)"
            :disabled="loading || item === currentPage"
            class="w-9 h-9 min-w-9 rounded-xl flex items-center justify-center text-sm font-semibold transition cursor-pointer"
            :class="
              item === currentPage
                ? 'bg-[#017E91] text-white font-bold shadow-xs pointer-events-none'
                : 'bg-white border border-slate-200/90 text-slate-700 hover:bg-slate-50 hover:border-slate-300'
            "
          >
            {{ item }}
          </button>
        </template>
      </div>

      <!-- Next Page -->
      <button
        type="button"
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage >= lastPage || loading"
        class="w-9 h-9 min-w-9 rounded-xl flex items-center justify-center bg-slate-100/80 text-slate-600 hover:bg-slate-200/80 hover:text-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
        title="Berikutnya"
      >
        <PhCaretRight :size="15" weight="bold" />
      </button>
    </nav>

    <!-- Right: Per-page selector -->
    <div
      v-if="showPerPage && perPageOptions.length > 1"
      class="flex items-center gap-2 text-sm text-slate-600 order-3 md:order-3"
    >
      <label :for="perPageId" class="font-normal text-slate-600">
        Tampilkan:
      </label>
      <div class="relative inline-flex items-center">
        <select
          :id="perPageId"
          :value="perPage"
          @change="onPerPageChange"
          :disabled="loading"
          class="appearance-none bg-white border border-slate-200 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#017E91]/20 focus:border-[#017E91] text-slate-700 text-sm font-medium rounded-xl pl-3.5 pr-8 py-1.5 cursor-pointer shadow-2xs transition disabled:opacity-50"
        >
          <option v-for="opt in perPageOptions" :key="opt" :value="opt">
            {{ opt }} per halaman
          </option>
        </select>
        <PhCaretDown
          :size="13"
          weight="bold"
          class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  PhCaretLeft,
  PhCaretRight,
  PhCaretDown,
} from '@phosphor-icons/vue'

const props = defineProps({
  currentPage: {
    type: Number,
    default: 1,
  },
  lastPage: {
    type: Number,
    default: 1,
  },
  total: {
    type: Number,
    default: null,
  },
  totalItems: {
    type: Number,
    default: null,
  },
  perPage: {
    type: Number,
    default: 6,
  },
  from: {
    type: Number,
    default: null,
  },
  fromItem: {
    type: Number,
    default: null,
  },
  to: {
    type: Number,
    default: null,
  },
  toItem: {
    type: Number,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  showPerPage: {
    type: Boolean,
    default: true,
  },
  perPageOptions: {
    type: Array,
    default: () => [6, 12, 24, 48],
  },
  itemName: {
    type: String,
    default: 'data',
  },
  card: {
    type: Boolean,
    default: false,
  },
})

const actualTotal = computed(() => {
  if (props.total !== null && props.total !== undefined) return props.total
  if (props.totalItems !== null && props.totalItems !== undefined) return props.totalItems
  return 0
})

const actualFrom = computed(() => {
  if (props.from !== null && props.from !== undefined && props.from > 0) return props.from
  if (props.fromItem !== null && props.fromItem !== undefined && props.fromItem > 0) return props.fromItem
  return actualTotal.value > 0 ? 1 : 0
})

const actualTo = computed(() => {
  if (props.to !== null && props.to !== undefined && props.to > 0) return props.to
  if (props.toItem !== null && props.toItem !== undefined && props.toItem > 0) return props.toItem
  return actualTotal.value
})

const emit = defineEmits(['change-page', 'change-per-page'])

const perPageId = `per-page-${Math.random().toString(36).substring(2, 9)}`

const goToPage = (page) => {
  if (page < 1 || page > props.lastPage || page === props.currentPage || props.loading) return
  emit('change-page', page)
}

const onPerPageChange = (e) => {
  const val = Number(e.target.value)
  if (val && val !== props.perPage) {
    emit('change-per-page', val)
  }
}

/**
 * Returns array of numbers and ellipsis strings for sliding window pagination
 * e.g., [1, 2, 3, 4, 5] or [1, '...', 4, 5, 6, '...', 12]
 */
const visiblePages = computed(() => {
  const current = props.currentPage
  const total = props.lastPage

  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }

  const pages = []

  // Always include page 1
  pages.push(1)

  const leftBound = Math.max(2, current - 1)
  const rightBound = Math.min(total - 1, current + 1)

  if (leftBound > 2) {
    pages.push('...')
  }

  for (let i = leftBound; i <= rightBound; i++) {
    pages.push(i)
  }

  if (rightBound < total - 1) {
    pages.push('...')
  }

  // Always include last page
  pages.push(total)

  return pages
})
</script>
