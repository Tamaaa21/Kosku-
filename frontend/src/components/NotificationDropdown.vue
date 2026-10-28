<template>
  <div class="relative" ref="dropdownRef">
    <!-- Bell Trigger Button -->
    <button
      @click="toggleDropdown"
      class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center justify-center focus:outline-none cursor-pointer"
      type="button"
      title="Notifikasi"
      aria-label="Lihat Notifikasi"
    >
      <PhBell :size="21" weight="bold" />

      <!-- Unread Indicator Badge matching UI screenshot -->
      <span
        v-if="notificationStore.unreadCount > 0"
        class="absolute top-1 right-1 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-rose-500 text-white font-bold text-[10px] ring-2 ring-white shadow-xs"
      >
        {{ notificationStore.unreadCount > 9 ? '9+' : notificationStore.unreadCount }}
      </span>
    </button>

    <!-- Popover Notification Menu -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 sm:w-96 bg-surface-container-lowest rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-150"
    >
      <!-- Header -->
      <div class="p-3.5 px-4 bg-surface-container-lowest border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <h3 class="text-xs font-bold text-on-surface uppercase tracking-wider">Notifikasi</h3>
          <span
            v-if="notificationStore.unreadCount > 0"
            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-error-container text-on-error-container"
          >
            {{ notificationStore.unreadCount }} baru
          </span>
        </div>

        <button
          v-if="notificationStore.unreadCount > 0"
          @click="notificationStore.markAllAsRead"
          class="text-[11px] text-primary hover:underline font-semibold"
          type="button"
        >
          Tandai semua dibaca
        </button>
      </div>

      <!-- Filter Tabs -->
      <div class="flex items-center gap-1 p-2 bg-surface-container-low/50 border-b border-slate-100 text-xs">
        <button
          @click="activeFilter = 'all'"
          :class="[
            'flex-1 py-1 px-3 rounded-lg font-semibold transition text-center',
            activeFilter === 'all'
              ? 'bg-surface-container-lowest text-primary shadow-xs'
              : 'text-on-surface-variant hover:text-on-surface'
          ]"
          type="button"
        >
          Semua ({{ notificationStore.notifications.length }})
        </button>
        <button
          @click="activeFilter = 'unread'"
          :class="[
            'flex-1 py-1 px-3 rounded-lg font-semibold transition text-center',
            activeFilter === 'unread'
              ? 'bg-surface-container-lowest text-primary shadow-xs'
              : 'text-on-surface-variant hover:text-on-surface'
          ]"
          type="button"
        >
          Belum Dibaca ({{ notificationStore.unreadCount }})
        </button>
      </div>

      <!-- Notifications List -->
      <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
        <div v-if="notificationStore.loading" class="p-8 text-center text-slate-400 text-xs">
          <PhCircleNotch :size="24" weight="bold" class="animate-spin text-primary mx-auto mb-1" />
          <p class="mt-1">Memuat notifikasi...</p>
        </div>

        <div
          v-else-if="filteredList.length === 0"
          class="p-8 text-center text-slate-400 text-xs flex flex-col items-center justify-center"
        >
          <PhBellSlash :size="32" weight="duotone" class="text-slate-300 mb-1" />
          <p class="font-semibold text-slate-600">Tidak ada notifikasi</p>
          <p class="text-[11px] text-slate-400 mt-0.5">
            {{ activeFilter === 'unread' ? 'Semua notifikasi sudah dibaca.' : 'Belum ada aktivitas baru saat ini.' }}
          </p>
        </div>

        <div
          v-for="item in filteredList"
          :key="item.id"
          @click="handleClickNotification(item)"
          :class="[
            'p-3.5 px-4 flex items-start gap-3 transition-colors cursor-pointer group',
            item.isRead ? 'bg-white hover:bg-slate-50' : 'bg-primary/5 hover:bg-primary/10'
          ]"
        >
          <!-- Icon Badge -->
          <div
            :class="[
              'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs',
              item.iconBg || 'bg-primary/10 text-primary'
            ]"
          >
            <component :is="resolveNotificationIcon(item.icon)" :size="18" weight="bold" />
          </div>

          <!-- Body -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
              <h4
                :class="[
                  'text-xs truncate leading-tight',
                  item.isRead ? 'font-semibold text-on-surface' : 'font-bold text-primary'
                ]"
              >
                {{ item.title }}
              </h4>
              <span
                v-if="!item.isRead"
                class="w-2 h-2 rounded-full bg-primary shrink-0"
                title="Belum dibaca"
              ></span>
            </div>
            <p class="text-[11px] text-on-surface-variant line-clamp-2 mt-1 leading-snug">
              {{ item.message }}
            </p>
            <span class="text-[10px] text-secondary mt-1.5 block">
              {{ formatRelativeTime(item.time) }}
            </span>
          </div>

          <!-- Dismiss Button -->
          <button
            @click.stop="notificationStore.removeNotification(item.id)"
            class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-slate-200/60 text-slate-400 hover:text-slate-600 transition"
            title="Hapus"
            type="button"
          >
            <PhX :size="14" weight="bold" />
          </button>
        </div>
      </div>

      <!-- Footer Action -->
      <div class="p-2.5 bg-surface-container-low/40 border-t border-slate-100 text-center">
        <button
          @click="refresh"
          class="text-[11px] font-semibold text-primary hover:underline flex items-center justify-center gap-1 w-full py-1"
          type="button"
        >
          <PhArrowClockwise :size="14" weight="bold" />
          <span>Segarkan Notifikasi</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '../stores/notification'
