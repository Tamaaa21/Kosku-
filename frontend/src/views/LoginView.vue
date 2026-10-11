<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm max-w-md w-full">
      <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke Akun Anda</h2>
        <p class="text-xs text-slate-500 mt-1">Gunakan kredensial yang telah terdaftar untuk melanjutkan.</p>
      </div>

      <!-- Quick Test Login Buttons -->
      <div class="mb-6 bg-slate-50 border border-slate-200 rounded-2xl p-3">
        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Login Cepat (Uji Coba):</span>
        <div class="grid grid-cols-3 gap-2">
          <button
            type="button"
            @click="quickFill('pencari@kos.test', 'password')"
            class="text-[11px] font-semibold bg-white border border-slate-200 hover:border-emerald-500 py-1.5 rounded-lg text-slate-700 transition"
          >
            Pencari
          </button>
          <button
            type="button"
            @click="quickFill('pemilik@kos.test', 'password')"
            class="text-[11px] font-semibold bg-white border border-slate-200 hover:border-emerald-500 py-1.5 rounded-lg text-slate-700 transition"
          >
            Pemilik
          </button>
          <button
            type="button"
            @click="quickFill('admin@kos.test', 'password')"
            class="text-[11px] font-semibold bg-white border border-slate-200 hover:border-emerald-500 py-1.5 rounded-lg text-slate-700 transition"
          >
            Admin
          </button>
        </div>
      </div>

      <div v-if="auth.error" class="mb-4 p-3 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-200">
        {{ auth.error }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            class="w-full text-sm rounded-xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
            placeholder="nama@example.com"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Kata Sandi</label>
          <input
            type="password"
            v-model="form.password"
            required
            class="w-full text-sm rounded-xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition text-sm shadow-sm disabled:opacity-50 mt-2"
        >
          {{ auth.loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>

      <p class="text-center text-xs text-slate-500 mt-6">
        Belum memiliki akun?
        <router-link to="/register" class="text-emerald-600 font-bold hover:underline">
          Daftar Sekarang
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

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
    } else if (user.role === 'pemilik') {
      router.push('/dashboard/pemilik')
    } else if (user.role === 'admin') {
      router.push('/dashboard/admin')
    } else {
      router.push('/dashboard/pencari')
    }
  } catch (err) {
    // Error ditangani di store
  }
}
</script>
