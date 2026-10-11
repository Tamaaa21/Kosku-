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
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const response = await api.post('/login', credentials)
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
        const response = await api.post('/register', data)
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

    async logout() {
      try {
        if (this.token) {
          await api.post('/logout')
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