import {
  PhBell,
  PhBellSlash,
  PhCircleNotch,
  PhX,
  PhArrowClockwise,
  PhReceipt,
  PhInfo,
  PhCheckCircle,
  PhWarning,
  PhHouse,
  PhUser,
  PhChatText,
  PhStorefront,
  PhXCircle,
  PhClock,
  PhLightbulb,
  PhDesktop
} from '@phosphor-icons/vue'

const router = useRouter()
const notificationStore = useNotificationStore()

const isOpen = ref(false)
const activeFilter = ref('all')
const dropdownRef = ref(null)

const resolveNotificationIcon = (iconName) => {
  switch (iconName) {
    case 'mark_chat_unread':
      return PhChatText
    case 'storefront':
      return PhStorefront
    case 'verified':
    case 'check_circle':
    case 'check':
      return PhCheckCircle
    case 'cancel':
      return PhXCircle
    case 'schedule':
      return PhClock
    case 'lightbulb':
      return PhLightbulb
    case 'dns':
      return PhDesktop
    case 'receipt_long':
    case 'receipt':
      return PhReceipt
    case 'warning':
    case 'alert':
      return PhWarning
    case 'home_work':
    case 'home':
      return PhHouse
    case 'person':
    case 'manage_accounts':
      return PhUser
    case 'info':
    default:
      return PhInfo
  }
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    notificationStore.fetchNotifications()
  }
}

const filteredList = computed(() => {
  if (activeFilter.value === 'unread') {
    return notificationStore.notifications.filter((n) => !n.isRead)
  }
  return notificationStore.notifications
})

const handleClickNotification = (item) => {
  notificationStore.markAsRead(item.id)
  isOpen.value = false
  if (item.route) {
    router.push(item.route)
  }
}

const refresh = async () => {
  await notificationStore.fetchNotifications()
}

const formatRelativeTime = (timeStr) => {
  if (!timeStr) return 'Baru saja'
  try {
    const diffMs = Date.now() - new Date(timeStr).getTime()
    const diffSec = Math.floor(diffMs / 1000)
    if (diffSec < 60) return 'Baru saja'
    const diffMin = Math.floor(diffSec / 60)
    if (diffMin < 60) return `${diffMin} menit yang lalu`
    const diffHour = Math.floor(diffMin / 60)
    if (diffHour < 24) return `${diffHour} jam yang lalu`
    const diffDay = Math.floor(diffHour / 24)
    return `${diffDay} hari yang lalu`
  } catch {
    return timeStr
  }
}

// Close when clicking outside
const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  notificationStore.fetchNotifications()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
