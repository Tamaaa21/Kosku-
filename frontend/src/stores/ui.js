import { defineStore } from 'pinia'

let nextToastId = 1

export const useUiStore = defineStore('ui', {
  state: () => ({
    toasts: [],
    confirmModal: {
      isOpen: false,
      title: '',
      message: '',
      confirmText: 'Konfirmasi',
      cancelText: 'Batal',
      confirmType: 'primary', // 'primary' | 'danger'
      resolve: null,
    },
  }),

  actions: {
    showToast(message, type = 'success', duration = 3500) {
      const id = nextToastId++
      this.toasts.push({ id, message, type })
      if (duration > 0) {
        setTimeout(() => {
          this.removeToast(id)
        }, duration)
      }
      return id
    },

    success(message, duration = 3500) {
      return this.showToast(message, 'success', duration)
    },

    error(message, duration = 4500) {
      return this.showToast(message, 'error', duration)
    },

    info(message, duration = 3500) {
      return this.showToast(message, 'info', duration)
    },

    warning(message, duration = 4000) {
      return this.showToast(message, 'warning', duration)
    },

    removeToast(id) {
      this.toasts = this.toasts.filter((t) => t.id !== id)
    },

    confirm({
      title = 'Konfirmasi Tindakan',
      message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
      confirmText = 'Konfirmasi',
      cancelText = 'Batal',
      confirmType = 'primary',
    } = {}) {
      return new Promise((resolve) => {
        this.confirmModal = {
          isOpen: true,
          title,
          message,
          confirmText,
          cancelText,
          confirmType,
          resolve,
        }
      })
    },

    handleConfirm(confirmed) {
      if (this.confirmModal.resolve) {
        this.confirmModal.resolve(confirmed)
      }
      this.confirmModal.isOpen = false
      this.confirmModal.resolve = null
    },
  },
})
