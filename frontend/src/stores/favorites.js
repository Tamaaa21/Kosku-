import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useFavoritesStore = defineStore('favorites', () => {
  const STORAGE_KEY = 'kosku_favorites'

  const loadFromStorage = () => {
    try {
      const raw = localStorage.getItem(STORAGE_KEY)
      return raw ? JSON.parse(raw) : []
    } catch {
      return []
    }
  }

  const favorites = ref(loadFromStorage())

  const persist = () => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites.value))
    } catch {
      // Storage might be full or disabled in private mode
    }
  }

  const count = computed(() => favorites.value.length)

  const isFavorite = (kosId) => {
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
        kamar_tersedia_count: kos.kamar_tersedia_count ?? kos.kamar?.filter(k => k.status === 'tersedia')?.length ?? 0,
        saved_at: new Date().toISOString(),
      })
      persist()
      return true
    }
  }

  const removeFavorite = (kosId) => {
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
