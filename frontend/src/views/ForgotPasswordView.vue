<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12 pt-20">
    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-slate-100 shadow-md max-w-md w-full">
      <div class="text-center mb-8">
        <img :src="logoUrl" alt="Kostara Logo" class="w-14 h-14 object-contain mx-auto mb-3" />
        <h2 class="text-2xl font-bold text-on-surface tracking-tight">Lupa Kata Sandi</h2>
        <p class="text-xs text-on-surface-variant mt-1">
          Masukkan alamat email yang terdaftar untuk menerima tautan atur ulang kata sandi.
        </p>
      </div>

      <!-- Success Notification Box -->
      <div
        v-if="successMessage"
        class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs border border-emerald-200 flex flex-col gap-2"
      >
        <div class="flex items-center gap-2 font-bold text-emerald-900">
          <PhCheckCircle :size="18" weight="bold" class="text-emerald-600 shrink-0" />
          <span>Tautan Terkirim!</span>
        </div>
        <p class="leading-relaxed">{{ successMessage }}</p>
        <p class="text-[11px] text-emerald-700/90 mt-1">
          Periksa kotak masuk (inbox) atau folder spam pada email Anda, lalu klik tautan di dalamnya untuk membuat kata sandi baru.
        </p>
      </div>

      <!-- Error Notification Box -->
      <div
        v-if="auth.error"
        class="mb-4 p-3 rounded-xl bg-error-container text-on-error-container text-xs border border-error/20"
      >
        {{ auth.error }}
      </div>

      <!-- Form -->
      <form v-if="!successMessage" @submit.prevent="handleForgotPassword" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Email Terdaftar</label>
          <input
            type="email"
            v-model="email"
            required
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="nama@example.com"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-primary-container hover:bg-primary text-white font-bold py-2.5 rounded-lg transition text-xs shadow-md disabled:opacity-50 mt-2 flex items-center justify-center gap-1.5"
        >
          <PhPaperPlaneTilt :size="16" weight="bold" />
          <span>{{ auth.loading ? 'Mengirim Tautan...' : 'Kirim Tautan Reset' }}</span>
        </button>
      </form>

      <!-- Re-send option if already submitted -->
      <div v-else class="space-y-3">
        <button
          type="button"
          @click="successMessage = ''"
          class="w-full bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold py-2 rounded-lg transition text-xs border border-slate-200"
        >
          Kirim Ulang ke Email Lain
        </button>
      </div>

      <div class="text-center mt-6 pt-4 border-t border-slate-100">
        <router-link
          to="/login"
          class="inline-flex items-center gap-1 text-xs text-primary font-bold hover:underline"
        >
          <PhArrowLeft :size="14" weight="bold" />
          <span>Kembali ke Halaman Masuk</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { PhPaperPlaneTilt, PhCheckCircle, PhArrowLeft } from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import logoUrl from '../assets/logo.png'

const auth = useAuthStore()
const email = ref('')
const successMessage = ref('')

const handleForgotPassword = async () => {
  try {
    const res = await auth.forgotPassword({
      email: email.value,
      frontend_url: window.location.origin,
    })
    successMessage.value = res.message || 'Tautan untuk mereset kata sandi telah dikirim ke email Anda.'
  } catch (err) {
    // Error ditangani dan disimpan di auth.error oleh store
  }
}
</script>
