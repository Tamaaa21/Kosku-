<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- Header & Quick Nav -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md mb-6">
      <div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">
          Selamat Datang, {{ auth.user?.nama }}! 👋
        </h1>
        <p class="text-xs text-on-surface-variant mt-0.5">
          Pantau analitik okupansi, tren reservasi sewa, dan kinerja finansial properti kos Anda.
        </p>
      </div>

      <!-- Quick Action Buttons -->
      
    </div>

    <!-- Alert Banner Permintaan Menunggu (Hanya Tampil Jika Ada Pending) -->
    <div
      v-if="pendingCount > 0"
      class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs"
    >
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[24px]">notifications_active</span>
        </div>
        <div>
          <h3 class="text-xs font-bold text-amber-950">
            Ada {{ pendingCount }} Permintaan Sewa Baru Menunggu Konfirmasi Anda
          </h3>
          <p class="text-[11px] text-amber-800/80 mt-0.5">
            Calon penghuni sedang menunggu persetujuan ketersediaan kamar dan jadwal masuk kos.
          </p>
        </div>
      </div>
      <router-link
        to="/dashboard/pemilik/pemesanan"
        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition flex items-center gap-1.5 self-start sm:self-auto shrink-0 shadow-xs"
      >
        <span>Tinjau Pemesanan</span>
        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
      </router-link>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mb-8">
      <!-- 1. Total Kos Aktif -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs border border-slate-100 flex items-center justify-between">
        <div>
          <span class="text-xs font-semibold text-on-surface-variant block">Total Properti Kos</span>
          <div class="mt-1 flex items-baseline gap-1.5">
            <span class="text-2xl font-black text-on-surface">{{ myKosList.length }}</span>
            <span class="text-xs text-secondary font-medium">Properti</span>
          </div>
          <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-flex items-center gap-0.5">
            <span class="material-symbols-outlined text-[13px]">verified</span> Aktif di listing
          </span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary shrink-0">
          <span class="material-symbols-outlined text-[24px]">domain</span>
        </div>
      </div>

      <!-- 2. Tingkat Okupansi -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs border border-slate-100 flex items-center justify-between">
        <div>
          <span class="text-xs font-semibold text-on-surface-variant block">Tingkat Okupansi</span>
          <div class="mt-1 flex items-baseline gap-1.5">
            <span class="text-2xl font-black text-primary">{{ occupancyRate }}%</span>
            <span class="text-xs text-secondary font-medium">Terisi</span>
          </div>
          <span class="text-[11px] text-slate-500 font-medium mt-1 block">
            {{ totalFilledRooms }} dari {{ totalRoomsCount }} unit kamar
          </span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
          <span class="material-symbols-outlined text-[24px]">bed</span>
        </div>
      </div>

      <!-- 3. Estimasi Pendapatan Berjalan -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs border border-slate-100 flex items-center justify-between">
        <div>
          <span class="text-xs font-semibold text-on-surface-variant block">Estimasi Omset Aktif</span>
          <div class="mt-1 flex items-baseline gap-1">
            <span class="text-lg font-black text-emerald-700 truncate max-w-[170px]" :title="formatRupiah(monthlyRevenueEstimate)">
              {{ formatRupiah(monthlyRevenueEstimate) }}
            </span>
          </div>
          <span class="text-[11px] text-slate-400 font-medium mt-1 block">
            Berdasarkan unit terisi / bln
          </span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 shrink-0">
          <span class="material-symbols-outlined text-[24px]">payments</span>
        </div>
      </div>

      <!-- 4. Total Reservasi Masuk -->
      <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs border border-slate-100 flex items-center justify-between">
        <div>
          <span class="text-xs font-semibold text-on-surface-variant block">Total Reservasi</span>
          <div class="mt-1 flex items-baseline gap-1.5">
            <span class="text-2xl font-black text-on-surface">{{ bookingsList.length }}</span>
            <span class="text-xs text-secondary font-medium">Transaksi</span>
          </div>
          <span class="text-[11px] text-slate-500 font-medium mt-1 block">
            {{ approvedBookingsCount }} disetujui &bull; {{ pendingCount }} menunggu
          </span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary-container shrink-0">
          <span class="material-symbols-outlined text-[24px]">receipt_long</span>
        </div>
      </div>
    </div>

    <!-- Empty State jika belum memiliki kos sama sekali -->
    <div
      v-if="!loading && myKosList.length === 0"
      class="text-center py-16 bg-surface-container-lowest rounded-2xl border border-slate-100 mb-8"
    >
      <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-3">
        <span class="material-symbols-outlined text-[32px]">apartment</span>
      </div>
      <h3 class="text-base font-bold text-on-surface">Mulai Daftarkan Properti Kos Anda</h3>
      <p class="text-xs text-on-surface-variant max-w-md mx-auto mt-1 mb-5">
        Anda belum memiliki data kos terdaftar. Tambahkan properti kos baru untuk mulai memantau okupansi kamar dan menerima permohonan sewa.
      </p>
      <router-link
        to="/dashboard/pemilik/kos"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-container text-white text-xs font-bold rounded-xl shadow-md hover:bg-primary transition"
      >
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah Kos Pertama</span>
      </router-link>
    </div>

    <!-- SECTION ANALYTICS CHARTS (Hanya jika kos sudah ada) -->
    <div v-else class="space-y-6">
      <!-- ROW 1: Tren Reservasi (8 cols) & Okupansi Kamar (4 cols) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Chart 1: Tren Reservasi SVG Area Line Chart (8 cols) -->
        <div class="lg:col-span-8 bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-slate-100 flex flex-col justify-between">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">trending_up</span>
                <h2 class="text-base font-bold text-on-surface">Tren Aktivitas Pemesanan</h2>
              </div>
              <p class="text-xs text-on-surface-variant mt-0.5">
                Pantau pergerakan reservasi sewa properti Anda dalam rentang waktu terpilih
              </p>
            </div>

            <!-- Periode Toggle Pills -->
            <div class="flex items-center gap-1.5 self-start sm:self-auto bg-slate-100 p-1 rounded-xl">
              <button
                type="button"
                @click="trendDaysFilter = 7"
                :class="[
                  'px-3 py-1 rounded-lg text-xs font-bold transition',
                  trendDaysFilter === 7 ? 'bg-white text-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'
                ]"
              >
                7 Hari
              </button>
              <button
                type="button"
                @click="trendDaysFilter = 14"
                :class="[
                  'px-3 py-1 rounded-lg text-xs font-bold transition',
                  trendDaysFilter === 14 ? 'bg-white text-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'
                ]"
              >
                14 Hari
              </button>
              <button
                type="button"
                @click="trendDaysFilter = 30"
                :class="[
                  'px-3 py-1 rounded-lg text-xs font-bold transition',
                  trendDaysFilter === 30 ? 'bg-white text-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'
                ]"
              >
                30 Hari
              </button>
            </div>
          </div>

          <!-- Total Highlight Tag -->
          <div class="flex items-center gap-2 mb-4">
            <span class="text-xs text-slate-500 font-medium">Total Masuk Periode Ini:</span>
            <span class="text-xs font-extrabold text-primary bg-primary/10 px-2.5 py-0.5 rounded-full">
              {{ totalTrendBookings }} Permintaan
            </span>
          </div>

          <!-- SVG Responsive Curve Chart -->
          <div class="relative w-full h-52">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 500 140" preserveAspectRatio="none">
              <defs>
                <linearGradient id="ownerChartGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#1570EF" stop-opacity="0.30" />
                  <stop offset="100%" stop-color="#1570EF" stop-opacity="0.0" />
                </linearGradient>
              </defs>
              <!-- Grid Lines -->
              <line x1="20" y1="20" x2="480" y2="20" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
              <line x1="20" y1="65" x2="480" y2="65" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
              <line x1="20" y1="115" x2="480" y2="115" stroke="#e2e8f0" stroke-width="1" />

              <!-- Area Fill -->
              <polygon v-if="chartAreaPath" :points="chartAreaPath" fill="url(#ownerChartGradient)" />

              <!-- Polyline Line -->
              <polyline
                v-if="chartLinePath"
                :points="chartLinePath"
                fill="none"
                stroke="#1570EF"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
              />

              <!-- Circles & Dots -->
              <g v-for="(p, idx) in chartPoints" :key="idx">
                <circle
                  :cx="p.x"
                  :cy="p.y"
                  r="4.5"
                  fill="#ffffff"
                  stroke="#1570EF"
                  stroke-width="2.5"
                  class="transition-all hover:r-6 cursor-pointer"
                />
                <text
                  v-if="p.count > 0 || trendDaysFilter === 7"
                  :x="p.x"
                  :y="p.y - 8"
                  text-anchor="middle"
                  class="text-[10px] font-bold fill-slate-700 select-none"
                >
                  {{ p.count }}
                </text>
              </g>
            </svg>
          </div>

          <!-- Date Labels Below Chart -->
          <div class="flex justify-between text-[10px] text-slate-400 font-semibold px-2 mt-2 select-none">
            <span v-for="(item, idx) in trendDataLabels" :key="idx">
              {{ item }}
            </span>
          </div>
        </div>

        <!-- Chart 2: Rasio Okupansi Kamar Donut Chart (4 cols) -->
        <div class="lg:col-span-4 bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-slate-100 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="material-symbols-outlined text-emerald-600 text-[20px]">pie_chart</span>
              <h2 class="text-base font-bold text-on-surface">Rasio Okupansi Kamar</h2>
            </div>
            <p class="text-xs text-on-surface-variant">Keterisian unit kamar seluruh properti</p>

            <!-- Donut SVG Gauge -->
            <div class="relative w-40 h-40 mx-auto my-5 flex items-center justify-center">
              <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 120 120">
                <!-- Background Circle -->
                <circle
                  cx="60"
                  cy="60"
                  r="50"
                  stroke="#f1f5f9"
                  stroke-width="12"
                  fill="transparent"
                />
                <!-- Active Arc -->
                <circle
                  cx="60"
                  cy="60"
                  r="50"
                  stroke="#10b981"
                  stroke-width="12"
                  fill="transparent"
                  stroke-linecap="round"
                  :stroke-dasharray="donutCircumference"
                  :stroke-dashoffset="donutOffset"
                  class="transition-all duration-1000 ease-out"
                />
              </svg>
              <!-- Center Text -->
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <span class="text-2xl font-black text-slate-900 leading-none">{{ occupancyRate }}%</span>
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-1">Okupansi</span>
              </div>
            </div>

            <!-- Legend Info List -->
            <div class="space-y-2.5 pt-2 border-t border-slate-100">
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                  <span class="text-slate-600 font-medium">Kamar Terisi</span>
                </div>
                <span class="font-bold text-slate-900">{{ totalFilledRooms }} Kamar</span>
              </div>

              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                  <span class="text-slate-600 font-medium">Kamar Siap Huni</span>
                </div>
                <span class="font-bold text-slate-900">{{ totalAvailableRooms }} Kamar</span>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 text-xs flex items-center justify-between text-slate-500">
            <span>Potensi Omset 100%:</span>
            <span class="font-extrabold text-slate-800">{{ formatRupiah(potentialMaxRevenue) }}</span>
          </div>
        </div>
      </div>

      <!-- ROW 2: Preferensi Durasi Sewa (4 cols) & Aktivitas Reservasi Masuk Terkini (8 cols) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- Chart 3: Distribusi Durasi Sewa (4 cols) -->
        <div class="lg:col-span-4 bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-slate-100 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="material-symbols-outlined text-amber-600 text-[20px]">date_range</span>
              <h2 class="text-base font-bold text-on-surface">Pilihan Durasi Sewa</h2>
            </div>
            <p class="text-xs text-on-surface-variant mb-5">
              Preferensi durasi sewa yang diajukan oleh calon penyewa
            </p>

            <div class="space-y-4">
              <!-- Bulanan -->
              <div>
                <div class="flex justify-between items-center text-xs mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span class="font-bold text-slate-700">Sewa Bulanan</span>
                  </div>
                  <span class="font-extrabold text-slate-900">
                    {{ durasiStats.bulanan.count }} pesanan
                    <span class="text-slate-400 font-normal">({{ durasiStats.bulanan.pct }}%)</span>
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="{ width: `${durasiStats.bulanan.pct}%` }"></div>
                </div>
              </div>

              <!-- Mingguan -->
              <div>
                <div class="flex justify-between items-center text-xs mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="font-bold text-slate-700">Sewa Mingguan</span>
                  </div>
                  <span class="font-extrabold text-slate-900">
                    {{ durasiStats.mingguan.count }} pesanan
                    <span class="text-slate-400 font-normal">({{ durasiStats.mingguan.pct }}%)</span>
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" :style="{ width: `${durasiStats.mingguan.pct}%` }"></div>
                </div>
              </div>

              <!-- Harian -->
              <div>
                <div class="flex justify-between items-center text-xs mb-1.5">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-slate-700">Sewa Harian</span>
                  </div>
                  <span class="font-extrabold text-slate-900">
                    {{ durasiStats.harian.count }} pesanan
                    <span class="text-slate-400 font-normal">({{ durasiStats.harian.pct }}%)</span>
                  </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" :style="{ width: `${durasiStats.harian.pct}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
            <span>Paling Diminati:</span>
            <span class="font-bold text-primary capitalize">{{ topDurasiType }}</span>
          </div>
        </div>

        <!-- Tabel Ramping 5 Pemesanan Terkini (8 cols) -->
        <div class="lg:col-span-8 bg-surface-container-lowest rounded-2xl p-6 shadow-xs border border-slate-100 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-4">
              <div>
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary text-[20px]">history</span>
                  <h2 class="text-base font-bold text-on-surface">Aktivitas Pemesanan Terkini</h2>
                </div>
                <p class="text-xs text-on-surface-variant mt-0.5">
                  5 permohonan sewa terbaru yang masuk ke properti Anda
                </p>
              </div>
              <router-link
                to="/dashboard/pemilik/pemesanan"
                class="text-xs text-primary font-bold hover:underline flex items-center gap-0.5"
              >
                <span>Kelola Semua</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
              </router-link>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs border-collapse">
                <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                  <tr>
                    <th class="py-2.5 px-3">Order</th>
                    <th class="py-2.5 px-3">Penyewa</th>
                    <th class="py-2.5 px-3">Kos & Kamar</th>
                    <th class="py-2.5 px-3">Durasi</th>
                    <th class="py-2.5 px-3">Total Biaya</th>
                    <th class="py-2.5 px-3 text-center">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                  <tr v-if="recentBookings.length === 0">
                    <td colspan="6" class="py-8 text-center text-xs text-slate-400">
                      Belum ada riwayat aktivitas pemesanan masuk.
                    </td>
                  </tr>
                  <tr
                    v-for="b in recentBookings"
                    :key="b.id"
                    class="hover:bg-slate-50/70 transition"
                  >
                    <td class="py-3 px-3 font-mono font-bold text-slate-900">
                      #{{ b.id }}
                    </td>
                    <td class="py-3 px-3 font-semibold text-slate-800">
                      {{ b.pengguna?.nama || '-' }}
                    </td>
                    <td class="py-3 px-3">
                      <div class="font-medium text-slate-900">{{ b.kos?.nama || '-' }}</div>
                      <div class="text-[10px] text-slate-400">{{ b.kamar?.nama_kamar || '-' }}</div>
                    </td>
                    <td class="py-3 px-3 font-medium text-slate-600">
                      {{ formatDurasi(b) }}
                    </td>
                    <td class="py-3 px-3 font-bold text-emerald-700">
                      {{ formatRupiah(b.total_harga || b.kamar?.harga) }}
                    </td>
                    <td class="py-3 px-3 text-center">
                      <span
                        :class="[
                          'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize',
                          b.status === 'menunggu' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                          b.status === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                          'bg-rose-50 text-rose-700 border border-rose-200'
                        ]"
                      >
                        {{ b.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Daftar permohonan lengkap beserta kontak WhatsApp penyewa</span>
            <router-link
              to="/dashboard/pemilik/pemesanan"
              class="font-bold text-primary hover:underline"
            >
              Buka Halaman Permintaan &rarr;
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const auth = useAuthStore()

const myKosList = ref([])
const bookingsList = ref([])
const loading = ref(false)
const trendDaysFilter = ref(7)

// 1. Rooms & Occupancy Calculations
const totalRoomsCount = computed(() => {
  return myKosList.value.reduce((acc, k) => acc + (k.kamar ? k.kamar.length : 0), 0)
})

const totalAvailableRooms = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    if (!k.kamar) return acc
    return acc + k.kamar.filter((r) => r.status === 'tersedia').length
  }, 0)
})

const totalFilledRooms = computed(() => {
  return totalRoomsCount.value - totalAvailableRooms.value
})

const occupancyRate = computed(() => {
  if (totalRoomsCount.value === 0) return 0
  return Math.round((totalFilledRooms.value / totalRoomsCount.value) * 100)
})

// Circular Donut Gauge Math
const donutCircumference = 2 * Math.PI * 50 // ~314.16
const donutOffset = computed(() => {
  return donutCircumference - (donutCircumference * (occupancyRate.value / 100))
})

// 2. Financial Metrics
const monthlyRevenueEstimate = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    if (!k.kamar || k.kamar.length === 0) return acc
    return (
      acc +
      k.kamar.reduce((kAcc, room) => {
        return kAcc + (room.status === 'terisi' ? (room.harga || k.harga || 0) : 0)
      }, 0)
    )
  }, 0)
})

const potentialMaxRevenue = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    if (!k.kamar || k.kamar.length === 0) return acc
    return (
      acc +
      k.kamar.reduce((kAcc, room) => {
        return kAcc + (room.harga || k.harga || 0)
      }, 0)
    )
  }, 0)
})

// 3. Booking Stats
const pendingCount = computed(() => {
  return bookingsList.value.filter((b) => b.status === 'menunggu').length
})

const approvedBookingsCount = computed(() => {
  return bookingsList.value.filter((b) => b.status === 'disetujui').length
})

const recentBookings = computed(() => {
  return [...bookingsList.value].sort((a, b) => a.id - b.id).slice(0, 5)
})

// 4. Trend Chart Calculation
const trendData = computed(() => {
  const days = trendDaysFilter.value
  const result = []
  const today = new Date()

  for (let i = days - 1; i >= 0; i--) {
    const d = new Date()
    d.setDate(today.getDate() - i)
    const dateStr = d.toISOString().slice(0, 10)

    const label = d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: days <= 14 ? 'short' : 'numeric'
    })

    const count = bookingsList.value.filter((b) => {
      const bDate = b.created_at ? b.created_at.slice(0, 10) : ''
      return bDate === dateStr
    }).length

    result.push({ date: dateStr, label, count })
  }
  return result
})

const chartPoints = computed(() => {
  const trend = trendData.value
  if (trend.length === 0) return []
  const maxVal = Math.max(...trend.map((d) => d.count), 4)
  const left = 30
  const right = 470
  const top = 20
  const bottom = 115

  return trend.map((d, i) => {
    const x = trend.length > 1 ? left + (i / (trend.length - 1)) * (right - left) : 250
    const y = bottom - (d.count / maxVal) * (bottom - top)
    return { x, y, count: d.count, label: d.label }
  })
})

const chartLinePath = computed(() => {
  if (chartPoints.value.length === 0) return ''
  return chartPoints.value.map((p) => `${p.x},${p.y}`).join(' ')
})

const chartAreaPath = computed(() => {
  if (chartPoints.value.length === 0) return ''
  const bottom = 115
  const first = chartPoints.value[0]
  const last = chartPoints.value[chartPoints.value.length - 1]
  const lineCoords = chartPoints.value.map((p) => `${p.x},${p.y}`).join(' ')
  return `${first.x},${bottom} ${lineCoords} ${last.x},${bottom}`
})

const totalTrendBookings = computed(() => {
  return trendData.value.reduce((acc, d) => acc + d.count, 0)
})

const trendDataLabels = computed(() => {
  const trend = trendData.value
  if (trendDaysFilter.value === 7) {
    return trend.map((t) => t.label)
  }
  // Step labels for 14 or 30 days
  const step = Math.ceil(trend.length / 6)
  return trend.filter((_, idx) => idx % step === 0 || idx === trend.length - 1).map((t) => t.label)
})

// 5. Durasi Sewa Preferences Breakdown
const durasiStats = computed(() => {
  const total = bookingsList.value.length
  if (total === 0) {
    return {
      bulanan: { count: 0, pct: 0 },
      mingguan: { count: 0, pct: 0 },
      harian: { count: 0, pct: 0 }
    }
  }

  const bulanan = bookingsList.value.filter((b) => !b.durasi_tipe || b.durasi_tipe === 'bulanan').length
  const mingguan = bookingsList.value.filter((b) => b.durasi_tipe === 'mingguan').length
  const harian = bookingsList.value.filter((b) => b.durasi_tipe === 'harian').length

  return {
    bulanan: { count: bulanan, pct: Math.round((bulanan / total) * 100) },
    mingguan: { count: mingguan, pct: Math.round((mingguan / total) * 100) },
    harian: { count: harian, pct: Math.round((harian / total) * 100) }
  }
})

const topDurasiType = computed(() => {
  const stats = durasiStats.value
  if (stats.bulanan.count >= stats.mingguan.count && stats.bulanan.count >= stats.harian.count) {
    return 'Bulanan'
  }
  if (stats.mingguan.count >= stats.bulanan.count && stats.mingguan.count >= stats.harian.count) {
    return 'Mingguan'
  }
  return 'Harian'
})

// Formatters
const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val)
}

const formatDurasi = (b) => {
  if (!b) return '-'
  if (b.durasi_tipe && b.durasi_jumlah) {
    const unit = b.durasi_tipe.charAt(0).toUpperCase() + b.durasi_tipe.slice(1)
    return `${b.durasi_jumlah} ${unit}`
  }
  const bln = b.durasi_sewa_bulan || b.durasi_bulan || 1
  return `${bln} Bulan`
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const [resKos, resBookings] = await Promise.all([
      api.get('/kos', { params: { pemilik_id: auth.user?.id, per_page: 50 } }),
      api.get('/pemesanan')
    ])
    myKosList.value = resKos.data.data || []
    bookingsList.value = resBookings.data.data || []
  } catch (err) {
    console.error('Gagal memuat analitik dashboard:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
