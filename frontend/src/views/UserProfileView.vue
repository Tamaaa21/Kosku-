<template>
  <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
    <!-- Alert / Toast Messages -->
    <div
      v-if="toastMsg"
      class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200 flex items-center justify-between shadow-xs transition-all"
    >
      <div class="flex items-center gap-2">
        <PhCheckCircle :size="18" weight="fill" class="text-emerald-600 shrink-0" />
        <span>{{ toastMsg }}</span>
      </div>
      <button @click="toastMsg = ''" class="text-emerald-700 font-bold ml-4 text-base hover:opacity-75 cursor-pointer">&times;</button>
    </div>

    <div
      v-if="errorToastMsg"
      class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200 flex items-center justify-between shadow-xs transition-all"
    >
      <div class="flex items-center gap-2">
        <PhWarningCircle :size="18" weight="fill" class="text-rose-600 shrink-0" />
        <span>{{ errorToastMsg }}</span>
      </div>
      <button @click="errorToastMsg = ''" class="text-rose-700 font-bold ml-4 text-base hover:opacity-75 cursor-pointer">&times;</button>
    </div>

    <!-- ================================================================= -->
    <!-- HALAMAN 1: PROFIL (Ringkasan akun dan menu pengaturan)             -->
    <!-- ================================================================= -->
    <div v-if="currentPage === 'profil'" class="space-y-6">
      <!-- Header Page -->
      

      <!-- 1. Ringkasan Akun Card (Identitas Pengguna & Ringkasan Status) -->
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Identity Box -->
        <div class="bg-gradient-to-br from-blue-50/90 via-slate-50/50 to-white p-6 sm:p-7 flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
          <!-- Large Avatar with Camera Upload Trigger -->
          <div class="relative group shrink-0">
            <div class="w-22 h-22 sm:w-24 sm:h-24 rounded-full overflow-hidden shadow-md ring-4 ring-white bg-[#1E3A8A] text-white flex items-center justify-center relative">
              <img
                v-if="avatarPreview || auth.user?.foto"
                :src="avatarPreview || auth.user.foto"
                :alt="auth.user?.nama"
                class="w-full h-full object-cover"
              />
              <span v-else-if="auth.user?.nama" class="text-3xl font-black">
                {{ auth.user.nama.charAt(0).toUpperCase() }}
              </span>
              <PhUser v-else :size="48" weight="bold" class="text-white select-none" />

              <!-- Uploading Spinner Overlay -->
              <div
                v-if="uploadingAvatar"
                class="absolute inset-0 bg-slate-900/70 backdrop-blur-2xs flex flex-col items-center justify-center text-white"
              >
                <PhCircleNotch :size="22" class="animate-spin text-white" />
                <span class="text-[9px] mt-1 font-bold">Mengunggah...</span>
              </div>
            </div>

            <button
              type="button"
              @click="triggerFileInput"
              :disabled="uploadingAvatar"
              class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-slate-900 hover:bg-slate-800 text-white shadow-md flex items-center justify-center border-2 border-white transition cursor-pointer active:scale-95 disabled:opacity-50"
              title="Ganti Foto Profil"
            >
              <PhCamera :size="15" weight="bold" />
            </button>
            <input
              ref="fileInputRef"
              type="file"
              accept="image/jpeg,image/png,image/webp,image/jpg"
              class="hidden"
              @change="onAvatarSelected"
            />
          </div>

          <!-- User Info Details -->
          <div class="min-w-0 flex-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-tight">
                  {{ auth.user?.nama || 'Pengguna Kostara' }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                  {{ auth.user?.email || 'email@kostara.id' }}
                </p>
              </div>

              <!-- Role Pill -->
              <div class="self-center sm:self-start">
                <span class="inline-block bg-primary text-white text-[11px] font-bold px-3.5 py-1 rounded-full uppercase tracking-wider shadow-2xs">
                  {{ roleTitle }}
                </span>
              </div>
            </div>

            <!-- Status Verified Pill & Phone -->
            <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                <PhCheckCircle :size="15" weight="fill" class="text-emerald-500" />
                Akun Terverifikasi
              </span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                <PhPhone :size="14" class="text-slate-500" />
                {{ auth.user?.no_hp || auth.user?.telepon || 'Belum ada nomor WhatsApp' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Menu Pengaturan Card (Berpindah ke halaman selanjutnya saat diklik) -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
        <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100">
          <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Menu Pengaturan</h2>
          <span class="text-[11px] font-medium text-slate-400">Pilih menu untuk mengelola</span>
        </div>

        <div class="space-y-1.5">
          <!-- 1. Informasi Pribadi -->
          <router-link
            to="/profil/informasi-pribadi"
            class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-50 transition group border border-transparent hover:border-slate-100"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-colors">
                <PhUser :size="20" weight="bold" />
              </div>
              <div class="min-w-0 text-left">
                <p class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors">Informasi Pribadi</p>
                <p class="text-xs text-slate-400 truncate mt-0.5">Nama, email, WhatsApp</p>
              </div>
            </div>
            <PhCaretRight :size="16" class="text-slate-400 group-hover:text-primary group-hover:translate-x-1 transition-all shrink-0" />
          </router-link>

          <!-- 2. Keamanan Akun -->
          <router-link
            to="/profil/keamanan"
            class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-50 transition group border border-transparent hover:border-slate-100"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition-colors">
                <PhShieldCheck :size="20" weight="bold" />
              </div>
              <div class="min-w-0 text-left">
                <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">Keamanan Akun</p>
                <p class="text-xs text-slate-400 truncate mt-0.5">Ubah password, sesi aktif</p>
              </div>
            </div>
            <PhCaretRight :size="16" class="text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all shrink-0" />
          </router-link>

          <!-- 3. Notifikasi -->
          <router-link
            to="/profil/notifikasi"
            class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-50 transition group border border-transparent hover:border-slate-100"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition-colors">
                <PhBell :size="20" weight="bold" />
              </div>
              <div class="min-w-0 text-left">
                <p class="text-sm font-bold text-slate-800 group-hover:text-amber-600 transition-colors">Notifikasi</p>
                <p class="text-xs text-slate-400 truncate mt-0.5">Preferensi pemberitahuan</p>
              </div>
            </div>
            <PhCaretRight :size="16" class="text-slate-400 group-hover:text-amber-600 group-hover:translate-x-1 transition-all shrink-0" />
          </router-link>

          <!-- 4. Kos Favorit -->
          <router-link
            to="/favorit"
            class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-50 transition group border border-transparent hover:border-slate-100"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:bg-rose-100 transition-colors">
                <PhHeart :size="20" weight="fill" />
              </div>
              <div class="min-w-0 text-left">
                <p class="text-sm font-bold text-slate-800 group-hover:text-rose-600 transition-colors">Kos Favorit</p>
                <p class="text-xs text-slate-400 truncate mt-0.5">Daftar kos tersimpan</p>
              </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <span
                v-if="favoritesStore.count > 0"
                class="px-2 py-0.5 text-[10px] font-bold bg-rose-500 text-white rounded-full shadow-2xs"
              >
                {{ favoritesStore.count }}
              </span>
              <PhCaretRight :size="16" class="text-slate-400 group-hover:text-rose-600 group-hover:translate-x-1 transition-all" />
            </div>
          </router-link>

          <!-- 5. Tentang Kami -->
          <router-link
            to="/about"
            class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-slate-50 transition group border border-transparent hover:border-slate-100"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:bg-sky-100 transition-colors">
                <PhInfo :size="20" weight="bold" />
              </div>
              <div class="min-w-0 text-left">
                <p class="text-sm font-bold text-slate-800 group-hover:text-sky-600 transition-colors">Tentang Kami</p>
                <p class="text-xs text-slate-400 truncate mt-0.5">Informasi Kostara</p>
              </div>
            </div>
            <PhCaretRight :size="16" class="text-slate-400 group-hover:text-sky-600 group-hover:translate-x-1 transition-all shrink-0" />
          </router-link>

          <!-- Divider -->
          <div class="border-t border-slate-100 my-2"></div>

          <!-- 6. Keluar (Logout) -->
          <button
            type="button"
            @click="handleLogout"
            class="w-full flex items-center justify-between p-3.5 rounded-2xl hover:bg-rose-50/80 transition group border border-transparent hover:border-rose-100 cursor-pointer"
          >
            <div class="flex items-center gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-rose-100/70 text-rose-600 flex items-center justify-center shrink-0 group-hover:bg-rose-200/70 transition-colors">
                <PhSignOut :size="20" weight="bold" />
              </div>
              <div class="min-w-0 text-left">
                <p class="text-sm font-bold text-rose-600">Keluar</p>
                <p class="text-xs text-rose-400 truncate mt-0.5">Akhiri sesi akun di perangkat ini</p>
              </div>
            </div>
            <PhCaretRight :size="16" class="text-rose-400 group-hover:translate-x-1 transition-all shrink-0" />
          </button>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- HALAMAN 2: INFORMASI PRIBADI (Nama, email, WhatsApp)               -->
    <!-- ================================================================= -->
    <div v-else-if="currentPage === 'informasi-pribadi'" class="space-y-6">
      <!-- Back Navigation Header -->
      <router-link
        to="/profil"
        class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-primary-hover group cursor-pointer transition"
      >
        <PhArrowLeft :size="16" weight="bold" class="group-hover:-translate-x-1 transition-transform" />
        <span>Kembali ke Profil</span>
      </router-link>

      <!-- Page Title -->
      <div class="flex items-center gap-3.5 mb-2">
        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 shadow-2xs">
          <PhUser :size="24" weight="bold" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Informasi Pribadi</h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">
            Nama, email, WhatsApp
          </p>
        </div>
      </div>

      <!-- Main Form Card -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <!-- Avatar Section in Personal Info -->
        <div class="pb-6 mb-6 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
          <div class="relative group shrink-0">
            <div class="w-20 h-20 rounded-full overflow-hidden shadow-md ring-4 ring-slate-100 bg-[#1E3A8A] text-white flex items-center justify-center relative">
              <img
                v-if="avatarPreview || auth.user?.foto"
                :src="avatarPreview || auth.user.foto"
                :alt="auth.user?.nama"
                class="w-full h-full object-cover"
              />
              <span v-else-if="auth.user?.nama" class="text-2xl font-black">
                {{ auth.user.nama.charAt(0).toUpperCase() }}
              </span>
              <PhUser v-else :size="36" weight="bold" class="text-white select-none" />

              <div
                v-if="uploadingAvatar"
                class="absolute inset-0 bg-slate-900/70 backdrop-blur-2xs flex flex-col items-center justify-center text-white"
              >
                <PhCircleNotch :size="18" class="animate-spin text-white" />
              </div>
            </div>

            <button
              type="button"
              @click="triggerFileInput"
              :disabled="uploadingAvatar"
              class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-slate-900 hover:bg-slate-800 text-white shadow-md flex items-center justify-center border-2 border-white transition cursor-pointer active:scale-95"
              title="Ganti Foto Profil"
            >
              <PhCamera :size="13" weight="bold" />
            </button>
          </div>

          <div>
            <h3 class="text-sm font-bold text-slate-900">Foto Profil</h3>
            <p class="text-xs text-slate-500 mt-0.5">Format JPG, PNG, atau WEBP dengan ukuran maksimal 2MB.</p>
            <button
              type="button"
              @click="triggerFileInput"
              class="mt-2 text-xs font-bold text-primary hover:text-primary-hover cursor-pointer"
            >
              Unggah Foto Baru
            </button>
          </div>
        </div>

        <!-- Form Inputs -->
        <form @submit.prevent="saveProfile" class="space-y-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 1. Nama Lengkap -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
              <div class="relative flex items-center">
                <PhUser :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                <input
                  ref="nameInputRef"
                  v-model="profileForm.nama"
                  type="text"
                  required
                  class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs font-semibold rounded-2xl border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800"
                  placeholder="Nama Lengkap"
                />
              </div>
            </div>

            <!-- 2. Email (Read-only) -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Terdaftar</label>
              <div class="relative flex items-center">
                <PhEnvelope :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                <input
                  :value="auth.user?.email"
                  type="email"
                  disabled
                  class="w-full pl-10 pr-4 py-2.5 bg-slate-100/80 text-xs font-semibold rounded-2xl border border-slate-200/80 text-slate-500 cursor-not-allowed outline-none select-none"
                />
              </div>
              <span class="block text-[10px] text-slate-400 mt-1">Email digunakan sebagai ID akun utama</span>
            </div>

            <!-- 3. Nomor WhatsApp -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp Aktif</label>
              <div class="relative flex items-center">
                <PhPhone :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                <input
                  v-model="profileForm.no_hp"
                  type="tel"
                  placeholder="08xxxxxxxxxx"
                  class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs font-semibold rounded-2xl border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800"
                />
              </div>
              <span class="block text-[10px] text-slate-400 mt-1">Digunakan untuk konfirmasi pesanan dari pemilik kos</span>
            </div>

            <!-- 4. Peran Akun -->
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1.5">Peran Akun Sistem</label>
              <div class="relative flex items-center">
                <PhLock :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                <input
                  :value="roleTitle"
                  type="text"
                  disabled
                  class="w-full pl-10 pr-4 py-2.5 bg-slate-100/80 text-xs font-semibold rounded-2xl border border-slate-200/80 text-slate-500 cursor-not-allowed outline-none select-none capitalize"
                />
              </div>
              <span class="block text-[10px] text-slate-400 mt-1">Peran akun diatur oleh administrator</span>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-slate-100">
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
              <PhInfo :size="16" class="text-blue-500 shrink-0" />
              <span>Data pribadi Anda dijaga kerahasiaannya sesuai kebijakan privasi.</span>
            </div>

            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-2xl text-xs font-bold transition shadow-xs active:scale-95 disabled:opacity-50 cursor-pointer shrink-0"
            >
              <PhCircleNotch v-if="saving" :size="16" class="animate-spin" />
              <PhFloppyDisk v-else :size="16" weight="bold" />
              <span>Simpan Informasi Pribadi</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- HALAMAN 3: KEAMANAN AKUN (Ubah password, sesi aktif)               -->
    <!-- ================================================================= -->
    <div v-else-if="currentPage === 'keamanan'" class="space-y-6">
      <!-- Back Navigation Header -->
      <router-link
        to="/profil"
        class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-primary-hover group cursor-pointer transition"
      >
        <PhArrowLeft :size="16" weight="bold" class="group-hover:-translate-x-1 transition-transform" />
        <span>Kembali ke Profil</span>
      </router-link>

      <!-- Page Title -->
      <div class="flex items-center gap-3.5 mb-2">
        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 shadow-2xs">
          <PhShieldCheck :size="24" weight="bold" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Keamanan Akun</h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">
            Ubah password, sesi aktif
          </p>
        </div>
      </div>

      <!-- 1. Card: Ubah Kata Sandi -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <PhLock :size="20" weight="bold" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Ubah Kata Sandi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Gunakan kombinasi kata sandi yang kuat untuk menjaga privasi akun Anda.</p>
          </div>
        </div>

        <!-- Alert Kata Sandi Sukses / Error -->
        <div
          v-if="passwordSuccess"
          class="mb-4 p-3.5 rounded-2xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200 flex items-center justify-between"
        >
          <div class="flex items-center gap-2">
            <PhCheckCircle :size="18" weight="fill" class="text-emerald-600 shrink-0" />
            <span>{{ passwordSuccess }}</span>
          </div>
          <button @click="passwordSuccess = ''" class="text-emerald-700 font-bold ml-4 text-base cursor-pointer">&times;</button>
        </div>

        <div
          v-if="passwordError"
          class="mb-4 p-3.5 rounded-2xl bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200 flex items-center justify-between"
        >
          <div class="flex items-center gap-2">
            <PhWarningCircle :size="18" weight="fill" class="text-rose-600 shrink-0" />
            <span>{{ passwordError }}</span>
          </div>
          <button @click="passwordError = ''" class="text-rose-700 font-bold ml-4 text-base cursor-pointer">&times;</button>
        </div>

        <form @submit.prevent="savePassword">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            <!-- Inputs (~7 cols) -->
            <div class="lg:col-span-7 space-y-3.5">
              <!-- Kata Sandi Saat Ini -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Saat Ini</label>
                <div class="relative flex items-center">
                  <PhLock :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                  <input
                    v-model="passwordForm.current_password"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    placeholder="Masukkan kata sandi saat ini"
                    required
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs font-semibold rounded-2xl border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800"
                  />
                  <button
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                    class="absolute right-3.5 text-slate-400 hover:text-slate-600 flex items-center cursor-pointer"
                  >
                    <PhEyeSlash v-if="showCurrentPassword" :size="16" />
                    <PhEye v-else :size="16" />
                  </button>
                </div>
              </div>

              <!-- Kata Sandi Baru -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                <div class="relative flex items-center">
                  <PhLock :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                  <input
                    v-model="passwordForm.password"
                    :type="showNewPassword ? 'text' : 'password'"
                    placeholder="Minimal 8 karakter kombinasi"
                    required
                    minlength="8"
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs font-semibold rounded-2xl border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800"
                  />
                  <button
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                    class="absolute right-3.5 text-slate-400 hover:text-slate-600 flex items-center cursor-pointer"
                  >
                    <PhEyeSlash v-if="showNewPassword" :size="16" />
                    <PhEye v-else :size="16" />
                  </button>
                </div>
              </div>

              <!-- Konfirmasi Kata Sandi Baru -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <div class="relative flex items-center">
                  <PhLock :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                  <input
                    v-model="passwordForm.password_confirmation"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    placeholder="Ulangi kata sandi baru"
                    required
                    minlength="8"
                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs font-semibold rounded-2xl border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800"
                  />
                  <button
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute right-3.5 text-slate-400 hover:text-slate-600 flex items-center cursor-pointer"
                  >
                    <PhEyeSlash v-if="showConfirmPassword" :size="16" />
                    <PhEye v-else :size="16" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Tips Password Box (~5 cols) -->
            <div class="lg:col-span-5 bg-blue-50/50 rounded-2xl p-4 sm:p-5 border border-blue-100/80 flex flex-col justify-between">
              <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 mb-3.5">
                  <PhShieldCheck :size="18" weight="bold" class="text-blue-600" />
                  <span>Tips Password Kuat</span>
                </div>
                <ul class="space-y-2.5 text-xs text-slate-600">
                  <li class="flex items-center gap-2 font-medium">
                    <PhCheckCircle :size="16" weight="fill" :class="hasMinLength ? 'text-emerald-500' : 'text-slate-300'" />
                    <span :class="{ 'text-slate-900 font-bold': hasMinLength }">Minimal 8 karakter</span>
                  </li>
                  <li class="flex items-center gap-2 font-medium">
                    <PhCheckCircle :size="16" weight="fill" :class="hasUpperLower ? 'text-emerald-500' : 'text-slate-300'" />
                    <span :class="{ 'text-slate-900 font-bold': hasUpperLower }">Huruf besar dan kecil</span>
                  </li>
                  <li class="flex items-center gap-2 font-medium">
                    <PhCheckCircle :size="16" weight="fill" :class="hasNumber ? 'text-emerald-500' : 'text-slate-300'" />
                    <span :class="{ 'text-slate-900 font-bold': hasNumber }">Mengandung angka (0-9)</span>
                  </li>
                  <li class="flex items-center gap-2 font-medium">
                    <PhCheckCircle :size="16" weight="fill" :class="hasSpecialChar ? 'text-emerald-500' : 'text-slate-300'" />
                    <span :class="{ 'text-slate-900 font-bold': hasSpecialChar }">Mengandung simbol (!@#$%)</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-5 mt-5 flex items-center justify-end border-t border-slate-100">
            <button
              type="submit"
              :disabled="passwordLoading"
              class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-2xl text-xs font-bold transition shadow-xs active:scale-95 disabled:opacity-50 cursor-pointer"
            >
              <PhCircleNotch v-if="passwordLoading" :size="16" class="animate-spin" />
              <PhLock v-else :size="16" weight="bold" />
              <span>Ubah Kata Sandi</span>
            </button>
          </div>
        </form>
      </div>

      <!-- 2. Card: Sesi Aktif -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
          <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <PhShieldCheck :size="20" weight="bold" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Sesi Aktif & Keamanan Login</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pantau status autentikasi akun dan perangkat yang sedang aktif.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Card A: Akun Terlindungi -->
          <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
              <PhShieldCheck :size="22" weight="fill" />
            </div>
            <div>
              <p class="text-xs font-bold text-slate-900">Autentikasi Terlindungi</p>
              <p class="text-[11px] text-slate-500 mt-0.5">Sesi Anda terenkripsi aman dengan Bearer Token.</p>
            </div>
          </div>

          <!-- Card B: Sesi Aktif -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-blue-600 flex items-center justify-center shrink-0">
                <PhDeviceMobile :size="20" weight="bold" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-900 truncate">Perangkat Saat Ini</p>
                <p class="text-[10px] text-slate-500 mt-0.5 truncate">Terakhir aktif: {{ lastLoginDisplay }}</p>
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Online
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- HALAMAN 4: NOTIFIKASI (Preferensi pemberitahuan)                  -->
    <!-- ================================================================= -->
    <div v-else-if="currentPage === 'notifikasi'" class="space-y-6">
      <!-- Back Navigation Header -->
      <router-link
        to="/profil"
        class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-primary-hover group cursor-pointer transition"
      >
        <PhArrowLeft :size="16" weight="bold" class="group-hover:-translate-x-1 transition-transform" />
        <span>Kembali ke Profil</span>
      </router-link>

      <!-- Page Title -->
      <div class="flex items-center gap-3.5 mb-2">
        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 shadow-2xs">
          <PhBell :size="24" weight="bold" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Notifikasi</h1>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">
            Preferensi pemberitahuan
          </p>
        </div>
      </div>

      <!-- Main Notification Preferences Card -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
          <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
            <PhBell :size="20" weight="bold" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Saluran Notifikasi</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pilih pemberitahuan yang ingin Anda terima via WhatsApp dan Email.</p>
          </div>
        </div>

        <!-- Notification Toggles -->
        <div class="space-y-4">
          <!-- 1. Notifikasi WhatsApp Pemesanan -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                <PhWhatsappLogo :size="22" weight="fill" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-900">Pembaruan Pesanan via WhatsApp</p>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                  Terima kabar instan melalui WhatsApp saat pengajuan sewa diterima atau disetujui pemilik kos.
                </p>
              </div>
            </div>

            <button
              type="button"
              @click="notifPrefs.whatsapp_order = !notifPrefs.whatsapp_order"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
              :class="notifPrefs.whatsapp_order ? 'bg-primary' : 'bg-slate-300'"
              aria-label="Toggle WhatsApp"
            >
              <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="notifPrefs.whatsapp_order ? 'translate-x-5' : 'translate-x-0'"
              />
            </button>
          </div>

          <!-- 2. Notifikasi Email Transaksi -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                <PhEnvelope :size="22" weight="bold" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-900">Email Faktur & Kuitansi Transaksi</p>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                  Kirimkan bukti pembayaran digital dan kontrak pemesanan langsung ke kotak masuk email Anda.
                </p>
              </div>
            </div>

            <button
              type="button"
              @click="notifPrefs.email_transaction = !notifPrefs.email_transaction"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
              :class="notifPrefs.email_transaction ? 'bg-primary' : 'bg-slate-300'"
              aria-label="Toggle Email"
            >
              <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="notifPrefs.email_transaction ? 'translate-x-5' : 'translate-x-0'"
              />
            </button>
          </div>

          <!-- 3. Pengingat Jatuh Tempo Pembayaran -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                <PhClock :size="22" weight="bold" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-900">Pengingat Batas Pembayaran</p>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                  Peringatan otomatis sebelum batas waktu pembayaran kos atau jatuh tempo sewa berikutnya berakhir.
                </p>
              </div>
            </div>

            <button
              type="button"
              @click="notifPrefs.payment_reminder = !notifPrefs.payment_reminder"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
              :class="notifPrefs.payment_reminder ? 'bg-primary' : 'bg-slate-300'"
              aria-label="Toggle Pengingat"
            >
              <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="notifPrefs.payment_reminder ? 'translate-x-5' : 'translate-x-0'"
              />
            </button>
          </div>

          <!-- 4. Promo & Rekomendasi Kos -->
          <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 mt-0.5">
                <PhTag :size="22" weight="bold" />
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-900">Promo Khusus & Rekomendasi Kos</p>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                  Dapatkan info potongan harga, diskon sewa bulanan, dan rekomendasi kos baru terbaik di kota Anda.
                </p>
              </div>
            </div>

            <button
              type="button"
              @click="notifPrefs.promos = !notifPrefs.promos"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
              :class="notifPrefs.promos ? 'bg-primary' : 'bg-slate-300'"
              aria-label="Toggle Promo"
            >
              <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                :class="notifPrefs.promos ? 'translate-x-5' : 'translate-x-0'"
              />
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-5 mt-5 flex items-center justify-between gap-4 border-t border-slate-100">
          <span class="text-[11px] text-slate-400">Preferensi Anda tersimpan di perangkat ini.</span>
          <button
            type="button"
            @click="saveNotifPrefs"
            class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white px-6 py-2.5 rounded-2xl text-xs font-bold transition shadow-xs active:scale-95 cursor-pointer"
          >
            <PhFloppyDisk :size="16" weight="bold" />
            <span>Simpan Preferensi Notifikasi</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import { useUiStore } from '../stores/ui'
import {
  PhIdentificationBadge,
  PhUser,
  PhCamera,
  PhCheckCircle,
  PhCalendarBlank,
  PhClock,
  PhEnvelope,
  PhPhone,
  PhLock,
  PhInfo,
  PhFloppyDisk,
  PhEye,
  PhEyeSlash,
  PhShieldCheck,
  PhDeviceMobile,
  PhCaretRight,
  PhCircleNotch,
  PhWarningCircle,
  PhHeart,
  PhSignOut,
  PhBell,
  PhArrowLeft,
  PhWhatsappLogo,
  PhTag,
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const ui = useUiStore()
const router = useRouter()
const route = useRoute()

// Current Page computed from URL route path / query
const currentPage = computed(() => {
  if (route.path === '/profil/informasi-pribadi' || route.query.tab === 'informasi-pribadi') {
    return 'informasi-pribadi'
  }
  if (route.path === '/profil/keamanan' || route.query.tab === 'keamanan') {
    return 'keamanan'
  }
  if (route.path === '/profil/notifikasi' || route.query.tab === 'notifikasi') {
    return 'notifikasi'
  }
  return 'profil'
})

// Profile Form State
const toastMsg = ref('')
const errorToastMsg = ref('')
const nameInputRef = ref(null)

const profileForm = reactive({
  nama: auth.user?.nama || '',
  no_hp: auth.user?.no_hp || auth.user?.telepon || '',
})

// Avatar upload state
const fileInputRef = ref(null)
const uploadingAvatar = ref(false)
const avatarPreview = ref(null)

// Password form state
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const passwordLoading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

// Notification preferences state
const notifStorageKey = computed(() => `kostara_notif_prefs_${auth.user?.id || 'guest'}`)
const notifPrefs = reactive({
  whatsapp_order: true,
  email_transaction: true,
  payment_reminder: true,
  promos: false,
})

const loadNotifPrefs = () => {
  try {
    const raw = localStorage.getItem(notifStorageKey.value)
    if (raw) {
      const parsed = JSON.parse(raw)
      Object.assign(notifPrefs, parsed)
    }
  } catch (e) {
    // ignore
  }
}

const saveNotifPrefs = () => {
  try {
    localStorage.setItem(notifStorageKey.value, JSON.stringify(notifPrefs))
    ui.success('Preferensi notifikasi berhasil disimpan!')
  } catch (e) {
    ui.error('Gagal menyimpan preferensi notifikasi.')
  }
}

// Password validators
const hasMinLength = computed(() => passwordForm.password.length >= 8)
const hasUpperLower = computed(() => /[a-z]/.test(passwordForm.password) && /[A-Z]/.test(passwordForm.password))
const hasNumber = computed(() => /[0-9]/.test(passwordForm.password))
const hasSpecialChar = computed(() => /[^a-zA-Z0-9]/.test(passwordForm.password))

// Computed Role Title
const roleTitle = computed(() => {
  switch (auth.role) {
    case 'admin':
      return 'Administrator'
    case 'pemilik':
      return 'Pemilik Kos'
    case 'pencari':
      return 'Pencari Kos'
    default:
      return 'Pengguna'
  }
})

// Joined Date (e.g., "12 Jan 2024")
const joinedDate = computed(() => {
  if (!auth.user?.created_at) return '12 Jan 2024'
  const date = new Date(auth.user.created_at)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
})

// Last Login Display (e.g., "26 Okt 2024, 14:32")
const lastLoginDisplay = computed(() => {
  const now = new Date()
  const dateStr = now.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
  const timeStr = now.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
  }).replace(/\./g, ':')
  return `${dateStr}, ${timeStr}`
})

onMounted(() => {
  if (auth.user) {
    profileForm.nama = auth.user.nama || ''
    profileForm.no_hp = auth.user.no_hp || auth.user.telepon || ''
    if (typeof favoritesStore.fetchFavorites === 'function') {
      favoritesStore.fetchFavorites()
    }
    loadNotifPrefs()
  }
})

const triggerFileInput = () => {
  fileInputRef.value?.click()
}

const onAvatarSelected = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']
  if (!validTypes.includes(file.type)) {
    errorToastMsg.value = 'Format foto harus berupa JPG, PNG, atau WEBP.'
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    errorToastMsg.value = 'Ukuran berkas foto profil maksimal 2MB.'
    return
  }

  avatarPreview.value = URL.createObjectURL(file)
  uploadingAvatar.value = true

  try {
    await auth.updateAvatar(file)
    toastMsg.value = 'Foto profil berhasil diperbarui!'
    ui.success('Foto profil berhasil diperbarui!')
  } catch (err) {
    avatarPreview.value = null
    errorToastMsg.value = err.response?.data?.message || 'Gagal mengunggah foto profil.'
  } finally {
    uploadingAvatar.value = false
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

const saving = ref(false)

const saveProfile = async () => {
  saving.value = true
  toastMsg.value = ''
  errorToastMsg.value = ''
  try {
    await auth.updateProfile({
      nama: profileForm.nama,
      no_hp: profileForm.no_hp,
      telepon: profileForm.no_hp,
    })
    toastMsg.value = 'Informasi pribadi berhasil disimpan!'
    ui.success('Informasi pribadi berhasil diperbarui!')
  } catch (err) {
    errorToastMsg.value = err.response?.data?.message || 'Gagal menyimpan profil, periksa kembali data Anda.'
  } finally {
    saving.value = false
  }
}

const savePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (!passwordForm.current_password) {
    passwordError.value = 'Kata sandi saat ini wajib diisi.'
    return
  }
  if (!passwordForm.password) {
    passwordError.value = 'Kata sandi baru wajib diisi.'
    return
  }
  if (passwordForm.password.length < 8) {
    passwordError.value = 'Kata sandi baru minimal 8 karakter.'
    return
  }
  if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordError.value = 'Konfirmasi kata sandi baru tidak cocok.'
    return
  }

  passwordLoading.value = true
  try {
    const res = await auth.updatePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation,
    })
    passwordSuccess.value = res.message || 'Kata sandi berhasil diperbarui.'
    ui.success('Kata sandi berhasil diperbarui!')
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Gagal memperbarui kata sandi. Periksa kembali kata sandi Anda.'
  } finally {
    passwordLoading.value = false
  }
}

const handleLogout = async () => {
  const confirmed = await ui.confirm({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari akun Kostara?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    confirmType: 'danger',
  })
  if (!confirmed) return

  await auth.logout()
  ui.success('Anda telah berhasil keluar.')
  router.push({ name: 'home' })
}
</script>
