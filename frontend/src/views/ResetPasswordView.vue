<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12 pt-20">
    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-slate-100 shadow-md max-w-md w-full">
      <div class="text-center mb-8">
        <img :src="logoUrl" alt="Kostara Logo" class="w-14 h-14 object-contain mx-auto mb-3" />
        <h2 class="text-2xl font-bold text-on-surface tracking-tight">Atur Ulang Kata Sandi</h2>
        <p class="text-xs text-on-surface-variant mt-1">
          Buat kata sandi baru yang aman untuk akun Kostara Anda.
        </p>
      </div>

      <!-- Missing Token Warning -->
      <div
        v-if="!token && !successMessage"
        class="mb-6 p-4 rounded-xl bg-amber-50 text-amber-900 text-xs border border-amber-200 space-y-2"
      >
        <div class="flex items-center gap-2 font-bold">
          <PhWarningCircle :size="18" weight="bold" class="text-amber-600 shrink-0" />
          <span>Tautan Tidak Lengkap</span>
        </div>
        <p class="leading-relaxed">
          Token reset kata sandi tidak ditemukan pada tautan ini. Silakan lakukan permintaan tautan baru.
        </p>
        <router-link
          to="/forgot-password"
          class="inline-block mt-2 font-bold text-primary hover:underline"
        >
          Minta Tautan Reset Baru &rarr;
        </router-link>
      </div>

      <!-- Success Notification Box -->
      <div
        v-if="successMessage"
        class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs border border-emerald-200 flex flex-col gap-3"
      >
        <div class="flex items-center gap-2 font-bold text-emerald-900">
          <PhCheckCircle :size="20" weight="bold" class="text-emerald-600 shrink-0" />
          <span>Kata Sandi Berhasil Diperbarui!</span>
        </div>
        <p class="leading-relaxed">{{ successMessage }}</p>
        <router-link
          to="/login"
          class="w-full bg-primary-container hover:bg-primary text-white font-bold py-2.5 rounded-lg transition text-xs shadow-md text-center block mt-1"
        >
          Masuk ke Akun Sekarang
        </router-link>
      </div>

      <!-- Error Notification Box -->
      <div
        v-if="auth.error"
        class="mb-4 p-3 rounded-xl bg-error-container text-on-error-container text-xs border border-error/20"
      >
        {{ auth.error }}
      </div>

      <!-- Reset Form -->
      <form v-if="token && !successMessage" @submit.prevent="handleResetPassword" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5 bg-slate-50"
            placeholder="nama@example.com"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Kata Sandi Baru</label>
          <input
            type="password"
            v-model="form.password"
            required
            minlength="8"
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="Minimal 8 karakter"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Konfirmasi Kata Sandi Baru</label>
          <input
            type="password"
            v-model="form.password_confirmation"
            required
            minlength="8"
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="Ulangi kata sandi baru"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-primary-container hover:bg-primary text-white font-bold py-2.5 rounded-lg transition text-xs shadow-md disabled:opacity-50 mt-2 flex items-center justify-center gap-1.5"
        >
          <PhLockKey :size="16" weight="bold" />
          <span>{{ auth.loading ? 'Menyimpan Kata Sandi...' : 'Simpan Kata Sandi Baru' }}</span>
        </button>
      </form>

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
import { reactive, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { PhLockKey, PhCheckCircle, PhArrowLeft, PhWarningCircle } from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import logoUrl from '../assets/logo.png'

const route = useRoute()
const auth = useAuthStore()

const token = ref('')
const successMessage = ref('')

const form = reactive({
  email: '',
  password: '',
  password_confirmation: '',
})

onMounted(() => {
  if (route.query.token) {
    token.value = route.query.token
  }
  if (route.query.email) {
    form.email = route.query.email
  }
})

const handleResetPassword = async () => {
  if (form.password !== form.password_confirmation) {
    auth.error = 'Konfirmasi kata sandi tidak cocok.'
    return
  }

  try {
    const res = await auth.resetPassword({
      token: token.value,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
    })
    successMessage.value = res.message || 'Kata sandi berhasil diperbarui.'
  } catch (err) {
    // Error ditangani oleh store
  }
}
</script>
