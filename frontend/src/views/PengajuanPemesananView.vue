<template>
  <div class="w-full max-w-5xl mx-auto px-margin py-space-xl pt-20">
    <!-- Breadcrumb & Nav Back Top -->
    <div class="mb-space-lg flex items-center justify-between">
      <router-link
        :to="kos ? `/kos/${kos.id}` : '/kos'"
        class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-primary transition-colors text-xs font-semibold group"
      >
        <span class="material-symbols-outlined text-sm transition-transform group-hover:-translate-x-1">arrow_back</span>
        <span>Kembali ke Detail Kos</span>
      </router-link>

      <div class="hidden sm:flex items-center gap-space-xs text-on-surface-variant text-xs">
        <span>Detail Kos</span>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-primary font-semibold">Pengajuan Sewa</span>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="h-96 bg-white rounded-xl border border-slate-100 animate-pulse"></div>

    <!-- Error State -->
    <div v-else-if="!kos || !kamar" class="text-center py-16 bg-white rounded-xl border border-slate-100">
      <p class="text-xs text-on-surface-variant">Data kos atau kamar tidak valid.</p>
      <router-link to="/kos" class="mt-4 inline-block text-xs font-bold text-primary hover:underline">
        Pilih Kos Lainnya
      </router-link>
    </div>

    <!-- Main Booking Card Wrap (2 Columns Split) -->
    <div v-else class="bg-surface-container-lowest rounded-xl shadow-md overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-0 border border-slate-100">
      <!-- Kolom Kiri: Ringkasan Kos (Sticky Summary Card) -->
      <aside class="lg:col-span-5 bg-surface-container-low p-space-lg flex flex-col justify-between">
        <div class="space-y-space-md">
          <!-- Property Hero Visual -->
          <div class="relative overflow-hidden rounded-lg shadow-sm group">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTwXv4Xzishf0BXTnGXeJGpf3six-aec8DUjhun4L1wAl49mDNbV4PPm9M7tACxaznDsA8efSHLFZXYg2Y2tqKbz4LG-PnJ_Izg77boMl0DFoZjIyMPJsP7f1m1TOy7WRSf9P9KWaENTrx0Q8oiX4WyyJUG--tFfpvnEr55wG2VWex3vG3bk3Oe7VB2DDfVDL3Hrqc01BGrDlFUw8nmuMIrr03zc8cscfCplekhP_8NAdixMSnIyhjFQ"
              :alt="kos.nama"
              class="w-full h-48 object-cover object-center group-hover:scale-105 transition-transform duration-500"
            />
            <span
              :class="[
                'absolute top-space-sm left-space-sm px-2.5 py-0.5 rounded-full text-[11px] font-semibold shadow-sm',
                kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                'bg-[#EDE9FE] text-[#6D28D9]'
              ]"
            >
              {{ formatJenis(kos.jenis) }}
            </span>
            <span class="absolute bottom-space-sm right-space-sm px-2 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-lowest/90 backdrop-blur-sm text-on-surface flex items-center gap-1 shadow-sm">
              <span class="material-symbols-outlined text-xs text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
              <span>4.8 (Terverifikasi)</span>
            </span>
          </div>

          <!-- Kos Identity Details -->
          <div>
            <div class="flex items-start justify-between gap-space-xs">
              <h2 class="text-lg font-bold text-on-surface tracking-tight">{{ kos.nama }}</h2>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-tertiary-fixed text-on-tertiary-fixed-variant">
                Terverifikasi
              </span>
            </div>
            <p class="text-xs text-on-surface-variant flex items-center gap-1 mt-1">
              <span class="material-symbols-outlined text-sm text-primary">location_on</span>
              <span>{{ kos.alamat }}</span>
            </p>
          </div>

          <!-- Price Display -->
          <div class="p-space-sm rounded-lg bg-surface-container-lowest shadow-sm flex items-baseline justify-between border border-slate-100">
            <span class="text-xs text-on-surface-variant">Kamar {{ kamar.nama_kamar }}</span>
            <div class="text-right">
              <span class="text-xl font-black text-primary tracking-tight">{{ formatRupiah(kamar.harga) }}</span>
              <span class="text-xs text-on-surface-variant">/ bulan</span>
            </div>
          </div>

          <!-- Key Facilities Badges -->
          <div>
            <span class="text-xs text-on-surface-variant block mb-2 font-medium">Fasilitas Kos:</span>
            <div class="flex flex-wrap gap-1.5">
              <span
                v-for="fas in (kos.fasilitas || []).slice(0, 4)"
                :key="fas.id"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-surface-container text-primary"
              >
                <span class="material-symbols-outlined text-[13px]">check_circle</span>
                <span>{{ fas.nama }}</span>
              </span>
            </div>
          </div>

          <!-- Pemilik Info -->
          <div class="flex items-center gap-2 p-2.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs">
            <span class="material-symbols-outlined text-base text-emerald-600">verified_user</span>
            <p>Dikelola oleh <strong class="font-semibold">{{ kos.pemilik?.nama }}</strong></p>
          </div>
        </div>
      </aside>

      <!-- Kolom Kanan: Formulir Pemesanan -->
      <section class="lg:col-span-7 p-space-lg lg:p-space-xl flex flex-col justify-between bg-surface-container-lowest">
        <div>
          <!-- Header Form -->
          <div class="mb-space-lg">
            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold text-primary bg-primary-fixed mb-1.5">
              <span class="material-symbols-outlined text-xs">receipt_long</span>
              Langkah Terakhir
            </div>
            <h1 class="text-2xl font-bold text-on-surface tracking-tight">Formulir Pemesanan</h1>
            <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">
              Permintaan pemesanan akan dikirim ke pemilik kos. Anda akan mendapat pemberitahuan setelah diproses dan dikonfirmasi.
            </p>
          </div>

          <!-- Alert Error -->
          <div v-if="errorMsg" class="mb-4 p-3 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-200">
            {{ errorMsg }}
          </div>

          <!-- Booking Form -->
          <form @submit.prevent="submitBooking" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-on-surface mb-1">Nama Penyewa</label>
              <input
                type="text"
                :value="auth.user?.nama"
                disabled
                class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-500 p-2.5 cursor-not-allowed"
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-on-surface mb-1">Email</label>
                <input
                  type="email"
                  :value="auth.user?.email"
                  disabled
                  class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-500 p-2.5 cursor-not-allowed"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-on-surface mb-1">No. WhatsApp</label>
                <input
                  type="tel"
                  :value="auth.user?.no_hp"
                  disabled
                  class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-500 p-2.5 cursor-not-allowed"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-on-surface mb-1">
                Rencana Tanggal Mulai Masuk (Check-in) <span class="text-error">*</span>
              </label>
              <input
                type="date"
                v-model="form.tanggal_pemesanan"
                :min="todayDate"
                required
                class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
              />
              <p class="text-[11px] text-on-surface-variant mt-1">Pilih tanggal mulai menetap di kamar kos ini.</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-on-surface mb-1">Durasi Sewa</label>
              <select class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 p-2.5" disabled>
                <option value="1">1 Bulan (Perpanjangan otomatis setiap bulan)</option>
              </select>
            </div>

            <div class="pt-2">
              <label class="flex items-start gap-2.5 cursor-pointer text-xs text-on-surface-variant select-none">
                <input
                  type="checkbox"
                  v-model="agreeTerms"
                  required
                  class="rounded text-primary focus:ring-primary mt-0.5"
                />
                <span>Saya menyetujui peraturan tata tertib kos dan menyatakan data permohonan sewa ini adalah benar.</span>
              </label>
            </div>

            <button
              type="submit"
              :disabled="submitting || !agreeTerms"
              class="w-full bg-primary-container hover:bg-primary text-white py-3 rounded-lg text-xs font-bold shadow-md transition disabled:opacity-50 mt-4 flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined text-base">send</span>
              <span>{{ submitting ? 'Mengirim Pengajuan...' : 'Kirim Pengajuan Sewa Sekarang' }}</span>
            </button>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const kos = ref(null)
const kamar = ref(null)
const loading = ref(true)
const submitting = ref(false)
const errorMsg = ref(null)
const agreeTerms = ref(true)

const todayDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const form = reactive({
  tanggal_pemesanan: new Date().toISOString().split('T')[0],
})

const formatJenis = (jenis) => {
  if (!jenis) return '-'
  return jenis.charAt(0).toUpperCase() + jenis.slice(1)
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const submitBooking = async () => {
  submitting.value = true
  errorMsg.value = null
  try {
    await api.post('/pemesanan', {
      kamar_id: Number(route.params.kamarId),
      tanggal_pemesanan: form.tanggal_pemesanan,
    })
    alert('Permohonan pemesanan kamar berhasil diajukan! Anda dapat memantau statusnya di halaman Riwayat Pesanan.')
    router.push('/pesanan')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal mengajukan pemesanan kamar.'
  } finally {
    submitting.value = false
  }
}

const fetchData = async () => {
  loading.value = true
  try {
    const resKos = await api.get(`/kos/${route.params.kosId}`)
    kos.value = resKos.data.data

    const kamarId = Number(route.params.kamarId)
    kamar.value = (kos.value.kamar || []).find((k) => k.id === kamarId) || null
  } catch (err) {
    kos.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>
