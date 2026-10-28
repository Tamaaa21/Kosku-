<template>
  <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
    <!-- Header Page Meta (Matching ui-profile.png) -->
    <div class="flex items-center gap-3.5 mb-8">
      <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 shadow-2xs">
        <PhIdentificationBadge :size="24" weight="bold" />
      </div>
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Profil Saya</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5 font-medium">
          Kelola informasi akun, pengaturan, dan keamanan akun Anda.
        </p>
      </div>
    </div>

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

    <!-- 2-Column Responsive Layout (Matching ui-profile.png) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- ============================================================= -->
      <!-- KOLOM KIRI: Identitas & Informasi Singkat (~320px)           -->
      <!-- ============================================================= -->
      <div class="lg:col-span-4 xl:col-span-4 space-y-6">
        <!-- 1. User Identity Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden relative">
          <!-- Subtle Decorative Gradient Top Header -->
          <div class="bg-gradient-to-b from-blue-50/90 via-blue-50/40 to-transparent p-6 pb-5 flex flex-col items-center text-center relative">
            <!-- Large Avatar with Camera Upload Button -->
            <div class="relative group mt-1 mb-3">
              <div class="w-24 h-24 rounded-full overflow-hidden shadow-md ring-4 ring-white bg-[#1E3A8A] text-white flex items-center justify-center relative">
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

                <!-- Uploading Overlay -->
                <div
                  v-if="uploadingAvatar"
                  class="absolute inset-0 bg-slate-900/70 backdrop-blur-2xs flex flex-col items-center justify-center text-white"
                >
                  <PhCircleNotch :size="22" class="animate-spin text-white" />
                  <span class="text-[9px] mt-1 font-bold">Mengunggah...</span>
                </div>
              </div>

              <!-- Camera Button -->
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

            <!-- User Name & Email -->
            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight">
              {{ auth.user?.nama || 'Admin' }}
            </h2>
            <p class="text-xs text-slate-500 font-medium mt-1">
              {{ auth.user?.email || 'admin@kostara.id' }}
            </p>

            <!-- Role Badge Pill -->
            <div class="mt-3">
              <span class="inline-block bg-[#0B63B6] text-white text-[10px] font-bold px-4 py-1 rounded-full uppercase tracking-wider shadow-2xs">
                {{ auth.role?.toUpperCase() || 'ADMIN' }}
              </span>
            </div>

            <!-- Status Card Box: Akun Terverifikasi -->
            <div class="w-full mt-5 p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200/80 flex items-center gap-3 text-left">
              <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                <PhCheckCircle :size="18" weight="fill" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-emerald-800 leading-tight">Akun Terverifikasi</p>
                <p class="text-[10px] text-emerald-600 mt-0.5">Akun Anda telah diverifikasi dan aman.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Informasi Singkat Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
          <h3 class="text-sm font-bold text-slate-900 mb-4">Informasi Singkat</h3>

          <div class="space-y-3.5">
            <!-- Item 1: Peran -->
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <PhUser :size="18" weight="bold" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Peran</p>
                <p class="text-xs font-bold text-slate-800 mt-0.5 capitalize">{{ roleTitle }}</p>
              </div>
            </div>

            <!-- Item 2: Bergabung Sejak -->
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <PhCalendarBlank :size="18" weight="bold" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Bergabung Sejak</p>
                <p class="text-xs font-bold text-slate-800 mt-0.5">{{ joinedDate }}</p>
              </div>
            </div>

            <!-- Item 3: Terakhir Login -->
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <PhClock :size="18" weight="bold" />
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Terakhir Login</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-xs font-bold text-slate-800">{{ lastLoginDisplay }}</span>
                  <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Online
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Security Info Box with Padlock & Leaves Vector -->
          <div class="mt-5 p-4 rounded-2xl bg-gradient-to-b from-blue-50/70 to-blue-50/20 border border-blue-100 flex flex-col items-center text-center">
            <!-- Cute Security Padlock Vector Illustration -->
            <div class="w-16 h-16 flex items-center justify-center mb-1 select-none">
              <svg class="w-14 h-14" viewBox="0 0 64 64" fill="none">
                <!-- Background Soft Aura -->
                <circle cx="32" cy="36" r="22" fill="#DBEAFE" fill-opacity="0.6" />
                <!-- Left Shrub / Leaves -->
                <path d="M14 44 C12 36, 18 32, 22 36" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" fill="none" />
                <path d="M12 40 C10 38, 14 34, 16 38" stroke="#34D399" stroke-width="2" stroke-linecap="round" fill="none" />
                <!-- Right Shrub / Leaves -->
                <path d="M50 44 C52 36, 46 32, 42 36" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" fill="none" />
                <path d="M52 40 C54 38, 50 34, 48 38" stroke="#34D399" stroke-width="2" stroke-linecap="round" fill="none" />
                <!-- Padlock Body -->
                <rect x="22" y="26" width="20" height="20" rx="5" fill="#2563EB" />
                <!-- Padlock Shackle -->
                <path d="M26 26 V21 C26 17.686 28.686 15 32 15 C35.314 15 38 17.686 38 21 V26" stroke="#3B82F6" stroke-width="3.5" stroke-linecap="round" fill="none" />
                <!-- Keyhole -->
                <circle cx="32" cy="34" r="2.5" fill="#FFFFFF" />
                <rect x="31" y="34" width="2" height="5" rx="1" fill="#FFFFFF" />
              </svg>
            </div>
            <p class="text-xs font-bold text-slate-800">Kelola akun Anda dengan aman</p>
            <p class="text-[10px] text-slate-500 mt-1 leading-relaxed">
              Pastikan informasi selalu diperbarui untuk menjaga keamanan akun.
            </p>
          </div>
        </div>
      </div>

      <!-- ============================================================= -->
      <!-- KOLOM KANAN: Formulir Profil, Ganti Sandi, & Sesi (~800px)    -->
      <!-- ============================================================= -->
      <div class="lg:col-span-8 xl:col-span-8 space-y-6">
        <!-- 1. Card: Informasi Akun -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
          <!-- Card Header -->
          <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <PhUser :size="20" weight="bold" />
              </div>
              <div>
                <h3 class="text-base font-bold text-slate-900">Informasi Akun</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kelola informasi dasar akun Anda.</p>
              </div>
            </div>

            <!-- Ubah Profil Button -->
            <button
              type="button"
              @click="focusNameInput"
              class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-blue-600 bg-blue-50/70 hover:bg-blue-100 border border-blue-200/80 transition cursor-pointer active:scale-95"
            >
              <PhPencilSimple :size="14" weight="bold" />
              <span>Ubah Profil</span>
            </button>
          </div>

          <!-- Form Informasi Akun -->
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nama Lengkap -->
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

              <!-- Email (Read-only) -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
                <div class="relative flex items-center">
                  <PhEnvelope :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                  <input
                    :value="auth.user?.email"
                    type="email"
                    disabled
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-100/80 text-xs font-semibold rounded-2xl border border-slate-200/80 text-slate-500 cursor-not-allowed outline-none select-none"
                  />
                </div>
              </div>

              <!-- Nomor WhatsApp -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp</label>
                <div class="relative flex items-center">
                  <PhPhone :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                  <input
                    v-model="profileForm.no_hp"
                    type="tel"
                    placeholder="08xxxxxxxxxx"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white text-xs font-semibold rounded-2xl border border-slate-200/80 focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none text-slate-800"
                  />
                </div>
              </div>

              <!-- Peran / Hak Akses Sistem -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Peran / Hak Akses Sistem</label>
                <div class="relative flex items-center">
                  <PhLock :size="17" class="absolute left-3.5 text-slate-400 select-none pointer-events-none" />
                  <input
                    :value="roleTitle"
                    type="text"
                    disabled
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-100/80 text-xs font-semibold rounded-2xl border border-slate-200/80 text-slate-500 cursor-not-allowed outline-none select-none capitalize"
                  />
                </div>
                <span class="block text-[10px] text-slate-400 mt-1">Peran akun tidak dapat diubah</span>
              </div>
            </div>

            <!-- Footer Action Row -->
            <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-100">
              <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
                <PhInfo :size="16" class="text-blue-500 shrink-0" />
                <span>Informasi ini digunakan untuk keperluan administrasi dan komunikasi.</span>
              </div>

              <button
                type="submit"
                :disabled="saving"
                class="inline-flex items-center justify-center gap-2 bg-[#0B63B6] hover:bg-primary-container text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition shadow-xs active:scale-95 disabled:opacity-50 cursor-pointer shrink-0"
              >
                <PhCircleNotch v-if="saving" :size="16" class="animate-spin" />
                <PhFloppyDisk v-else :size="16" weight="bold" />
                <span>Simpan Perubahan</span>
              </button>
            </div>
          </form>
        </div>

        <!-- 2. Card: Ubah Kata Sandi -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
          <!-- Card Header -->
          <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <PhLock :size="20" weight="bold" />
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900">Ubah Kata Sandi</h3>
              <p class="text-xs text-slate-400 mt-0.5">Gunakan kata sandi yang kuat untuk menjaga keamanan akun Anda.</p>
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
              <!-- Left Sub-column: Inputs (~7 cols) -->
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
                      placeholder="Masukkan kata sandi baru"
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
                      placeholder="Konfirmasi kata sandi baru"
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

              <!-- Right Sub-column: Tips Password Box (~5 cols) -->
              <div class="lg:col-span-5 bg-blue-50/50 rounded-2xl p-4 sm:p-5 border border-blue-100/80 flex flex-col justify-between">
                <div>
                  <div class="flex items-center gap-2 text-xs font-bold text-slate-800 mb-3.5">
                    <PhShieldCheck :size="18" weight="bold" class="text-blue-600" />
                    <span>Tips Password</span>
                  </div>
                  <ul class="space-y-2.5 text-xs text-slate-600">
                    <li class="flex items-center gap-2 font-medium">
                      <PhCheckCircle :size="16" weight="fill" :class="hasMinLength ? 'text-emerald-500' : 'text-slate-300'" />
                      <span :class="{ 'text-slate-900 font-bold': hasMinLength }">Minimal 8 karakter</span>
                    </li>
                    <li class="flex items-center gap-2 font-medium">
                      <PhCheckCircle :size="16" weight="fill" :class="hasUpperLower ? 'text-emerald-500' : 'text-slate-300'" />
                      <span :class="{ 'text-slate-900 font-bold': hasUpperLower }">Mengandung huruf besar dan kecil</span>
                    </li>
                    <li class="flex items-center gap-2 font-medium">
                      <PhCheckCircle :size="16" weight="fill" :class="hasNumber ? 'text-emerald-500' : 'text-slate-300'" />
                      <span :class="{ 'text-slate-900 font-bold': hasNumber }">Mengandung angka</span>
                    </li>
                    <li class="flex items-center gap-2 font-medium">
                      <PhCheckCircle :size="16" weight="fill" :class="hasSpecialChar ? 'text-emerald-500' : 'text-slate-300'" />
                      <span :class="{ 'text-slate-900 font-bold': hasSpecialChar }">Mengandung simbol (contoh: !@#)</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Update Password Submit Button -->
            <div class="pt-5 mt-5 flex items-center justify-end border-t border-slate-100">
              <button
                type="submit"
                :disabled="passwordLoading"
                class="inline-flex items-center gap-2 bg-[#0B63B6] hover:bg-primary-container text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition shadow-xs active:scale-95 disabled:opacity-50 cursor-pointer"
              >
                <PhCircleNotch v-if="passwordLoading" :size="16" class="animate-spin" />
                <PhLock v-else :size="16" weight="bold" />
                <span>Ubah Kata Sandi</span>
              </button>
            </div>
          </form>
        </div>

        <!-- 3. Card: Keamanan & Sesi Akun -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
          <!-- Card Header -->
          <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <PhShieldCheck :size="20" weight="bold" />
            </div>
            <div>
              <h4 class="text-base font-bold text-slate-900">Keamanan & Sesi Akun</h4>
              <p class="text-xs text-slate-400 mt-0.5">Pantau aktivitas akun dan kelola sesi yang sedang aktif.</p>
            </div>
          </div>

          <!-- 2 Horizontal Status Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Card A: Akun Terlindungi -->
            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-center gap-3.5">
              <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-2xs">
                <PhShieldCheck :size="22" weight="fill" />
              </div>
              <div>
                <p class="text-xs font-bold text-slate-900">Akun Terlindungi</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Keamanan akun Anda dalam kondisi baik.</p>
              </div>
            </div>

            <!-- Card B: Sesi Aktif -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3 hover:bg-slate-100/70 transition">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-blue-600 flex items-center justify-center shrink-0">
                  <PhDeviceMobile :size="20" weight="bold" />
                </div>
                <div class="min-w-0">
                  <p class="text-xs font-bold text-slate-900 truncate">Sesi Aktif</p>
                  <p class="text-[10px] text-slate-500 mt-0.5 truncate">Terakhir login: {{ lastLoginDisplay }}</p>
                </div>
              </div>

              <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Online
                </span>
                <PhCaretRight :size="14" class="text-slate-400" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import {
  PhIdentificationBadge,
  PhUser,
  PhCamera,
  PhCheckCircle,
  PhCalendarBlank,
  PhClock,
  PhPencilSimple,
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
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()
const router = useRouter()

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

// Tips Password Real-Time Validators
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
  }
})

const focusNameInput = () => {
  nameInputRef.value?.focus()
}

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
    toastMsg.value = 'Informasi profil dan nomor WhatsApp berhasil disimpan!'
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
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Gagal memperbarui kata sandi. Periksa kembali kata sandi Anda.'
  } finally {
    passwordLoading.value = false
  }
}
</script>
