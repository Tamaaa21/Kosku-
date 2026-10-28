<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12 pt-20">
    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-slate-100 shadow-md max-w-md w-full">
      <div class="text-center mb-8">
        <img :src="logoUrl" alt="Kostara Logo" class="w-14 h-14 object-contain mx-auto mb-3" />
        <h2 class="text-2xl font-bold text-on-surface tracking-tight">Masuk ke Kostara</h2>
        <p class="text-xs text-on-surface-variant mt-1">Gunakan akun terdaftar Anda untuk melanjutkan ke sistem.</p>
      </div>

      <!-- Quick Test Login Buttons -->
      <div class="mb-6 bg-surface-container-low border border-slate-200/80 rounded-xl p-3">
        <span class="text-[11px] font-bold text-secondary uppercase tracking-wider block mb-2">Uji Coba Cepat (Akun Demo):</span>
        <div class="grid grid-cols-3 gap-2">
          <button
            type="button"
            @click="quickFill('pencari@kos.test', 'password')"
            class="text-[11px] font-semibold bg-white border border-slate-200 hover:border-primary py-1.5 rounded-lg text-on-surface transition"
          >
            Pencari
          </button>
          <button
            type="button"
            @click="quickFill('pemilik@kos.test', 'password')"
            class="text-[11px] font-semibold bg-white border border-slate-200 hover:border-primary py-1.5 rounded-lg text-on-surface transition"
          >
            Pemilik
          </button>
          <button
            type="button"
            @click="quickFill('admin@kos.test', 'password')"
            class="text-[11px] font-semibold bg-white border border-slate-200 hover:border-primary py-1.5 rounded-lg text-on-surface transition"
          >
            Admin
          </button>
        </div>
      </div>

      <div v-if="auth.error" class="mb-4 p-3 rounded-xl bg-error-container text-on-error-container text-xs border border-error/20">
        {{ auth.error }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="nama@example.com"
          />
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-semibold text-on-surface">Kata Sandi</label>
            <router-link
              to="/forgot-password"
              class="text-[11px] font-semibold text-primary hover:underline hover:text-primary-hover transition"
            >
              Lupa Kata Sandi?
            </router-link>
          </div>
          <input
            type="password"
            v-model="form.password"
            required
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-primary-container hover:bg-primary text-white font-bold py-2.5 rounded-lg transition text-xs shadow-md disabled:opacity-50 mt-2 flex items-center justify-center gap-1.5"
        >
          <PhSignIn :size="16" weight="bold" />
          <span>{{ auth.loading ? 'Memproses...' : 'Masuk Sekarang' }}</span>
        </button>
      </form>

      <p class="text-center text-xs text-on-surface-variant mt-6">
        Belum memiliki akun?
        <router-link to="/register" class="text-primary font-bold hover:underline">
          Daftar Sekarang
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { PhSignIn } from '@phosphor-icons/vue'
import { useAuthStore } from '../stores/auth'
import logoUrl from '../assets/logo.png'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const quickFill = (email, pass) => {
  form.email = email
  form.password = pass
}

const handleLogin = async () => {
  try {
    const user = await auth.login(form)
    const redirectUrl = route.query.redirect

    if (redirectUrl) {
      router.push(redirectUrl)
    } else if (user.role === 'admin') {
      router.push('/dashboard/admin')
    } else if (user.role === 'pemilik') {
      router.push('/dashboard/pemilik')
    } else {
      router.push('/pesanan')
    }
  } catch (err) {
    // Error ditangani di store
  }
}
</script>
