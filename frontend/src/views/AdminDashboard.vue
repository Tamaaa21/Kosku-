<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-bold text-on-surface tracking-tight">Dashboard Administrator</h1>
          <span class="bg-primary-fixed text-on-primary-fixed text-xs font-bold px-2.5 py-0.5 rounded-full">System Admin</span>
        </div>
        <p class="text-sm text-slate-500 mt-1">
          Kelola seluruh akun pengguna, master data fasilitas umum, dan pantau transaksi pemesanan sistem.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          v-if="activeTab === 'fasilitas'"
          @click="openCreateFasilitasModal"
          class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Tambah Fasilitas Baru
        </button>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div v-if="successMsg" class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200 flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = null" class="text-emerald-700 font-bold ml-4">&times;</button>
    </div>
    <div v-if="errorMsg" class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 text-sm border border-rose-200 flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = null" class="text-rose-700 font-bold ml-4">&times;</button>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-3">
      <button
        @click="activeTab = 'pengguna'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2',
          activeTab === 'pengguna' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        Manajemen Pengguna ({{ usersList.length }})
      </button>

      <button
        @click="activeTab = 'fasilitas'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2',
          activeTab === 'fasilitas' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        Master Data Fasilitas ({{ fasilitasList.length }})
      </button>

      <button
        @click="activeTab = 'pemesanan'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2',
          activeTab === 'pemesanan' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Log Seluruh Pemesanan ({{ allBookings.length }})
      </button>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 1: MANAJEMEN PENGGUNA -->
    <!-- ================================================================= -->
    <div v-if="activeTab === 'pengguna'" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
      <!-- Search & Role Filter -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
          <input
            type="text"
            v-model="userSearchQuery"
            placeholder="Cari nama atau email..."
            class="w-full text-xs rounded-xl border-slate-200 pl-9 pr-4 py-2.5"
            @input="debounceUserSearch"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="userRoleFilter"
            @change="fetchUsers"
            class="text-xs rounded-xl border-slate-200 py-2.5 px-3 bg-white"
          >
            <option value="">Semua Peran</option>
            <option value="pencari">Pencari</option>
            <option value="pemilik">Pemilik</option>
            <option value="admin">Admin</option>
          </select>
          <button
            @click="fetchUsers"
            class="p-2.5 text-slate-600 hover:bg-slate-100 rounded-xl border border-slate-200"
            title="Segarkan Data"
          >
            <svg class="w-4 h-4" :class="{ 'animate-spin': loadingUsers }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Users Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
            <tr>
              <th class="py-3 px-4">Pengguna</th>
              <th class="py-3 px-4">Kontak / HP</th>
              <th class="py-3 px-4">Peran (Role)</th>
              <th class="py-3 px-4">Status Akun</th>
              <th class="py-3 px-4">Terdaftar</th>
              <th class="py-3 px-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingUsers">
              <td colspan="6" class="py-8 text-center text-slate-400">Memuat data pengguna...</td>
            </tr>
            <tr v-else-if="usersList.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengguna ditemukan.</td>
            </tr>
            <tr v-for="user in usersList" :key="user.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">{{ user.nama }}</div>
                <div class="text-slate-400 text-[11px]">{{ user.email }}</div>
              </td>
              <td class="py-3 px-4 font-mono">{{ user.no_hp || '-' }}</td>
              <td class="py-3 px-4">
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border',
                    user.role === 'admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' :
                    user.role === 'pemilik' ? 'bg-amber-50 text-amber-700 border-amber-200' :
                    'bg-emerald-50 text-emerald-700 border-emerald-200'
                  ]"
                >
                  {{ user.role }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span
                  :class="[
                    'inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full',
                    user.aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'
                  ]"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="user.aktif ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                  {{ user.aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="py-3 px-4 text-slate-400">{{ formatDate(user.created_at) }}</td>
              <td class="py-3 px-4 text-right">
                <button
                  @click="toggleUserActive(user)"
                  :disabled="togglingUserId === user.id"
                  :class="[
                    'text-[11px] font-bold px-3 py-1.5 rounded-xl border transition',
                    user.aktif
                      ? 'border-rose-200 text-rose-600 hover:bg-rose-50'
                      : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50'
                  ]"
                >
                  {{ user.aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: MASTER FASILITAS -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'fasilitas'" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
            <tr>
              <th class="py-3 px-4">ID</th>
              <th class="py-3 px-4">Nama Fasilitas</th>
              <th class="py-3 px-4">Icon Identifier</th>
              <th class="py-3 px-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingFasilitas">
              <td colspan="4" class="py-8 text-center text-slate-400">Memuat data fasilitas...</td>
            </tr>
            <tr v-else-if="fasilitasList.length === 0">
              <td colspan="4" class="py-8 text-center text-slate-400">Belum ada master fasilitas.</td>
            </tr>
            <tr v-for="fas in fasilitasList" :key="fas.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4 font-mono font-bold text-slate-400">#{{ fas.id }}</td>
              <td class="py-3 px-4 font-bold text-slate-900">{{ fas.nama }}</td>
              <td class="py-3 px-4 font-mono text-slate-500">{{ fas.icon || '-' }}</td>
              <td class="py-3 px-4 text-right space-x-2">
                <button
                  @click="openEditFasilitasModal(fas)"
                  class="text-[11px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition"
                >
                  Edit
                </button>
                <button
                  @click="deleteFasilitas(fas.id)"
                  class="text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-xl transition"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 3: SELURUH PEMESANAN SISTEM -->
    <!-- ================================================================= -->
    <div v-else-if="activeTab === 'pemesanan'" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-y border-slate-200">
            <tr>
              <th class="py-3 px-4">ID Pesanan</th>
              <th class="py-3 px-4">Penyewa (Pencari)</th>
              <th class="py-3 px-4">Properti Kos</th>
              <th class="py-3 px-4">Kamar</th>
              <th class="py-3 px-4">Tanggal Masuk</th>
              <th class="py-3 px-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-if="loadingBookings">
              <td colspan="6" class="py-8 text-center text-slate-400">Memuat log pemesanan...</td>
            </tr>
            <tr v-else-if="allBookings.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-400">Belum ada aktivitas transaksi pemesanan.</td>
            </tr>
            <tr v-for="b in allBookings" :key="b.id" class="hover:bg-slate-50/60 transition">
              <td class="py-3 px-4 font-mono font-bold text-slate-900">#{{ b.id }}</td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">{{ b.pengguna?.nama || '-' }}</div>
                <div class="text-slate-400 text-[11px]">{{ b.pengguna?.email || '-' }}</div>
              </td>
              <td class="py-3 px-4 font-semibold text-slate-800">{{ b.kos?.nama || '-' }}</td>
              <td class="py-3 px-4 text-emerald-700 font-bold">{{ b.kamar?.nama_kamar || '-' }}</td>
              <td class="py-3 px-4">{{ formatDateOnly(b.tanggal_pemesanan) }}</td>
              <td class="py-3 px-4">
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded border',
                    statusBadgeClass(b.status)
                  ]"
                >
                  {{ b.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: CREATE / EDIT FASILITAS -->
    <!-- ================================================================= -->
    <div
      v-if="showFasilitasModal"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <h3 class="text-base font-black text-slate-900">
            {{ isEditingFasilitas ? 'Edit Master Fasilitas' : 'Tambah Fasilitas Baru' }}
          </h3>
          <button @click="showFasilitasModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveFasilitas" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Fasilitas</label>
            <input
              type="text"
              v-model="fasilitasForm.nama"
              required
              class="w-full text-xs rounded-xl border-slate-200 p-2.5"
              placeholder="Cth: WiFi Cepat 50 Mbps"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Icon Key (Opsional)</label>
            <input
              type="text"
              v-model="fasilitasForm.icon"
              class="w-full text-xs rounded-xl border-slate-200 p-2.5"
              placeholder="Cth: wifi, ac, parking"
            />
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showFasilitasModal = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="savingFasilitas"
              class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition disabled:opacity-50 shadow-sm"
            >
              {{ savingFasilitas ? 'Menyimpan...' : 'Simpan Fasilitas' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '../services/api'

const activeTab = ref('pengguna')

const usersList = ref([])
const fasilitasList = ref([])
const allBookings = ref([])

const loadingUsers = ref(false)
const loadingFasilitas = ref(false)
const loadingBookings = ref(false)
const togglingUserId = ref(null)

const userSearchQuery = ref('')
const userRoleFilter = ref('')

const successMsg = ref(null)
const errorMsg = ref(null)

// Fasilitas Modal State
const showFasilitasModal = ref(false)
const isEditingFasilitas = ref(false)
const editingFasilitasId = ref(null)
const savingFasilitas = ref(false)
const fasilitasForm = reactive({
  nama: '',
  icon: '',
})

let searchTimeout = null
const debounceUserSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchUsers()
  }, 350)
}

const fetchUsers = async () => {
  loadingUsers.value = true
  try {
    const params = {}
    if (userSearchQuery.value) params.q = userSearchQuery.value
    if (userRoleFilter.value) params.role = userRoleFilter.value

    const res = await api.get('/admin/pengguna', { params })
    usersList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat pengguna.'
  } finally {
    loadingUsers.value = false
  }
}

const toggleUserActive = async (user) => {
  togglingUserId.value = user.id
  try {
    const nextStatus = !user.aktif
    await api.patch(`/admin/pengguna/${user.id}/status`, { aktif: nextStatus })
    user.aktif = nextStatus
    successMsg.value = `Status akun ${user.nama} berhasil diubah.`
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memperbarui status akun.'
  } finally {
    togglingUserId.value = null
  }
}

const fetchFasilitas = async () => {
  loadingFasilitas.value = true
  try {
    const res = await api.get('/fasilitas')
    fasilitasList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat master fasilitas.'
  } finally {
    loadingFasilitas.value = false
  }
}

const openCreateFasilitasModal = () => {
  isEditingFasilitas.value = false
  editingFasilitasId.value = null
  fasilitasForm.nama = ''
  fasilitasForm.icon = ''
  showFasilitasModal.value = true
}

const openEditFasilitasModal = (fas) => {
  isEditingFasilitas.value = true
  editingFasilitasId.value = fas.id
  fasilitasForm.nama = fas.nama
  fasilitasForm.icon = fas.icon || ''
  showFasilitasModal.value = true
}

const saveFasilitas = async () => {
  savingFasilitas.value = true
  try {
    if (isEditingFasilitas.value) {
      await api.put(`/fasilitas/${editingFasilitasId.value}`, fasilitasForm)
      successMsg.value = 'Fasilitas berhasil diperbarui.'
    } else {
      await api.post('/fasilitas', fasilitasForm)
      successMsg.value = 'Fasilitas baru berhasil ditambahkan.'
    }
    showFasilitasModal.value = false
    await fetchFasilitas()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan fasilitas.'
  } finally {
    savingFasilitas.value = false
  }
}

const deleteFasilitas = async (id) => {
  if (!confirm('Hapus master fasilitas ini?')) return
  try {
    await api.delete(`/fasilitas/${id}`)
    successMsg.value = 'Fasilitas berhasil dihapus.'
    await fetchFasilitas()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menghapus fasilitas.'
  }
}

const fetchAllBookings = async () => {
  loadingBookings.value = true
  try {
    const res = await api.get('/pemesanan')
    allBookings.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat pemesanan.'
  } finally {
    loadingBookings.value = false
  }
}

const statusBadgeClass = (status) => {
  switch (status) {
    case 'menunggu':
      return 'bg-amber-50 text-amber-700 border-amber-200'
    case 'disetujui':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'ditolak':
      return 'bg-rose-50 text-rose-700 border-rose-200'
    case 'dibatalkan':
      return 'bg-slate-100 text-slate-600 border-slate-300'
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchUsers()
  fetchFasilitas()
  fetchAllBookings()
})
</script>
