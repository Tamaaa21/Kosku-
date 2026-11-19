import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { Capacitor } from '@capacitor/core'
import { StatusBar, Style } from '@capacitor/status-bar'

export const useThemeStore = defineStore('theme', () => {
  const currentTheme = ref('light')

  const isDark = computed(() => currentTheme.value === 'dark')

  const applyThemeToDom = async (dark) => {
    if (dark) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }

    // Sinkronisasi status bar pada perangkat Android / iOS via Capacitor
    if (Capacitor.isNativePlatform()) {
      try {
        if (dark) {
          await StatusBar.setStyle({ style: Style.Dark })
          await StatusBar.setBackgroundColor({ color: '#0b1120' })
        } else {
          await StatusBar.setStyle({ style: Style.Dark })
          await StatusBar.setBackgroundColor({ color: '#017E91' })
        }
      } catch (_) {}
    }
  }

  const initTheme = () => {
    const saved = localStorage.getItem('kostara_theme')
    if (saved === 'dark' || saved === 'light') {
      currentTheme.value = saved
    } else {
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
      currentTheme.value = prefersDark ? 'dark' : 'light'
    }
    applyThemeToDom(isDark.value)
  }

  const setTheme = (theme) => {
    currentTheme.value = theme
    localStorage.setItem('kostara_theme', theme)
    applyThemeToDom(isDark.value)
  }

  const toggleTheme = () => {
    const next = isDark.value ? 'light' : 'dark'
    setTheme(next)
  }

  return {
    currentTheme,
    isDark,
    initTheme,
    setTheme,
    toggleTheme,
  }
})
