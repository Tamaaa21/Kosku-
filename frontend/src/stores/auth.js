import { defineStore } from 'pinia'
import api from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    role: (state) => state.user?.role || null,
    isPencari: (state) => state.user?.role === 'pencari',
    isPemilik: (state) => state.user?.role === 'pemilik',
    isAdmin: (state) => state.user?.role === 'admin',
    isVerified: (state) => {
      if (state.user?.role === 'admin') return true
      return state.user?.status_verifikasi === 'verified'
    },
    verificationStatus: (state) => state.user?.status_verifikasi || 'unverified',
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/auth/login', credentials)
        const { user, token } = response.data

        this.user = user
        this.token = token
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('token', token)

        return user
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal login, periksa kembali email dan kata sandi Anda.'
        throw err
      } finally {
        this.loading = false
      }
    },

    async register(data) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/auth/register', data)
        const { user, token } = response.data

        this.user = user
        this.token = token
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('token', token)

        return user
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal registrasi.'
        throw err
      } finally {
        this.loading = false
      }
    },

    async fetchMe() {
      if (!this.token) return null
      try {
        const response = await api.get('/me')
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        return this.user
      } catch (err) {
        this.logout()
        return null
      }
    },

    async updateProfile(data) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put('/me', data)
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memperbarui profil.'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updateAvatar(file) {
      this.loading = true
      this.error = null
      try {
        const formData = new FormData()
        formData.append('foto', file)
        const response = await api.post('/me/avatar', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal memperbarui foto profil.'
        throw err
      } finally {
        this.loading = false
      }
    },

    async updatePassword(payload) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put('/me/password', payload)
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Gagal mengubah kata sandi.'
        throw err
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await api.post('/auth/logout')
        }
      } catch (err) {
        // Abaikan error saat logout
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('user')
        localStorage.removeItem('token')
      }
    },
  },
})
