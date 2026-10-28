<template>
  <div class="min-h-screen bg-surface-container-low pt-20 pb-16">
    <div class="max-w-7xl mx-auto px-margin">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs text-on-surface-variant mb-4">
        <router-link to="/" class="hover:text-primary transition">Beranda</router-link>
        <span>/</span>
        <span class="text-on-surface font-semibold">Kos Tersimpan</span>
      </nav>

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 bg-surface-container-lowest p-6 rounded-2xl border border-slate-100 shadow-xs">
        <div>
          <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-error flex items-center justify-center shrink-0">
              <PhHeart :size="24" weight="fill" />
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-bold text-on-surface tracking-tight">
                Kos Tersimpan / Favorit
              </h1>
              <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">
                Koleksi kamar dan kos pilihan yang Anda tandai untuk pertimbangan sewa.
              </p>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-surface-container text-on-surface">
            {{ favoritesStore.count }} Kos Tersimpan
          </span>

          <button
            v-if="favoritesStore.count > 0"
            @click="confirmClearFavorites"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-error hover:bg-error-container/20 border border-error/20 transition cursor-pointer"
            type="button"
          >
            <PhTrash :size="15" weight="bold" />
            <span>Hapus Semua</span>
          </button>
        </div>
      </div>

      <!-- Guest Sync Banner -->
      <div
        v-if="!auth.isAuthenticated && favoritesStore.count > 0"
        class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-primary flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs"
      >
        <div class="flex items-center gap-2.5">
          <PhInfo :size="20" weight="bold" class="text-primary shrink-0" />
          <span class="leading-relaxed">
            Anda sedang melihat kos tersimpan sebagai <strong>tamu</strong>. Masuk ke akun Anda agar daftar favorit ini otomatis tersimpan secara permanen.
          </span>
        </div>
        <router-link
          to="/login?redirect=/favorit"
          class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-primary hover:bg-primary-hover text-white font-bold transition shadow-xs text-center shrink-0"
        >
          Masuk Sekarang
        </router-link>
      </div>

      <!-- Empty State -->
      <div
        v-if="favoritesStore.count === 0"
        class="bg-surface-container-lowest rounded-2xl border border-slate-100 p-12 text-center max-w-lg mx-auto shadow-xs my-8"
      >
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-error flex items-center justify-center mx-auto mb-4">
          <PhHeartBreak :size="32" weight="duotone" />
        </div>
        <h2 class="text-lg font-bold text-on-surface">Belum Ada Kos Tersimpan</h2>
        <p class="text-xs sm:text-sm text-on-surface-variant mt-2 leading-relaxed">
          Temukan hunian kos impian Anda dan tekan tombol ikon hati <strong class="text-on-surface">"Simpan"</strong> untuk menyimpannya di daftar favorit ini.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
          <router-link
            to="/kos"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold hover:bg-primary transition shadow-sm"
          >
            <PhMagnifyingGlass :size="16" weight="bold" />
            <span>Cari Kos Sekarang</span>
          </router-link>
          <router-link
            to="/peta"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-surface-container text-on-surface text-xs font-semibold hover:bg-surface-container-high transition"
          >
            <PhMapPin :size="16" weight="bold" />
            <span>Lihat di Peta</span>
          </router-link>
        </div>
      </div>

      <!-- Favorites Cards Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
        <article
          v-for="kos in favoritesStore.favorites"
          :key="kos.id"
          @click="router.push(`/kos/${kos.id}`)"
          class="bg-surface-container-lowest rounded-2xl shadow-xs hover:shadow-md transition-all flex flex-col overflow-hidden group border border-slate-100 cursor-pointer"
        >
          <!-- Thumbnail Image -->
          <div class="relative w-full aspect-[4/3] overflow-hidden bg-surface-container">
            <img
              :src="getImageForKos(kos)"
              :alt="kos.nama"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute top-3 left-3 flex items-center gap-1.5">
              <GenderBadge :jenis="kos.jenis" />
            </div>

            <!-- Remove from Favorite Button -->
            <button
              @click.stop.prevent="handleRemove(kos)"
              class="absolute top-3 right-3 min-w-[36px] min-h-[36px] rounded-full bg-surface-container-lowest/90 backdrop-blur-sm text-error hover:bg-error hover:text-white flex items-center justify-center shadow-sm transition-all cursor-pointer"
              title="Hapus dari Tersimpan"
              type="button"
            >
              <PhHeart :size="18" weight="fill" class="select-none" />
            </button>
          </div>

          <!-- Card Body -->
          <div class="p-space-md flex-1 flex flex-col justify-between space-y-space-sm">
            <div>
              <div class="flex items-center justify-between gap-space-xs mb-1">
                <router-link
                  :to="`/kos/${kos.id}`"
                  @click.stop
                  class="text-base font-bold text-on-surface group-hover:text-primary transition-colors truncate"
                >
                  {{ kos.nama }}
                </router-link>
                <div class="flex items-center gap-1 text-on-surface text-xs shrink-0">
                  <PhStar :size="14" weight="fill" class="text-amber-500 select-none" />
                  <span class="font-semibold">{{ kos.rating_rata_rata ? Number(kos.rating_rata_rata).toFixed(1) : 'Baru' }}</span>
                  <span v-if="kos.ulasan_count" class="text-[10px] text-slate-400 font-normal">({{ kos.ulasan_count }})</span>
                </div>
              </div>

              <div class="flex items-center gap-1 text-on-surface-variant text-xs mb-space-sm">
                <PhMapPin :size="14" weight="bold" class="text-primary select-none shrink-0" />
                <span class="truncate">{{ kos.alamat }}</span>
              </div>

              <!-- Facilities Badges -->
              <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span
                  v-for="fas in (kos.fasilitas || []).slice(0, 3)"
                  :key="fas.id"
                  class="inline-flex items-center gap-1 bg-surface-container-low text-primary text-[11px] font-semibold px-2.5 py-1 rounded-full"
                >
                  <PhCheckCircle :size="13" weight="bold" class="select-none shrink-0" />
                  <span>{{ fas.nama }}</span>
                </span>
                <span v-if="(kos.fasilitas || []).length > 3" class="text-[11px] text-on-surface-variant font-medium">
                  +{{ kos.fasilitas.length - 3 }}
                </span>
              </div>
            </div>

            <!-- Price & CTA -->
            <div class="pt-space-sm border-t border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-base font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                <span class="text-xs text-on-surface-variant font-normal"> / bln</span>
                <div v-if="kos.harga_harian || kos.harga_mingguan" class="flex items-center gap-1 mt-0.5">
                  <span v-if="kos.harga_harian" class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Bisa Harian</span>
                  <span v-if="kos.harga_mingguan" class="text-[9px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">Mingguan</span>
                </div>
              </div>

              <router-link
                :to="`/kos/${kos.id}`"
                @click.stop
                class="px-4 py-2 rounded-xl bg-primary-container text-white text-xs font-bold hover:bg-primary transition shadow-xs"
              >
                Lihat Detail
              </router-link>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import { useUiStore } from '../stores/ui'
