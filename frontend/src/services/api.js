import axios from 'axios'
import { Capacitor } from '@capacitor/core'

const resolveBaseUrl = () => {
  const customUrl = localStorage.getItem('api_base_url')
  if (customUrl) return customUrl

  let envUrl = import.meta.env.VITE_API_URL
  if (Capacitor.isNativePlatform()) {
    // Di Android Emulator, loopback ke localhost mesin pengembang adalah 10.0.2.2
    if (!envUrl || envUrl.includes('127.0.0.1') || envUrl.includes('localhost')) {
      return envUrl ? envUrl.replace(/127\.0\.0\.1|localhost/, '10.0.2.2') : 'https://kostara.web.id/api/v1'
    }
  }

  return envUrl || 'https://kostara.web.id/api/v1'
}

const api = axios.create({
  baseURL: resolveBaseUrl(),
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

// Request Interceptor: inject Bearer token from localStorage
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, (error) => {
  return Promise.reject(error)
})

// Response Interceptor: handle 401 unauthenticated
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api
