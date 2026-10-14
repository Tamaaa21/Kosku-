<template>
  <div class="w-full max-w-5xl mx-auto px-margin py-space-xl pt-20">
    <!-- Nav Back Top -->
    <div class="mb-space-lg">
      <router-link
        :to="kos ? `/kos/${kos.id}` : '/kos'"
        class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-primary transition-colors text-xs font-semibold group"
      >
        <PhArrowLeft :size="14" weight="bold" class="transition-transform group-hover:-translate-x-1" />
        <span>Kembali ke Detail Kos</span>
      </router-link>
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
            <GenderBadge :jenis="kos.jenis" size="sm" class="absolute top-space-sm left-space-sm" />
            <span class="absolute bottom-space-sm right-space-sm px-2 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-lowest/90 backdrop-blur-sm text-on-surface flex items-center gap-1 shadow-sm">
              <PhStar :size="12" weight="fill" class="text-amber-500" />
              <span>{{ kos.rating_rata_rata ? Number(kos.rating_rata_rata).toFixed(1) : 'Baru' }}</span>
              <span v-if="kos.ulasan_count" class="text-slate-400 font-normal">({{ kos.ulasan_count }} ulasan)</span>
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
              <PhMapPin :size="14" weight="bold" class="text-primary shrink-0" />
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
                <PhCheckCircle :size="13" weight="bold" class="shrink-0" />
                <span>{{ fas.nama }}</span>
              </span>
            </div>
          </div>

          <!-- Pemilik Info -->
          <div class="flex items-center gap-2 p-2.5 rounded-lg bg-emerald-50 text-emerald-800 text-xs">
            <PhShieldCheck :size="16" weight="bold" class="text-emerald-600 shrink-0" />
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
              <PhReceipt :size="12" weight="bold" />
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

            <!-- Pilihan Durasi & Jumlah Durasi -->
            <div class="space-y-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-on-surface mb-1">
                    Tipe Durasi Sewa <span class="text-error">*</span>
                  </label>
                  <select
                    v-model="form.durasi_tipe"
                    class="w-full text-xs rounded-lg border-slate-200 bg-white focus:ring-primary focus:border-primary p-2.5"
                  >
                    <option
                      v-for="opt in availableDurationOptions"
                      :key="opt.value"
                      :value="opt.value"
                    >
                      {{ opt.label }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-on-surface mb-1">
                    Jumlah Durasi ({{ form.durasi_tipe }}) <span class="text-error">*</span>
                  </label>
                  <div class="flex items-center">
                    <button
                      type="button"
                      @click="form.durasi_jumlah = Math.max(1, form.durasi_jumlah - 1)"
                      class="px-3 py-2 bg-slate-200 hover:bg-slate-300 rounded-l-lg text-xs font-bold text-slate-700 transition"
                    >
                      -
                    </button>
                    <input
                      type="number"
                      v-model.number="form.durasi_jumlah"
                      min="1"
                      max="365"
                      required
                      class="w-full text-center text-xs border-y border-slate-200 p-2 bg-white font-bold"
                    />
                    <button
                      type="button"
                      @click="form.durasi_jumlah = form.durasi_jumlah + 1"
                      class="px-3 py-2 bg-slate-200 hover:bg-slate-300 rounded-r-lg text-xs font-bold text-slate-700 transition"
                    >
                      +
                    </button>
                  </div>
                </div>
              </div>

              <!-- Rincian Perhitungan Biaya -->
              <div class="pt-2 border-t border-slate-200/80 space-y-1.5 text-xs text-slate-600">
                <div class="flex justify-between">
                  <span>Tarif per {{ form.durasi_tipe }}:</span>
                  <span class="font-bold text-slate-900">{{ formatRupiah(ratePerUnit) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Perkiraan Selesai (Check-out):</span>
                  <span class="font-bold text-slate-900">{{ estimatedEndDate }}</span>
                </div>
                <div class="flex justify-between pt-1 border-t border-dashed border-slate-300 text-sm font-extrabold text-primary">
                  <span>Total Estimasi Biaya:</span>
                  <span>{{ formatRupiah(totalEstimatedPrice) }}</span>
                </div>
              </div>
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
              <PhPaperPlaneTilt :size="16" weight="bold" />
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
import { useUiStore } from '../stores/ui'
import GenderBadge from '../components/GenderBadge.vue'
import api from '../services/api'
import {
  PhArrowLeft,
  PhStar,
  PhMapPin,
  PhCheckCircle,
  PhShieldCheck,
  PhReceipt,
  PhPaperPlaneTilt
} from '@phosphor-icons/vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const ui = useUiStore()

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
  durasi_tipe: route.query.durasi || 'bulanan',
  durasi_jumlah: 1,
})

const availableDurationOptions = computed(() => {
  const options = [{ value: 'bulanan', label: `Bulanan (${formatRupiah(kamar.value?.harga || 0)} / bln)` }]
  const roomDurations = kamar.value?.durasi_sewa || kos.value?.durasi_sewa || ['bulanan']

  if (roomDurations.includes('mingguan') || kamar.value?.harga_mingguan) {
    options.push({
      value: 'mingguan',
      label: `Mingguan (${formatRupiah(kamar.value?.harga_mingguan || Math.ceil((kamar.value?.harga || 0) / 4))} / mgg)`
    })
  }
  if (roomDurations.includes('harian') || kamar.value?.harga_harian) {
    options.push({
      value: 'harian',
      label: `Harian (${formatRupiah(kamar.value?.harga_harian || Math.ceil((kamar.value?.harga || 0) / 30))} / hari)`
    })
  }
  return options
})

const ratePerUnit = computed(() => {
  if (!kamar.value) return 0
  if (form.durasi_tipe === 'harian') {
    return kamar.value.harga_harian || Math.ceil(kamar.value.harga / 30)
  }
  if (form.durasi_tipe === 'mingguan') {
    return kamar.value.harga_mingguan || Math.ceil(kamar.value.harga / 4)
  }
  return kamar.value.harga
})

const totalEstimatedPrice = computed(() => {
  return ratePerUnit.value * (Number(form.durasi_jumlah) || 1)
})

const estimatedEndDate = computed(() => {
  if (!form.tanggal_pemesanan) return '-'
  const d = new Date(form.tanggal_pemesanan)
  const count = Number(form.durasi_jumlah) || 1
  if (form.durasi_tipe === 'harian') {
    d.setDate(d.getDate() + count)
  } else if (form.durasi_tipe === 'mingguan') {
    d.setDate(d.getDate() + (count * 7))
  } else {
    d.setMonth(d.getMonth() + count)
  }
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
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
      durasi_tipe: form.durasi_tipe,
      durasi_jumlah: Number(form.durasi_jumlah),
    })
    ui.success('Permohonan pemesanan kamar berhasil diajukan! Anda dapat memantau statusnya di halaman Riwayat Pesanan.')
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