import GenderBadge from '../components/GenderBadge.vue'
import {
  PhHeart,
  PhHeartBreak,
  PhStar,
  PhMapPin,
  PhCheckCircle,
  PhTrash,
  PhMagnifyingGlass,
  PhInfo
} from '@phosphor-icons/vue'

const router = useRouter()
const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const ui = useUiStore()

const samplePhotos = [
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBtAMGcNImL28Wkcksd1pB136MBTXtxAeUZZdLwny2E0TfcGGRkdrMA_JR6pVnktaHUu8Ffua13FdN3HdgZd29vhkeFDWM4WJtQJFMuZndi8o1HB2GXyn8ikhIgK45eAlZmqqiEVYkjMe7_m-Cvw0nxPY6cKLGKCrWp2Ch2dtBIlIqQb5pKjNVS1WaRhbnQPFyDftN7_uvVGXkv3Z_JUnsJpPxReDktVkZYw0eJ1YRW7caBLYvFdhslDw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCOitClHgLDKT01wozYNVTlNFfj9k7oJNfa4q2kNrDk86lv3RxjjBUoPS6XVwSRz1fJt2Njq_q5ULmoPtKJ6tl88vke1gmGCs0RASAks5YEI7qsJO_F2vpf0H79xaVOLcj6daJIZt4_vYQD98RpawFS-fn8uc4Gg8SAZ6U1phc_LSoddYK5bxVMFELhOTo3bzl4rS6xJsZs6VtkRf6E9koZW5_wbWiwXvorevgASvvlZRP5P-63L62c2Q',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCq7lWrhImOr_FOsjNzINLkTN7b_5naoC4qeVNpyv6ugH4fnh9cc_IfxyVcenUHoVsykW_bUzYT96tFdwcpt9ghlRd6ji-wQjUfun67OQOq3T1S6XB2tOmVgvzykn_ra-KV7mRpZLQ09KBeHgpQj_rkik9wD8A8tT5piCUPOvWmwULPLmec2WUaeEHWcK2rVuT-D0jdYE8LleRBoUP8Vwlz6LcasWJw3DEPRxzciDC7dsnsgoobvasOfw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBTwXv4Xzishf0BXTnGXeJGpf3six-aec8DUjhun4L1wAl49mDNbV4PPm9M7tACxaznDsA8efSHLFZXYg2Y2tqKbz4LG-PnJ_Izg77boMl0DFoZjIyMPJsP7f1m1TOy7WRSf9P9KWaENTrx0Q8oiX4WyyJUG--tFfpvnEr55wG2VWex3vG3bk3Oe7VB2DDfVDL3Hrqc01BGrDlFUw8nmuMIrr03zc8cscfCplekhP_8NAdixMSnIyhjFQ',
]

const getImageForKos = (kos) => {
  if (kos.foto && Array.isArray(kos.foto) && kos.foto.length > 0) {
    return kos.foto[0]
  }
  const idNum = Math.max(Number(kos.id) || 1, 1)
  return samplePhotos[(idNum - 1) % samplePhotos.length]
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const handleRemove = (kos) => {
  favoritesStore.removeFavorite(kos.id)
  ui.info(`Kos "${kos.nama}" dihapus dari favorit.`)
}

const confirmClearFavorites = async () => {
  const confirmed = await ui.confirm({
    title: 'Kosongkan Kos Tersimpan',
    message: 'Apakah Anda yakin ingin menghapus seluruh properti dari daftar favorit Anda?',
    confirmText: 'Ya, Hapus Semua',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  favoritesStore.clearFavorites()
  ui.info('Daftar kos tersimpan berhasil dikosongkan.')
}
</script>
