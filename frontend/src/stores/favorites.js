import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useAuthStore } from './auth'

export const useFavoritesStore = defineStore('favorites', () => {
  const auth = useAuthStore()

  const getStorageKey = () => {
    return auth.user?.id ? `kosku_favorites_${auth.user.id}` : null
  }

  const loadFromStorage = () => {
    const key = getStorageKey()
    if (!key) return []
    try {
      const raw = localStorage.getItem(key)
      if (raw) return JSON.parse(raw)
      // Check legacy global key for migration
      const legacy = localStorage.getItem('kosku_favorites')
      if (legacy) {
        localStorage.setItem(key, legacy)
        return JSON.parse(legacy)
      }
      return []
    } catch {
      return []
    }
  }

  const favorites = ref(loadFromStorage())

  // Keep favorites in sync with the logged-in user
  watch(
    () => auth.user?.id,
    (newUserId) => {
      if (newUserId) {
        favorites.value = loadFromStorage()
      } else {
        favorites.value = []
      }
    }
  )

  const persist = () => {
    const key = getStorageKey()
    if (!key) return
    try {
      localStorage.setItem(key, JSON.stringify(favorites.value))
    } catch {
      // Storage might be full or disabled in private mode
    }
  }

  const count = computed(() => (auth.isAuthenticated ? favorites.value.length : 0))

  const isFavorite = (kosId) => {
    if (!auth.isAuthenticated) return false
    const id = Number(kosId)
    return favorites.value.some((item) => Number(item.id) === id)
  }

  const toggleFavorite = (kos) => {
    if (!auth.isAuthenticated || !kos || !kos.id) return false

    const id = Number(kos.id)
    const index = favorites.value.findIndex((item) => Number(item.id) === id)

    if (index >= 0) {
      favorites.value.splice(index, 1)
      persist()
      return false
    } else {
      // Store relevant metadata so cards render smoothly in favorites page
      favorites.value.push({
        id: kos.id,
        nama: kos.nama,
        alamat: kos.alamat,
        jenis: kos.jenis,
        harga: kos.harga,
        harga_harian: kos.harga_harian,
        harga_mingguan: kos.harga_mingguan,
        latitude: kos.latitude,
        longitude: kos.longitude,
        rating_rata_rata: kos.rating_rata_rata,
        ulasan_count: kos.ulasan_count,
        fasilitas: kos.fasilitas || [],
        foto: kos.foto || [],
        kamar_tersedia_count: kos.kamar_tersedia_count ?? kos.kamar?.filter(k => k.status === 'tersedia')?.length ?? 0,
        saved_at: new Date().toISOString(),
      })
      persist()
      return true
    }
  }

  const removeFavorite = (kosId) => {
    if (!auth.isAuthenticated) return
    const id = Number(kosId)
    const index = favorites.value.findIndex((item) => Number(item.id) === id)
    if (index >= 0) {
      favorites.value.splice(index, 1)
      persist()
    }
  }

  const clearFavorites = () => {
    if (!auth.isAuthenticated) return
    favorites.value = []
    persist()
  }

  return {
    favorites,
    count,
    isFavorite,
    toggleFavorite,
    removeFavorite,
    clearFavorites,
  }
})
