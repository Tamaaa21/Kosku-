import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useAuthStore } from './auth'

export const useFavoritesStore = defineStore('favorites', () => {
  const auth = useAuthStore()

  const getStorageKey = () => {
    return auth.user?.id ? `kostara_favorites_${auth.user.id}` : 'kostara_favorites_guest'
  }

  const mergeGuestFavorites = (userKey) => {
    try {
      const guestRaw = localStorage.getItem('kostara_favorites_guest')
      if (guestRaw) {
        const guestItems = JSON.parse(guestRaw)
        if (Array.isArray(guestItems) && guestItems.length > 0) {
          const userRaw = localStorage.getItem(userKey)
          const userItems = userRaw ? JSON.parse(userRaw) : []
          const merged = [...userItems]
          guestItems.forEach((g) => {
            if (!merged.some((u) => Number(u.id) === Number(g.id))) {
              merged.push(g)
            }
          })
          localStorage.setItem(userKey, JSON.stringify(merged))
          localStorage.removeItem('kostara_favorites_guest')
          return merged
        }
      }
    } catch {
      // Ignore parse error
    }
    return null
  }

  const loadFromStorage = () => {
    const key = getStorageKey()
    try {
      if (auth.user?.id) {
        const merged = mergeGuestFavorites(key)
        if (merged) return merged
      }

      const raw = localStorage.getItem(key)
      if (raw) return JSON.parse(raw)

      // Check legacy global key for migration
      const legacy =
        localStorage.getItem('kostara_favorites') ||
        localStorage.getItem('kosku_favorites') ||
        (auth.user?.id ? localStorage.getItem(`kosku_favorites_${auth.user.id}`) : null)

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

  // Keep favorites in sync when user logs in, switches accounts, or logs out
  watch(
    () => auth.user?.id,
    () => {
      favorites.value = loadFromStorage()
    }
  )

  const persist = () => {
    const key = getStorageKey()
    try {
      localStorage.setItem(key, JSON.stringify(favorites.value))
    } catch {
      // Storage might be full or disabled in private mode
    }
  }

  const count = computed(() => favorites.value.length)

  const isFavorite = (kosId) => {
    if (!kosId) return false
    const id = Number(kosId)
    return favorites.value.some((item) => Number(item.id) === id)
  }

  const toggleFavorite = (kos) => {
    if (!kos || !kos.id) return false

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
        kamar_tersedia_count:
          kos.kamar_tersedia_count ?? kos.kamar?.filter((k) => k.status === 'tersedia')?.length ?? 0,
        saved_at: new Date().toISOString(),
      })
      persist()
      return true
    }
  }

  const removeFavorite = (kosId) => {
    if (!kosId) return
    const id = Number(kosId)
    const index = favorites.value.findIndex((item) => Number(item.id) === id)
    if (index >= 0) {
      favorites.value.splice(index, 1)
      persist()
    }
  }

  const clearFavorites = () => {
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
