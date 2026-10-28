import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useAuthStore } from './auth'
import api from '../services/api'

export const useNotificationStore = defineStore('notification', () => {
  const auth = useAuthStore()

  // Base list of notifications
  const notifications = ref([])
  const loading = ref(false)

  // Retrieve read IDs from localStorage
  const getReadIds = () => {
    try {
      const stored = localStorage.getItem(`read_notifications_${auth.user?.id || 'guest'}`)
      return stored ? JSON.parse(stored) : []
    } catch {
      return []
    }
  }

  const saveReadIds = (ids) => {
    try {
      localStorage.setItem(`read_notifications_${auth.user?.id || 'guest'}`, JSON.stringify(ids))
    } catch (e) {
      console.error(e)
    }
  }

  const readIds = ref(getReadIds())

  // Computed unread notifications
  const unreadCount = computed(() => {
    return notifications.value.filter((n) => !n.isRead).length
  })

  // Automatically refresh/clear notifications on login/logout
  watch(
    () => auth.user?.id,
    (newUserId) => {
      readIds.value = getReadIds()
      if (newUserId) {
        fetchNotifications()
      } else {
        notifications.value = []
      }
    }
  )

  // Fetch notifications based on role
  const fetchNotifications = async () => {
    if (!auth.isAuthenticated) {
      notifications.value = []
      return
    }

    loading.value = true
    readIds.value = getReadIds()

    const role = auth.role
    const list = []

    try {
      if (role === 'pemilik') {
        // Pemilik: pull recent bookings for their properties
        const res = await api.get('/bookings')
        const bookings = res.data?.data || []

        bookings.forEach((b) => {
          if (b.status === 'menunggu') {
            list.push({
              id: `booking_pending_${b.id}`,
              type: 'booking_request',
              title: 'Permintaan Sewa Baru',
              message: `${b.pengguna?.nama || b.user?.nama || 'Calon Penghuni'} mengajukan sewa untuk ${b.kamar?.nama_kamar || 'kamar'} di ${b.kos?.nama || 'kos Anda'}.`,
              time: b.created_at || new Date().toISOString(),
              icon: 'mark_chat_unread',
              iconBg: 'bg-amber-100 text-amber-700',
              route: '/dashboard/pemilik/pemesanan',
              isRead: readIds.value.includes(`booking_pending_${b.id}`)
            })
          } else if (b.status === 'disetujui') {
            list.push({
              id: `booking_approved_${b.id}`,
              type: 'booking_approved',
              title: 'Pemesanan Dikonfirmasi',
              message: `Reservasi #${b.id} untuk ${b.pengguna?.nama || b.user?.nama || 'penyewa'} telah Anda setujui.`,
              time: b.created_at || new Date().toISOString(),
              icon: 'check_circle',
              iconBg: 'bg-emerald-100 text-emerald-700',
              route: '/dashboard/pemilik/pemesanan',
              isRead: readIds.value.includes(`booking_approved_${b.id}`)
            })
          }
        })

        // Default welcoming/operational notification
        list.push({
          id: 'pemilik_welcome',
          type: 'info',
          title: 'Panel Mitra Kostara Aktif',
          message: 'Selamat datang di panel pengelola kos. Anda dapat memantau ketersediaan kamar dan transaksi sewa.',
          time: new Date(Date.now() - 3600000 * 24).toISOString(),
          icon: 'storefront',
          iconBg: 'bg-blue-100 text-primary',
          route: '/dashboard/pemilik/kos',
          isRead: readIds.value.includes('pemilik_welcome')
        })
      } else if (role === 'pencari') {
        // Pencari: pull their bookings
        const res = await api.get('/bookings')
        const bookings = res.data?.data || []

        bookings.forEach((b) => {
          if (b.status === 'disetujui') {
            list.push({
              id: `pencari_approved_${b.id}`,
              type: 'approved',
              title: 'Pengajuan Sewa Disetujui! 🎉',
              message: `Pemilik ${b.kos?.nama || 'Kos'} telah menyetujui permohonan sewa Anda untuk ${b.kamar?.nama_kamar || 'kamar'}.`,
              time: b.created_at || new Date().toISOString(),
              icon: 'verified',
              iconBg: 'bg-emerald-100 text-emerald-700',
              route: '/pesanan',
              isRead: readIds.value.includes(`pencari_approved_${b.id}`)
            })
          } else if (b.status === 'ditolak') {
            list.push({
              id: `pencari_rejected_${b.id}`,
              type: 'rejected',
              title: 'Pengajuan Sewa Ditolak',
              message: `Mohon maaf, permohonan sewa #${b.id} di ${b.kos?.nama || 'Kos'} belum dapat disetujui.`,
              time: b.created_at || new Date().toISOString(),
              icon: 'cancel',
              iconBg: 'bg-rose-100 text-rose-700',
              route: '/pesanan',
              isRead: readIds.value.includes(`pencari_rejected_${b.id}`)
            })
          } else if (b.status === 'menunggu') {
            list.push({
              id: `pencari_pending_${b.id}`,
              type: 'pending',
              title: 'Menunggu Konfirmasi Pemilik',
              message: `Pengajuan sewa untuk ${b.kos?.nama || 'Kos'} sedang menunggu peninjauan pemilik.`,
              time: b.created_at || new Date().toISOString(),
              icon: 'schedule',
              iconBg: 'bg-amber-100 text-amber-700',
              route: '/pesanan',
              isRead: readIds.value.includes(`pencari_pending_${b.id}`)
            })
          }
        })

        list.push({
          id: 'pencari_tips',
          type: 'info',
          title: 'Tips Mencari Kos Ideal',
          message: 'Gunakan fitur Peta Lokasi untuk menemukan kos terdekat dari kampus atau tempat kerja Anda di seluruh Indonesia.',
          time: new Date(Date.now() - 3600000 * 48).toISOString(),
          icon: 'lightbulb',
          iconBg: 'bg-blue-100 text-primary',
          route: '/peta',
          isRead: readIds.value.includes('pencari_tips')
        })
      } else if (role === 'admin') {
        // Admin: pull bookings and users overview
        const res = await api.get('/bookings')
        const bookings = res.data?.data || []

        bookings.slice(0, 5).forEach((b) => {
          list.push({
            id: `admin_booking_${b.id}`,
            type: 'admin_audit',
            title: `Transaksi Reservasi Masuk #${b.id}`,
            message: `Pemesanan baru oleh ${b.pengguna?.nama || b.user?.nama || 'User'} pada ${b.kos?.nama || 'Kos'} (Status: ${b.status}).`,
            time: b.created_at || new Date().toISOString(),
            icon: 'receipt_long',
            iconBg: 'bg-slate-200 text-slate-800',
            route: '/pesanan',
            isRead: readIds.value.includes(`admin_booking_${b.id}`)
          })
        })

        list.push({
          id: 'admin_system_healthy',
          type: 'system',
          title: 'Sistem Kostara Berjalan Normal',
          message: 'Semua layanan REST API, Geocoding OpenStreetMap, dan Database tersinkronisasi optimal.',
          time: new Date().toISOString(),
          icon: 'dns',
          iconBg: 'bg-emerald-100 text-emerald-800',
          route: '/dashboard/admin',
          isRead: readIds.value.includes('admin_system_healthy')
        })
      }
    } catch (err) {
      console.warn('Gagal memuat notifikasi dinamis:', err)
    } finally {
      loading.value = false
      notifications.value = list
    }
  }

  // Mark single as read
  const markAsRead = (id) => {
    const item = notifications.value.find((n) => n.id === id)
    if (item && !item.isRead) {
      item.isRead = true
      if (!readIds.value.includes(id)) {
        readIds.value.push(id)
        saveReadIds(readIds.value)
      }
    }
  }

  // Mark all as read
  const markAllAsRead = () => {
    notifications.value.forEach((n) => {
      n.isRead = true
      if (!readIds.value.includes(n.id)) {
        readIds.value.push(n.id)
      }
    })
    saveReadIds(readIds.value)
  }

  // Remove notification
  const removeNotification = (id) => {
    notifications.value = notifications.value.filter((n) => n.id !== id)
  }

  return {
    notifications,
    loading,
    unreadCount,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
    removeNotification
  }
})
