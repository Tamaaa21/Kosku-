<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12">
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm max-w-lg w-full">
      <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-xs text-slate-500 mt-1">Bergabunglah sebagai pencari kos atau pemilik kos.</p>
      </div>

      <div v-if="errorMsg" class="mb-5 p-3 rounded-xl bg-rose-50 text-rose-700 text-xs border border-rose-200">
        {{ errorMsg }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Role Selection -->
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-2">Daftar Sebagai:</label>
          <div class="grid grid-cols-2 gap-3">
            <label
              :class="[
                'border rounded-2xl p-3 flex items-center gap-3 cursor-pointer transition select-none',
                form.role === 'pencari'
                  ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/20'
                  : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'
              ]"
            >
              <input type="radio" v-model="form.role" value="pencari" class="text-emerald-600 focus:ring-emerald-500" />
              <div>
                <div class="text-xs font-bold text-slate-800">Pencari Kos</div>
                <div class="text-[11px] text-slate-500">Mencari & memesan kamar</div>
              </div>
            </label>

            <label
              :class="[
                'border rounded-2xl p-3 flex items-center gap-3 cursor-pointer transition select-none',
                form.role === 'pemilik'
                  ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/20'
                  : 'border-slate-200 hover:border-slate-300 bg-slate-50/50'
              ]"
            >
              <input type="radio" v-model="form.role" value="pemilik" class="text-emerald-600 focus:ring-emerald-500" />
              <div>
                <div class="text-xs font-bold text-slate-800">Pemilik Kos</div>
                <div class="text-[11px] text-slate-500">Iklankan properti & kamar</div>
              </div>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
          <input
            type="text"
            v-model="form.nama"
            required
            class="w-full text-sm rounded-xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
            placeholder="cth. Budi Pratama"
          />
        </div>

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
          <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Telepon / WhatsApp</label>
          <input
            type="tel"
            v-model="form.no_hp"
            required
            class="w-full text-sm rounded-xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
            placeholder="081234567890"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Kata Sandi</label>
          <input
            type="password"
            v-model="form.password"
            required
            minlength="8"
            class="w-full text-sm rounded-xl border-slate-200 focus:ring-emerald-500 focus:border-emerald-500 p-2.5"
            placeholder="Minimal 8 karakter"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition text-sm shadow-sm disabled:opacity-50 mt-4"
        >
          {{ auth.loading ? 'Mendaftarkan Akun...' : 'Daftar Sekarang' }}
        </button>
      </form>

      <p class="text-center text-xs text-slate-500 mt-6">
        Sudah memiliki akun?
        <router-link to="/login" class="text-emerald-600 font-bold hover:underline">
          Masuk di Sini
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const errorMsg = ref(null)

const form = reactive({
  nama: '',
  email: '',
  no_hp: '',
  password: '',
  role: 'pencari',
})

const handleRegister = async () => {
  errorMsg.value = null
  try {
    const user = await auth.register(form)
    if (user.role === 'pemilik') {
      router.push('/dashboard/pemilik')
    } else {
      router.push('/dashboard/pencari')
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      const errs = err.response.data.errors
      const firstKey = Object.keys(errs)[0]
      errorMsg.value = errs[firstKey][0]
    } else {
      errorMsg.value = err.response?.data?.message || 'Registrasi gagal. Silakan coba lagi.'
    }
  }
}
</script>
