<template>
  <div class="max-w-7xl mx-auto px-margin py-space-md pt-20">
    <!-- Loading State -->
    <div v-if="loading" class="py-20 text-center animate-pulse">
      <div class="h-8 bg-surface-container w-64 mx-auto rounded-lg mb-4"></div>
      <div class="h-96 bg-surface-container-low max-w-4xl mx-auto rounded-xl"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="!kos" class="text-center py-20 bg-white rounded-2xl border border-slate-100">
      <h2 class="text-xl font-bold text-on-surface">Properti Kos Tidak Ditemukan</h2>
      <router-link to="/kos" class="mt-4 inline-block px-4 py-2 bg-primary-container text-white text-xs font-semibold rounded-lg">
        Kembali ke Daftar Kos
      </router-link>
    </div>

    <!-- Detail Content -->
    <div v-else>
      <!-- Breadcrumb & Top Actions -->
      <div class="flex flex-wrap items-center justify-between gap-space-sm mb-space-md">
        <nav class="flex items-center gap-space-xs text-xs text-on-surface-variant">
          <router-link to="/" class="hover:text-primary transition-colors">Beranda</router-link>
          <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          <router-link to="/kos" class="hover:text-primary transition-colors">Daftar Kos</router-link>
          <span class="material-symbols-outlined text-[16px]">chevron_right</span>
          <span class="text-on-surface font-semibold truncate max-w-[200px]">{{ kos.nama }}</span>
        </nav>

        <div class="flex items-center gap-space-sm">
          <button
            @click="shareKos"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface text-xs font-medium transition shadow-sm"
          >
            <span class="material-symbols-outlined text-[16px]">share</span>
            <span>Bagikan</span>
          </button>
          <button
            @click="toggleFavorite"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface text-xs font-medium transition shadow-sm"
          >
            <span class="material-symbols-outlined text-[16px]" :class="isFavorite ? 'text-error' : ''">
              {{ isFavorite ? 'favorite' : 'favorite_border' }}
            </span>
            <span>{{ isFavorite ? 'Tersimpan' : 'Simpan' }}</span>
          </button>
        </div>
      </div>

      <!-- Main Listing Layout: 2-Column Split -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <!-- Left Column: Gallery, Navigation Tabs, & Rich Sections (7 cols) -->
        <div class="lg:col-span-7 flex flex-col gap-space-lg">
          <!-- Media Gallery -->
          <section class="flex flex-col gap-space-sm">
            <!-- Main Hero Thumbnail -->
            <div class="relative w-full aspect-[16/10] sm:aspect-[16/9] rounded-xl overflow-hidden bg-surface-container shadow-md group">
              <img
                :src="activeGalleryImage"
                :alt="kos.nama"
                class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
              />
              <div class="absolute top-space-sm left-space-sm flex items-center gap-space-xs">
                <span
                  :class="[
                    'px-2.5 py-1 rounded-full text-[11px] font-semibold flex items-center gap-1 shadow-sm',
                    kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                    kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                    'bg-[#EDE9FE] text-[#6D28D9]'
                  ]"
                >
                  <span class="material-symbols-outlined text-[14px]">group</span>
                  Khusus {{ formatJenis(kos.jenis) }}
                </span>
                <span class="px-2.5 py-1 rounded-full bg-surface-container-lowest/90 backdrop-blur-sm text-tertiary text-[11px] font-semibold flex items-center gap-1 shadow-sm">
                  <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                  Terverifikasi
                </span>
              </div>
            </div>

            <!-- Thumbnail Navigation Strip -->
            <div class="grid grid-cols-4 gap-space-xs sm:gap-space-sm">
              <button
                v-for="(img, idx) in galleryImages"
                :key="idx"
                @click="activeGalleryIndex = idx"
                :class="[
                  'relative aspect-video rounded-lg overflow-hidden bg-surface-container shadow-sm transition-all',
                  activeGalleryIndex === idx ? 'ring-2 ring-primary opacity-100' : 'opacity-70 hover:opacity-100'
                ]"
              >
                <img :src="img" class="w-full h-full object-cover" />
              </button>
            </div>
          </section>

          <!-- Informational Section Tabs -->
          <div class="w-full bg-surface-container-lowest rounded-xl shadow-sm p-space-md sm:p-space-lg flex flex-col gap-space-lg border border-slate-100">
            <!-- Tab Bar -->
            <div class="flex items-center gap-space-xs bg-surface-container-low p-1 rounded-xl">
              <button
                v-for="tab in ['deskripsi', 'fasilitas', 'peraturan', 'lokasi', 'pemilik']"
                :key="tab"
                @click="activeTab = tab"
                :class="[
                  'flex-1 py-2 px-space-sm rounded-lg text-xs text-center transition-all capitalize',
                  activeTab === tab
                    ? 'bg-surface-container-lowest text-primary shadow-sm font-semibold'
                    : 'text-on-surface-variant hover:text-on-surface font-normal'
                ]"
              >
                {{ tab }}
              </button>
            </div>

            <!-- Tab Content: Deskripsi -->
            <div v-if="activeTab === 'deskripsi'" class="space-y-space-md">
              <div>
                <h3 class="text-base font-bold text-on-surface mb-2">Tentang Kos Ini</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed whitespace-pre-line">
                  {{ kos.deskripsi || 'Kos nyaman dengan lokasi strategis. Cocok untuk mahasiswa dan pekerja yang mendambakan suasana hunian tenang dan kondusif.' }}
                </p>
              </div>

              <!-- Rincian Spesifikasi Kos -->
              <div>
                <h4 class="text-xs font-semibold text-on-surface mb-2">Rincian Spesifikasi Kos</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm text-xs">
                  <div class="p-2.5 bg-surface-container-low rounded-lg flex items-center justify-between">
                    <span class="text-on-surface-variant">Kategori Hunian</span>
                    <span class="font-semibold text-primary capitalize">{{ kos.jenis }}</span>
                  </div>
                  <div class="p-2.5 bg-surface-container-low rounded-lg flex items-center justify-between">
                    <span class="text-on-surface-variant">Total Kamar</span>
                    <span class="font-semibold text-primary">{{ kos.kamar?.length || 0 }} Unit</span>
                  </div>
                  <div class="p-2.5 bg-surface-container-low rounded-lg flex items-center justify-between">
                    <span class="text-on-surface-variant">Tersedia Siap Huni</span>
                    <span class="font-semibold text-tertiary">{{ availableRoomsCount }} Kamar</span>
                  </div>
                  <div class="p-2.5 bg-surface-container-low rounded-lg flex items-center justify-between">
                    <span class="text-on-surface-variant">Periode Pembayaran</span>
                    <span class="font-semibold text-on-surface">Bulanan</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tab Content: Fasilitas -->
            <div v-else-if="activeTab === 'fasilitas'" class="space-y-space-md">
              <h3 class="text-base font-bold text-on-surface">Fasilitas yang Disediakan</h3>
              <div v-if="(kos.fasilitas || []).length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                <div
                  v-for="fas in kos.fasilitas"
                  :key="fas.id"
                  class="flex items-center gap-2 p-2.5 rounded-lg bg-surface-container-low text-xs text-on-surface"
                >
                  <span class="material-symbols-outlined text-primary text-base">check_circle</span>
                  <span class="font-medium">{{ fas.nama }}</span>
                </div>
              </div>
              <p v-else class="text-xs text-on-surface-variant italic">Belum ada rincian fasilitas terdaftar.</p>
            </div>

            <!-- Tab Content: Peraturan / Tata Tertib -->
            <div v-else-if="activeTab === 'peraturan'" class="space-y-space-md">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-base font-bold text-on-surface">Tata Tertib & Peraturan Kos</h3>
                  <p class="text-xs text-on-surface-variant">Harap dipatuhi oleh seluruh calon penghuni demi ketertiban bersama.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-surface-container text-primary">
                  {{ (kos.peraturan || []).length }} Aturan
                </span>
              </div>

              <div v-if="(kos.peraturan || []).length > 0" class="space-y-2">
                <div
                  v-for="(rule, idx) in kos.peraturan"
                  :key="idx"
                  class="flex items-start gap-2.5 p-3 rounded-xl bg-surface-container-low text-xs text-on-surface border border-slate-100"
                >
                  <span class="material-symbols-outlined text-emerald-600 text-[18px] shrink-0 mt-0.5">verified_user</span>
                  <span class="font-medium leading-relaxed">{{ rule }}</span>
                </div>
              </div>
              <div v-else class="p-4 rounded-xl bg-surface-container-low text-xs text-on-surface-variant italic text-center">
                Belum ada aturan tertulis khusus untuk kos ini. Berlaku norma kesopanan dan ketertiban umum.
              </div>

              <div class="p-3 bg-amber-50/70 border border-amber-200/60 rounded-xl flex items-center gap-2.5 text-xs text-amber-800">
                <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0">info</span>
                <span>Pelanggaran terhadap tata tertib kos dapat dikenai sanksi atau teguran oleh pemilik/pengelola kos.</span>
              </div>
            </div>

            <!-- Tab Content: Lokasi -->
            <div v-else-if="activeTab === 'lokasi'" class="space-y-space-md">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <h3 class="text-base font-bold text-on-surface mb-1">Titik Lokasi Properti</h3>
                  <p class="text-xs text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                    {{ kos.alamat }}
                  </p>
                </div>

                <!-- Open in Google Maps button -->
                <a
                  :href="getGoogleMapsUrl(kos)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition shadow-xs self-start sm:self-auto shrink-0"
                  title="Buka rute navigasi di Google Maps"
                >
                  <span class="material-symbols-outlined text-[16px]">near_me</span>
                  <span>Buka di Google Maps</span>
                  <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                </a>
              </div>

              <!-- Interactive Leaflet Map -->
              <div class="rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                <LeafletMap
                  :latitude="kos.latitude"
                  :longitude="kos.longitude"
                  :title="kos.nama"
                />
              </div>

              <!-- Navigation Directions Callout -->
              <div class="p-3 bg-surface-container-low/70 rounded-xl border border-slate-100 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 text-on-surface-variant">
                  <span class="material-symbols-outlined text-emerald-600 text-[18px]">directions</span>
                  <span>Ingin survei langsung ke lokasi kos ini? Buka rute panduan di Google Maps.</span>
                </div>
                <a
                  :href="getGoogleMapsUrl(kos)"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="font-bold text-emerald-700 hover:underline flex items-center gap-0.5 shrink-0"
                >
                  <span>Petunjuk Arah</span>
                  <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                </a>
              </div>
            </div>

            <!-- Tab Content: Pemilik -->
            <div v-else-if="activeTab === 'pemilik'" class="space-y-space-md">
              <h3 class="text-base font-bold text-on-surface">Pengelola / Pemilik Properti</h3>
              <div class="p-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                  <div class="w-14 h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg shrink-0">
                    {{ kos.pemilik?.nama ? kos.pemilik.nama.charAt(0).toUpperCase() : 'P' }}
                  </div>
                  <div class="space-y-1">
                    <div class="flex items-center gap-2">
                      <span class="font-bold text-sm text-on-surface">{{ kos.pemilik?.nama || 'Pemilik Kos' }}</span>
                      <span class="text-[10px] bg-tertiary-fixed text-on-tertiary-fixed-variant px-2 py-0.5 rounded-full font-semibold">
                        Pemilik Resmi
                      </span>
                    </div>
                    <p class="text-xs text-on-surface-variant">
                      Nomor WhatsApp / HP: <span class="font-semibold text-on-surface">{{ kos.pemilik?.telepon || kos.pemilik?.no_hp || '-' }}</span>
                    </p>
                    <p class="text-xs text-on-surface-variant">
                      Email: <span class="font-semibold text-on-surface">{{ kos.pemilik?.email || '-' }}</span>
                    </p>
                  </div>
                </div>

                <!-- Direct WhatsApp button in owner tab -->
                <button
                  type="button"
                  @click="handleWhatsAppInquiry"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition active:scale-95 shrink-0"
                >
                  <span class="material-symbols-outlined text-[18px]">chat</span>
                  <span>Chat WhatsApp</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Unit Kamar List -->
          <div class="w-full bg-surface-container-lowest rounded-xl shadow-sm p-space-md sm:p-space-lg border border-slate-100 space-y-space-md">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-base font-bold text-on-surface">Pilihan Unit Kamar</h3>
                <p class="text-xs text-on-surface-variant">Pilih nomor kamar untuk melanjutkan proses pemesanan.</p>
              </div>
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-surface-container text-primary">
                {{ availableRoomsCount }} Kamar Tersedia
              </span>
            </div>

            <div v-if="(kos.kamar || []).length === 0" class="text-center py-6 text-xs text-on-surface-variant italic">
              Belum ada data kamar terdaftar pada kos ini.
            </div>

            <div v-else class="space-y-2.5">
              <div
                v-for="kamar in kos.kamar"
                :key="kamar.id"
                :class="[
                  'p-3.5 rounded-xl border transition flex items-center justify-between cursor-pointer',
                  selectedKamar?.id === kamar.id
                    ? 'border-primary bg-primary/5 ring-1 ring-primary'
                    : 'border-slate-200 bg-white hover:border-slate-300'
                ]"
                @click="kamar.status === 'tersedia' ? selectKamar(kamar) : null"
              >
                <div class="flex items-center gap-3">
                  <div
                    v-if="kamar.foto && kamar.foto.length > 0"
                    class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden shrink-0 shadow-xs"
                  >
                    <img
                      :src="resolveImageUrl(kamar.foto[0])"
                      :alt="kamar.nama_kamar"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div class="space-y-1">
                    <div class="flex items-center gap-2">
                      <span class="text-sm font-bold text-on-surface">{{ kamar.nama_kamar }}</span>
                      <span
                        v-if="kamar.ukuran"
                        class="text-[10px] font-semibold px-2 py-0.5 rounded bg-surface-container text-primary"
                      >
                        {{ kamar.ukuran }}
                      </span>
                      <span
                        :class="[
                          'text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border',
                          kamar.status === 'tersedia'
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                            : 'bg-rose-50 text-rose-700 border-rose-200'
                        ]"
                      >
                        {{ kamar.status }}
                      </span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-bold text-primary">{{ formatRupiah(kamar.harga) }} / bulan</span>
                      <span
                        v-if="(kamar.fasilitas_kamar || []).length > 0"
                        class="text-[11px] text-on-surface-variant truncate max-w-xs"
                      >
                        &bull; {{ kamar.fasilitas_kamar.slice(0, 3).join(', ') }}
                      </span>
                    </div>
                  </div>
                </div>

                <div>
                  <button
                    v-if="kamar.status === 'tersedia'"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
                    :class="selectedKamar?.id === kamar.id ? 'bg-primary text-white' : 'bg-surface-container text-primary hover:bg-primary hover:text-white'"
                  >
                    {{ selectedKamar?.id === kamar.id ? 'Terpilih' : 'Pilih Kamar' }}
                  </button>
                  <span v-else class="text-xs text-slate-400 font-medium italic">Tidak Tersedia</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Sticky Booking Card (5 cols) -->
        <aside class="lg:col-span-5 lg:sticky lg:top-20 z-20">
          <div class="bg-surface-container-lowest rounded-xl shadow-md p-space-lg border border-slate-100 space-y-space-md">
            <div>
              <span class="text-xs text-on-surface-variant block">Mulai dari</span>
              <div class="flex items-baseline gap-1 mt-0.5">
                <span class="text-2xl font-black text-primary">{{ formatRupiah(currentSelectedPrice) }}</span>
                <span class="text-xs text-on-surface-variant font-normal">/ bulan</span>
              </div>
            </div>

            <!-- Selected Room Summary -->
            <div class="p-3 bg-surface-container-low rounded-lg space-y-1 text-xs">
              <div class="flex justify-between">
                <span class="text-on-surface-variant">Kamar Terpilih:</span>
                <span class="font-bold text-on-surface">{{ selectedKamar?.nama_kamar || 'Belum dipilih' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-on-surface-variant">Tipe Hunian:</span>
                <span class="font-semibold text-primary capitalize">{{ kos.jenis }}</span>
              </div>
            </div>

            <!-- Action Buttons: Sewa & WhatsApp Inquiry -->
            <div class="space-y-2">
              <button
                @click="handleProceedBooking"
                :disabled="!selectedKamar"
                class="w-full bg-primary-container hover:bg-primary text-white py-3 rounded-lg text-sm font-bold shadow-md transition disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <span class="material-symbols-outlined text-lg">receipt_long</span>
                <span>Ajukan Sewa Sekarang</span>
              </button>

              <button
                type="button"
                @click="handleWhatsAppInquiry"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg text-xs font-bold shadow-sm transition flex items-center justify-center gap-2"
                title="Tanya ketersediaan atau jadwal survei langsung ke pemilik via WhatsApp"
              >
                <span class="material-symbols-outlined text-base">chat</span>
                <span>Tanya Pemilik via WhatsApp</span>
              </button>
            </div>

            <p class="text-[11px] text-on-surface-variant text-center flex items-center justify-center gap-1">
              <span class="material-symbols-outlined text-xs text-tertiary">lock</span>
              <span>Transaksi aman dan diverifikasi langsung ke pemilik</span>
            </p>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LeafletMap from '../components/LeafletMap.vue'
import api from '../services/api'
import { openWhatsAppChat, buildInquiryTemplate, getGoogleMapsUrl } from '../utils/whatsapp'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const kos = ref(null)
const loading = ref(true)
const activeTab = ref('deskripsi')
const selectedKamar = ref(null)
const isFavorite = ref(false)

const defaultGalleryPlaceholders = [
  'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1000&q=80',
  'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=1000&q=80',
  'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1000&q=80',
  'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1000&q=80'
]

const resolveImageUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  const baseBackend = import.meta.env.VITE_API_URL
    ? import.meta.env.VITE_API_URL.replace(/\/api\/v1\/?$/, '')
    : 'https://backend-kos.ddev.site'
  return `${baseBackend}${url.startsWith('/') ? '' : '/'}${url}`
}

const galleryImages = computed(() => {
  if (kos.value?.foto && Array.isArray(kos.value.foto) && kos.value.foto.length > 0) {
    return kos.value.foto.map(resolveImageUrl)
  }
  return defaultGalleryPlaceholders
})

const activeGalleryIndex = ref(0)
const activeGalleryImage = computed(() => galleryImages.value[activeGalleryIndex.value] || galleryImages.value[0])

const availableRoomsCount = computed(() => {
  if (!kos.value?.kamar) return 0
  return kos.value.kamar.filter((k) => k.status === 'tersedia').length
})

const currentSelectedPrice = computed(() => {
  return selectedKamar.value ? selectedKamar.value.harga : kos.value?.harga
})

const formatJenis = (jenis) => {
  if (!jenis) return '-'
  return jenis.charAt(0).toUpperCase() + jenis.slice(1)
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const selectKamar = (kamar) => {
  selectedKamar.value = kamar
}

const shareKos = () => {
  if (navigator.share) {
    navigator.share({ title: kos.value.nama, url: window.location.href })
  } else {
    navigator.clipboard.writeText(window.location.href)
    alert('Tautan halaman kos berhasil disalin ke clipboard!')
  }
}

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
}

const handleProceedBooking = () => {
  if (!auth.isAuthenticated) {
    alert('Silakan masuk (login) terlebih dahulu untuk mengajukan sewa kos.')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  if (!auth.isPencari) {
    alert('Hanya akun dengan peran Pencari yang dapat mengajukan pemesanan kamar.')
    return
  }

  router.push(`/pesan/${kos.value.id}/${selectedKamar.value.id}`)
}

const handleWhatsAppInquiry = () => {
  const phone = kos.value?.pemilik?.telepon || kos.value?.pemilik?.no_hp
  const ownerName = kos.value?.pemilik?.nama || 'Pemilik Kos'
  const roomName = selectedKamar.value ? selectedKamar.value.nama_kamar : ''
  const message = buildInquiryTemplate({
    ownerName,
    kosName: kos.value?.nama || 'Kos',
    roomName,
    kosUrl: window.location.href,
  })

  openWhatsAppChat(phone, message)
}

const fetchKosDetail = async () => {
  loading.value = true
  try {
    const res = await api.get(`/kos/${route.params.id}`)
    kos.value = res.data.data

    // Otomatis pilih kamar pertama yang berstatus tersedia jika ada
    if (kos.value.kamar && kos.value.kamar.length > 0) {
      const firstAvailable = kos.value.kamar.find((k) => k.status === 'tersedia')
      if (firstAvailable) {
        selectedKamar.value = firstAvailable
      }
    }
  } catch (err) {
    kos.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchKosDetail()
})
</script>
