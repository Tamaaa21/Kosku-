<template>
  <div class="min-h-screen bg-slate-50 flex flex-col font-sans text-slate-800">
    <TenantNavbar />

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 bg-gradient-to-b from-[#017E91]/10 via-slate-50 to-slate-50 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#017E91]/10 text-[#017E91] border border-[#017E91]/20 mb-6">
          <PhCompass :size="16" weight="bold" />
          Mengenal Platform Kostara
        </span>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight max-w-3xl mx-auto leading-tight">
          Solusi Cerdas Pencarian & Reservasi Kos Modern di Indonesia
        </h1>
        <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
          Kostara menjembatani pencari hunian sewa dengan mitra pemilik kos tepercaya melalui integrasi peta lokasi OpenStreetMap, transparansi ketersediaan kamar, dan transaksi reservasi terproteksi.
        </p>
      </div>
    </section>

    <!-- Visi & Misi Cards -->
    <section class="py-12 bg-white border-y border-slate-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Visi -->
          <div class="p-8 rounded-2xl bg-gradient-to-br from-slate-50 to-blue-50/30 border border-slate-100 shadow-sm flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-[#017E91]/10 text-[#017E91] flex items-center justify-center mb-6">
              <PhEye :size="28" weight="bold" />
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Visi Kami</h2>
            <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
              Menjadi ekosistem digital terdepan di Indonesia dalam penyediaan layanan reservasi hunian indekos yang aman, transparan, dan inklusif bagi seluruh mahasiswa, profesional, dan keluarga.
            </p>
          </div>

          <!-- Misi -->
          <div class="p-8 rounded-2xl bg-gradient-to-br from-slate-50 to-amber-50/30 border border-slate-100 shadow-sm flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-[#E68202]/10 text-[#E68202] flex items-center justify-center mb-6">
              <PhTarget :size="28" weight="bold" />
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-3">Misi Kami</h2>
            <ul class="space-y-2 text-slate-600 text-sm sm:text-base">
              <li class="flex items-start gap-2">
                <PhCheckCircle :size="20" class="text-emerald-600 shrink-0 mt-0.5" weight="fill" />
                <span>Mengeliminasi risiko miskomunikasi dan konflik bentrok pemesanan (*double-booking*).</span>
              </li>
              <li class="flex items-start gap-2">
                <PhCheckCircle :size="20" class="text-emerald-600 shrink-0 mt-0.5" weight="fill" />
                <span>Menyediakan akurasi lokasi geocoding dengan data GPS interaktif berbasis peta digital.</span>
              </li>
              <li class="flex items-start gap-2">
                <PhCheckCircle :size="20" class="text-emerald-600 shrink-0 mt-0.5" weight="fill" />
                <span>Mendukung digitalisasi operasional mitra pemilik kos melalui panel analitik dan moderasi transparan.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Keunggulan Kostara -->
    <section class="py-16 bg-slate-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Mengapa Memilih Kostara?</h2>
          <p class="mt-2 text-sm sm:text-base text-slate-600">Teknologi modern yang dirancang untuk kenyamanan pencari dan pemilik kos.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#017E91] flex items-center justify-center mb-4">
              <PhShieldCheck :size="24" weight="bold" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Verifikasi Kepemilikan</h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              Seluruh properti kos melewati tinjauan moderasi admin dan verifikasi berkas bukti kepemilikan untuk mencegah penipuan listing fiktif.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
              <PhLockKey :size="24" weight="bold" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Transaksi Atomik</h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              Sistem reservasi didukung mekanisme database transaction dan pessimistic locking untuk mengunci ketersediaan unit kamar secara *real-time*.
            </p>
          </div>

          <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-[#E68202] flex items-center justify-center mb-4">
              <PhMapPin :size="24" weight="bold" />
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Geocoding Otomatis</h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              Integrasi OpenStreetMap Nominatim mendeteksi koordinat lintang dan bujur properti langsung dari teks alamat kos.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer Simple -->
    <footer class="mt-auto py-8 bg-white border-t border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
        <p>© 2026 Kostara (KosKu) &middot; Hak Cipta Dilindungi Undang-Undang.</p>
        <div class="flex items-center gap-6">
          <router-link to="/" class="hover:text-primary transition">Beranda</router-link>
          <router-link to="/kos" class="hover:text-primary transition">Cari Kos</router-link>
          <router-link to="/peta" class="hover:text-primary transition">Peta</router-link>
          <router-link to="/about" class="text-primary font-semibold">Tentang Kami</router-link>
        </div>
      </div>
    </footer>

    <BackToTop />
  </div>
</template>

<script setup>
import TenantNavbar from '../components/TenantNavbar.vue'
import BackToTop from '../components/BackToTop.vue'
import {
  PhCompass,
  PhEye,
  PhTarget,
  PhCheckCircle,
  PhShieldCheck,
  PhLockKey,
  PhMapPin
} from '@phosphor-icons/vue'
</script>
