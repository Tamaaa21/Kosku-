<template>
  <div class="w-full max-w-7xl mx-auto px-margin py-space-xl pt-20">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-space-xs text-on-surface-variant mb-space-lg text-xs">
      <router-link to="/" class="hover:text-primary transition-colors">Beranda</router-link>
      <span class="material-symbols-outlined text-base">chevron_right</span>
      <span class="text-on-surface font-semibold">Profil Pengguna</span>
    </nav>

    <!-- Two-Column Profile Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
      <!-- LEFT SIDEBAR: Account Navigation & Quick Avatar Overview -->
      <aside class="lg:col-span-4 xl:col-span-4 flex flex-col gap-space-lg">
        <!-- User Avatar & Identity Card -->
        <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col items-center text-center border border-slate-100">
          <div class="relative group">
            <div class="w-24 h-24 rounded-full overflow-hidden shadow-sm bg-surface-container p-1 border-2 border-primary/20 flex items-center justify-center">
              <span class="material-symbols-outlined text-5xl text-primary select-none">person</span>
            </div>
          </div>

          <h3 class="mt-space-md text-lg font-bold text-on-surface">{{ auth.user?.nama }}</h3>
          <p class="text-xs text-on-surface-variant flex items-center justify-center gap-1 mt-0.5">
            <span class="material-symbols-outlined text-sm text-tertiary">verified</span>
            Akun Terverifikasi
          </p>

          <!-- Quick Stat Chips -->
          <div class="w-full grid grid-cols-2 gap-space-xs mt-space-lg pt-space-md bg-surface-container-low/60 rounded-lg p-space-sm text-xs">
            <div class="text-left px-space-xs">
              <span class="text-on-surface-variant block text-[11px]">Peran Akun</span>
              <span class="font-bold text-primary capitalize mt-0.5 block">
                {{ auth.user?.role }}
              </span>
            </div>
            <div class="text-left px-space-xs">
              <span class="text-on-surface-variant block text-[11px]">Status</span>
              <span class="font-bold text-tertiary flex items-center gap-1 mt-0.5">
                <span class="w-2 h-2 rounded-full bg-tertiary"></span> Aktif
              </span>
            </div>
          </div>
        </div>

        <!-- Vertical Menu Navigation -->
        <div class="bg-surface-container-lowest rounded-xl p-space-sm shadow-sm border border-slate-100">
          <nav class="flex flex-col gap-1 text-xs">
            <!-- Profil (Active) -->
            <button
              class="flex items-center gap-space-md px-space-md py-3 rounded-lg bg-surface-container-low text-primary font-semibold transition-all text-left"
            >
              <span class="material-symbols-outlined text-[20px] text-primary" style="font-variation-settings: 'FILL' 1;">person</span>
              <span class="flex-1">Profil Saya</span>
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>

            <!-- Riwayat Pemesanan (Pencari) -->
            <router-link
              v-if="auth.isPencari"
              to="/pesanan"
              class="flex items-center gap-space-md px-space-md py-3 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors"
            >
              <span class="material-symbols-outlined text-[20px]">calendar_month</span>
              <span class="flex-1">Riwayat Pemesanan</span>
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </router-link>

            <!-- Dashboard Shortcut (Pemilik/Admin) -->
            <router-link
              v-if="auth.isPemilik"
              to="/dashboard/pemilik"
              class="flex items-center gap-space-md px-space-md py-3 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors"
            >
              <span class="material-symbols-outlined text-[20px]">dashboard</span>
              <span class="flex-1">Dashboard Properti</span>
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </router-link>

            <router-link
              v-if="auth.isAdmin"
              to="/dashboard/admin"
              class="flex items-center gap-space-md px-space-md py-3 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors"
            >
              <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
              <span class="flex-1">Panel Administrator</span>
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </router-link>

            <div class="h-px bg-surface-container my-space-xs"></div>

            <!-- Keluar / Logout -->
            <button
              @click="handleLogout"
              class="w-full flex items-center gap-space-md px-space-md py-3 rounded-lg text-error hover:bg-error-container/40 transition-colors font-semibold text-left"
              type="button"
            >
              <span class="material-symbols-outlined text-[20px] text-error">logout</span>
              <span class="flex-1">Keluar / Logout</span>
            </button>
          </nav>
        </div>
      </aside>

      <!-- RIGHT MAIN CONTENT: Profile Details Form -->
      <main class="lg:col-span-8 xl:col-span-8 bg-surface-container-lowest rounded-xl p-space-lg shadow-sm border border-slate-100 space-y-space-lg">
        <div class="border-b border-slate-100 pb-4">
          <h2 class="text-xl font-bold text-on-surface">Informasi Akun Pengguna</h2>
          <p class="text-xs text-on-surface-variant mt-1">
            Data pribadi Anda digunakan untuk proses pemesanan dan verifikasi hunian kos.
          </p>
        </div>

        <form class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-on-surface mb-1">Nama Lengkap</label>
            <input
              type="text"
              :value="auth.user?.nama"
              disabled
              class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-700 p-2.5"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-on-surface mb-1">Alamat Email</label>
              <input
                type="email"
                :value="auth.user?.email"
                disabled
                class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-700 p-2.5"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-on-surface mb-1">Nomor Telepon / WhatsApp</label>
              <input
                type="tel"
                :value="auth.user?.no_hp"
                disabled
                class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-700 p-2.5"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-on-surface mb-1">Peran Akun Sistem</label>
            <input
              type="text"
              :value="auth.user?.role?.toUpperCase()"
              disabled
              class="w-full text-xs rounded-lg border-slate-200 bg-slate-50 text-slate-700 p-2.5 font-bold tracking-wide"
            />
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-on-surface-variant flex items-center gap-1">
              <span class="material-symbols-outlined text-sm text-tertiary">lock</span>
              Data tersinkronisasi dengan REST API KosKu
            </span>
            <button
              type="button"
              @click="auth.fetchMe"
              class="px-4 py-2 bg-surface-container hover:bg-surface-container-high text-primary text-xs font-semibold rounded-lg transition"
            >
              Segarkan Data Akun
            </button>
          </div>
        </form>
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await auth.logout()
  router.push({ name: 'home' })
}
</script>
