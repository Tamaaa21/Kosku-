<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12 pt-20">
    <div class="bg-surface-container-lowest p-8 rounded-2xl border border-slate-100 shadow-md max-w-md w-full">
      <div class="text-center mb-8">
        <img :src="logoUrl" alt="KosKu Logo" class="w-14 h-14 object-contain mx-auto mb-3" />
        <h2 class="text-2xl font-bold text-on-surface tracking-tight">Daftar Akun KosKu</h2>
        <p class="text-xs text-on-surface-variant mt-1">Bergabunglah sebagai pencari kos atau pemilik kos.</p>
      </div>

      <div v-if="errorMsg" class="mb-5 p-3 rounded-xl bg-error-container text-on-error-container text-xs border border-error/20">
        {{ errorMsg }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Role Selection -->
        <div>
          <label class="block text-xs font-semibold text-on-surface mb-2">Daftar Sebagai:</label>
          <div class="grid grid-cols-2 gap-3">
            <label
              :class="[
                'border rounded-xl p-3 flex items-center gap-2.5 cursor-pointer transition select-none',
                form.role === 'pencari'
                  ? 'border-primary bg-primary/5 ring-1 ring-primary'
                  : 'border-slate-200 hover:border-slate-300 bg-surface-container-low/50'
              ]"
            >
              <input type="radio" v-model="form.role" value="pencari" class="text-primary focus:ring-primary" />
              <div>
                <div class="text-xs font-bold text-on-surface">Pencari Kos</div>
                <div class="text-[10px] text-on-surface-variant">Sewa kamar</div>
              </div>
            </label>

            <label
              :class="[
                'border rounded-xl p-3 flex items-center gap-2.5 cursor-pointer transition select-none',
                form.role === 'pemilik'
                  ? 'border-primary bg-primary/5 ring-1 ring-primary'
                  : 'border-slate-200 hover:border-slate-300 bg-surface-container-low/50'
              ]"
            >
              <input type="radio" v-model="form.role" value="pemilik" class="text-primary focus:ring-primary" />
              <div>
                <div class="text-xs font-bold text-on-surface">Pemilik Kos</div>
                <div class="text-[10px] text-on-surface-variant">Iklankan properti</div>
              </div>
            </label>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Nama Lengkap</label>
          <input
            type="text"
            v-model="form.nama"
            required
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="cth. Sigit Pratama"
          />
        </div>

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
          <label class="block text-xs font-semibold text-on-surface mb-1">Nomor Telepon / WhatsApp</label>
          <input
            type="tel"
            v-model="form.no_hp"
            required
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="081234567890"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-on-surface mb-1">Kata Sandi</label>
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
          <label class="block text-xs font-semibold text-on-surface mb-1">Konfirmasi Kata Sandi</label>
          <input
            type="password"
            v-model="form.password_confirmation"
            required
            minlength="8"
            class="w-full text-xs rounded-lg border-slate-200 focus:ring-primary focus:border-primary p-2.5"
            placeholder="Ulangi kata sandi Anda"
          />
        </div>

        <button
          type="submit"
          :disabled="auth.loading"
          class="w-full bg-primary-container hover:bg-primary text-white font-bold py-2.5 rounded-lg transition text-xs shadow-md disabled:opacity-50 mt-4 flex items-center justify-center gap-1.5"
        >
          <span class="material-symbols-outlined text-base">how_to_reg</span>
          <span>{{ auth.loading ? 'Mendaftarkan Akun...' : 'Daftar Sekarang' }}</span>
        </button>
      </form>

      <p class="text-center text-xs text-on-surface-variant mt-6">
        Sudah memiliki akun?
        <router-link to="/login" class="text-primary font-bold hover:underline">
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
import logoUrl from '../assets/KosKu Housing Pin Logo.png'

const router = useRouter()
const auth = useAuthStore()

const errorMsg = ref(null)

const form = reactive({
  nama: '',
  email: '',
  no_hp: '',
  password: '',
  password_confirmation: '',
  role: 'pencari',
})

const handleRegister = async () => {
  errorMsg.value = null

  if (form.password !== form.password_confirmation) {
    errorMsg.value = 'Konfirmasi kata sandi tidak cocok dengan kata sandi yang dimasukkan.'
    return
  }

  try {
    const user = await auth.register(form)
    if (user?.role === 'pemilik') {
      router.push('/dashboard/pemilik')
    } else {
      router.push('/pesanan')
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
