<template>
  <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-space-xl pt-20">
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
      <aside class="lg:col-span-5 bg-surface-container-low p-4 sm:p-6 lg:p-space-lg flex flex-col justify-between">
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
      <section class="lg:col-span-7 p-4 sm:p-6 lg:p-space-xl flex flex-col justify-between bg-surface-container-lowest">
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

            <!-- Pilihan Metode Transaksi (Dual Mode) -->
            <div class="space-y-2.5">
              <label class="block text-xs font-semibold text-on-surface">
                Pilih Metode Transaksi <span class="text-error">*</span>
              </label>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Opsi 1: Manual (Transaksi Langsung dengan Pemilik) -->
                <div
                  @click="form.metode_transaksi = 'manual'"
                  :class="[
                    'p-3.5 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between relative',
                    form.metode_transaksi === 'manual'
                      ? 'border-primary bg-primary/5 shadow-xs'
                      : 'border-slate-200 hover:border-slate-300 bg-white'
                  ]"
                >
                  <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                      <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <PhHandshake :size="18" weight="bold" />
                      </div>
                      <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        Rekomendasi
                      </span>
                    </div>
                    <h4 class="text-xs font-bold text-on-surface">Transaksi Langsung</h4>
                    <p class="text-[11px] text-on-surface-variant leading-relaxed">
                      Bayar langsung ke pemilik via transfer bank atau tunai/COD saat check-in. Konfirmasi langsung oleh pemilik kos.
                    </p>
                  </div>
                  <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Biaya Layanan:</span>
                    <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                  </div>
                </div>

                <!-- Opsi 2: Midtrans Online (DP 20%) -->
                <div
                  @click="form.metode_transaksi = 'midtrans'"
                  :class="[
                    'p-3.5 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between relative',
                    form.metode_transaksi === 'midtrans'
                      ? 'border-primary bg-primary/5 shadow-xs'
                      : 'border-slate-200 hover:border-slate-300 bg-white'
                  ]"
                >
                  <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                      <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <PhCreditCard :size="18" weight="bold" />
                      </div>
                      <span class="text-[10px] font-bold text-primary bg-primary/10 border border-primary/20 px-2 py-0.5 rounded-full">
                        Gateway Online
                      </span>
                    </div>
                    <h4 class="text-xs font-bold text-on-surface">Midtrans Gateway (DP 20%)</h4>
                    <p class="text-[11px] text-on-surface-variant leading-relaxed">
                      Kunci kamar otomatis dengan bayar uang muka DP 20% secara online (Virtual Account, QRIS, GoPay, CC).
                    </p>
                  </div>
                  <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Biaya Gateway:</span>
                    <span class="font-bold text-slate-700">{{ formatRupiah(gatewayFee) }}</span>
                  </div>
                </div>
              </div>
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
                      :max="maxDuration"
                      required
                      class="w-full text-center text-xs border-y border-slate-200 p-2 bg-white font-bold"
                    />
                    <button
                      type="button"
                      @click="form.durasi_jumlah = Math.min(maxDuration, form.durasi_jumlah + 1)"
                      class="px-3 py-2 bg-slate-200 hover:bg-slate-300 rounded-r-lg text-xs font-bold text-slate-700 transition"
                    >
                      +
                    </button>
                  </div>
                </div>
              </div>

              <!-- Rincian Perhitungan Biaya Sesuai Metode Transaksi -->
              <div class="pt-2 border-t border-slate-200/80 space-y-2 text-xs text-slate-600">
                <div class="flex justify-between">
                  <span>Tarif sewa per {{ form.durasi_tipe }}:</span>
                  <span class="font-bold text-slate-900">{{ formatRupiah(ratePerUnit) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Perkiraan Selesai (Check-out):</span>
                  <span class="font-bold text-slate-900">{{ estimatedEndDate }}</span>
                </div>
                <div class="flex justify-between pt-1 border-t border-slate-200 text-xs font-semibold text-slate-700">
                  <span>Total Nilai Sewa:</span>
                  <span class="font-bold text-slate-900">{{ formatRupiah(totalEstimatedPrice) }}</span>
                </div>

                <!-- Box untuk Mode Manual / Langsung -->
                <div v-if="form.metode_transaksi === 'manual'" class="p-3 bg-emerald-50/70 rounded-xl border border-emerald-200 space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-emerald-900">Total Biaya Sewa:</span>
                    <span class="font-bold text-emerald-900">{{ formatRupiah(totalEstimatedPrice) }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[11px] text-emerald-700">
                    <span>Biaya Layanan Gateway:</span>
                    <span class="font-bold text-emerald-800">Gratis (Rp 0)</span>
                  </div>
                  <div class="flex items-center justify-between pt-1 border-t border-emerald-200 text-xs font-black text-emerald-900">
                    <span>Total Tagihan:</span>
                    <span>{{ formatRupiah(totalEstimatedPrice) }}</span>
                  </div>
                  <p class="text-[11px] text-emerald-800 pt-1 leading-relaxed">
                    * <strong>Metode Langsung:</strong> Pembayaran dilakukan langsung ke pemilik (Transfer/Tunai saat check-in). Reservasi akan aktif setelah disetujui pemilik kos.
                  </p>
                </div>

                <!-- Box untuk Mode Midtrans (DP 20%) -->
                <div v-else class="p-3 bg-primary/5 rounded-xl border border-primary/20 space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-primary">Uang Muka / DP (20%):</span>
                    <span class="font-bold text-primary">{{ formatRupiah(dpAmount) }}</span>
                  </div>
                  <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <span>Biaya Layanan Gateway:</span>
                    <span>{{ formatRupiah(gatewayFee) }}</span>
                  </div>
                  <div class="flex items-center justify-between pt-1 border-t border-primary/20 text-xs font-black text-primary">
                    <span>Total Tagihan DP Sekarang:</span>
                    <span>{{ formatRupiah(totalDpPayable) }}</span>
                  </div>
                  <div class="flex items-center justify-between pt-1 text-[11px] text-slate-600">
                    <span>Sisa Pelunasan (80% saat masuk):</span>
                    <span class="font-bold text-slate-800">{{ formatRupiah(remainingBalance) }}</span>
                  </div>
                  <p class="text-[11px] text-slate-500 pt-1 leading-relaxed">
                    * <strong>Sistem Hibrida Midtrans:</strong> Cukup bayar DP 20% sekarang untuk mengunci kamar. Sisa pelunasan 80% dibayarkan saat serah terima kunci.
                  </p>
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
              class="w-full bg-primary-container hover:bg-primary text-white py-3 rounded-xl text-xs sm:text-sm font-bold shadow-md transition disabled:opacity-50 mt-4 flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99] touch-manipulation"
            >
              <component :is="form.metode_transaksi === 'manual' ? PhPaperPlaneTilt : PhCreditCard" :size="16" weight="bold" />
              <span>
                {{
                  submitting
                    ? (form.metode_transaksi === 'manual' ? 'Mengajukan Sewa...' : 'Menyiapkan Midtrans...')
                    : (form.metode_transaksi === 'manual'
                        ? 'Ajukan Permohonan Sewa Langsung'
                        : `Bayar DP & Kunci Kamar (${formatRupiah(totalDpPayable)})`)
                }}
              </span>
            </button>
          </form>
        </div>
      </section>
    </div>

    <!-- Modal Sukses Pengajuan Sewa Langsung -->
    <div
      v-if="manualSuccessBooking"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-200">
        <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
          <PhCheckCircle :size="36" weight="fill" />
        </div>

        <div class="text-center space-y-1">
          <h3 class="text-lg font-bold text-on-surface">Permohonan Sewa Terkirim!</h3>
          <p class="text-xs text-on-surface-variant leading-relaxed">
            Permohonan Anda untuk kamar <strong>{{ kamar?.nama_kamar }}</strong> di <strong>{{ kos?.nama }}</strong> berhasil diajukan dengan ID Reservasi <strong>#ORD-{{ String(manualSuccessBooking.id).padStart(4, '0') }}</strong>.
          </p>
        </div>

        <div class="p-3 bg-surface-container-low rounded-xl text-xs space-y-1.5 border border-slate-100">
          <div class="flex justify-between text-on-surface-variant">
            <span>Pemilik Kos:</span>
            <span class="font-bold text-on-surface">{{ kos?.pemilik?.nama || '-' }}</span>
          </div>
          <div class="flex justify-between text-on-surface-variant">
            <span>Total Nilai Sewa:</span>
            <span class="font-bold text-primary">{{ formatRupiah(manualSuccessBooking.total_harga || totalEstimatedPrice) }}</span>
          </div>
          <div class="flex justify-between text-on-surface-variant">
            <span>Metode Transaksi:</span>
            <span class="font-semibold text-emerald-700">Langsung ke Pemilik (COD/Transfer)</span>
          </div>
        </div>

        <p class="text-[11px] text-slate-500 text-center leading-relaxed">
          Segera hubungi pemilik melalui WhatsApp untuk mengonfirmasi ketersediaan dan kesepakatan pembayaran agar kamar segera disetujui.
        </p>

        <div class="space-y-2 pt-2">
          <button
            type="button"
            @click="contactOwnerWA"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center gap-2 cursor-pointer active:scale-95"
          >
            <PhWhatsappLogo :size="18" weight="fill" />
            <span>Hubungi Pemilik via WhatsApp</span>
          </button>

          <button
            type="button"
            @click="goToOrders"
            class="w-full bg-surface-container-high hover:bg-surface-container-highest text-on-surface py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-center cursor-pointer"
          >
            <span>Buka Halaman Pesanan Saya</span>
          </button>
        </div>
      </div>
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
  PhCreditCard,
  PhPaperPlaneTilt,
  PhHandshake,
  PhWhatsappLogo,
  PhInfo,
} from '@phosphor-icons/vue'
import { payWithSnap } from '../utils/midtrans'
import { openWhatsAppChat, buildTenantInquiryTemplate } from '../utils/whatsapp'

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
const manualSuccessBooking = ref(null)

const todayDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

const maxDuration = computed(() => {
  if (form.durasi_tipe === 'harian') return 30
  if (form.durasi_tipe === 'mingguan') return 52
  return 36
})

const form = reactive({
  tanggal_pemesanan: new Date().toISOString().split('T')[0],
  durasi_tipe: route.query.durasi || 'bulanan',
  durasi_jumlah: 1,
  metode_transaksi: 'manual',
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

const dpPercentage = 20
const gatewayFee = 4000

const dpAmount = computed(() => {
  return Math.round((totalEstimatedPrice.value * dpPercentage) / 100)
})

const remainingBalance = computed(() => {
  return Math.max(0, totalEstimatedPrice.value - dpAmount.value)
})

const totalDpPayable = computed(() => {
  return dpAmount.value + gatewayFee
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
    const resBooking = await api.post('/bookings', {
      kamar_id: Number(route.params.kamarId),
      tanggal_pemesanan: form.tanggal_pemesanan,
      durasi_tipe: form.durasi_tipe,
      durasi_jumlah: Number(form.durasi_jumlah),
      metode_transaksi: form.metode_transaksi,
    })

    const bookingData = resBooking.data.data
    const bookingId = bookingData.id

    // Jika metode langsung, tampilkan dialog sukses dan direct WhatsApp
    if (form.metode_transaksi === 'manual') {
      manualSuccessBooking.value = bookingData
      ui.success('Permohonan sewa berhasil diajukan!')
      return
    }

    // Request Snap DP checkout untuk mode Midtrans
    ui.info('Menyiapkan gerbang pembayaran Midtrans...')
    const resCheckout = await api.post(`/bookings/${bookingId}/checkout-dp`)
    const snapToken = resCheckout.data.data.snap_token

    if (snapToken) {
      await payWithSnap(snapToken, {
        onSuccess: async () => {
          try {
            await api.post(`/bookings/${bookingId}/sync-status`)
          } catch (_) {}
          ui.success('Pembayaran DP berhasil! Kamar Anda telah resmi terpesan.')
          router.push('/pesanan')
        },
        onPending: async () => {
          try {
            await api.post(`/bookings/${bookingId}/sync-status`)
          } catch (_) {}
          ui.info('Menunggu penyelesaian pembayaran DP. Anda dapat memantau statusnya di menu Pesanan.')
          router.push('/pesanan')
        },
        onError: () => {
          ui.warning('Pembayaran gagal atau dibatalkan. Anda dapat mencoba kembali di halaman Pesanan.')
          router.push('/pesanan')
        },
        onClose: async () => {
          try {
            await api.post(`/bookings/${bookingId}/sync-status`)
          } catch (_) {}
          ui.info('Jendela pembayaran ditutup. Anda dapat melanjutkan pembayaran kapan saja di menu Pesanan.')
          router.push('/pesanan')
        },
      })
    } else {
      ui.success('Permohonan pemesanan kamar berhasil diajukan!')
      router.push('/pesanan')
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message || err.message || 'Gagal memproses pemesanan kamar.'
  } finally {
    submitting.value = false
  }
}

const contactOwnerWA = () => {
  const phone = kos.value?.pemilik?.telepon || kos.value?.pemilik?.no_hp
  if (!phone) {
    ui.info('Nomor WhatsApp pemilik kos belum dicantumkan.')
    goToOrders()
    return
  }
  const message = buildTenantInquiryTemplate({
    ownerName: kos.value?.pemilik?.nama || 'Pemilik Kos',
    kosName: kos.value?.nama || 'Properti Kos',
    roomName: kamar.value?.nama_kamar || '',
    bookingId: manualSuccessBooking.value?.id,
  })
  openWhatsAppChat(phone, message)
  goToOrders()
}

const goToOrders = () => {
  manualSuccessBooking.value = null
  router.push('/pesanan')
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
