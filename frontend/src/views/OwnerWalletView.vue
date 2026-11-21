<template>
  <div class="min-h-screen bg-[#F8FAFC] pb-16 pt-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary mb-2">
          <PhWallet :size="14" weight="bold" />
          <span>Dompet Mitra Kostara</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
          Saldo & Penarikan Dana
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
          Kelola saldo pendapatan bersih sewa kos Anda dan ajukan pencairan dana langsung ke rekening bank.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openWithdrawModal"
          :disabled="summary.saldo_tersedia < 50000"
          class="px-4 py-2.5 bg-primary hover:bg-primary-container text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition flex items-center gap-2 disabled:opacity-50 cursor-pointer"
        >
          <PhArrowUpRight :size="16" weight="bold" />
          <span>Tarik Saldo</span>
        </button>
      </div>
    </div>

    <!-- Info Strategy Monetisasi (Zero Subscription Fee) -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/80 to-indigo-50/50 border border-blue-100 flex items-start gap-3.5">
      <div class="p-2 bg-blue-100 text-primary rounded-xl shrink-0 mt-0.5">
        <PhShieldCheck :size="20" weight="bold" />
      </div>
      <div class="text-xs text-slate-600 leading-relaxed space-y-1">
        <h4 class="font-bold text-slate-900 text-sm">Bebas Biaya Langganan & Komisi Transparan</h4>
        <p>
          Mitra pemilik kos dapat menikmati seluruh fitur Kostara secara <strong>100% gratis tanpa biaya langganan bulanan</strong>.
          Kostara hanya memotong <strong>komisi platform 5%</strong> secara otomatis per transaksi sewa yang berhasil setelah serah terima kunci.
        </p>
      </div>
    </div>

    <!-- 1. KPI Financial Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      <!-- Saldo Tersedia -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500">Saldo Tersedia (Bisa Ditarik)</span>
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <PhWallet :size="20" weight="bold" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-black text-emerald-600 tracking-tight">
            {{ formatRupiah(summary.saldo_tersedia) }}
          </span>
          <span class="text-[11px] text-slate-400 block mt-1">Minimal penarikan Rp 50.000</span>
        </div>
      </div>

      <!-- Total Pendapatan Bersih -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500">Total Pendapatan Bersih (Akumulasi)</span>
          <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
            <PhArrowDownLeft :size="20" weight="bold" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-black text-slate-900 tracking-tight">
            {{ formatRupiah(summary.total_pendapatan) }}
          </span>
          <span class="text-[11px] text-slate-400 block mt-1">Hak bersih sewa setelah komisi 5%</span>
        </div>
      </div>

      <!-- Sedang Diproses -->
      <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500">Sedang Diproses Pencairan</span>
          <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <PhClock :size="20" weight="bold" />
          </div>
        </div>
        <div class="mt-4">
          <span class="text-2xl font-black text-amber-600 tracking-tight">
            {{ formatRupiah(summary.sedang_ditarik) }}
          </span>
          <span class="text-[11px] text-slate-400 block mt-1">Menunggu transfer admin</span>
        </div>
      </div>
    </div>

    <!-- 2. Main Grid: Rekening Bank & Riwayat Penarikan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Kolom Kiri: Rekening Bank Saya (4 cols) -->
      <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-slate-100 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-slate-900">Rekening Bank Tujuan</h3>
            <p class="text-[11px] text-slate-400">Untuk menerima dana pencairan</p>
          </div>
          <button
            @click="showAddBankModal = true"
            class="p-2 rounded-xl bg-primary/10 hover:bg-primary text-primary hover:text-white transition cursor-pointer"
            title="Tambah Rekening Baru"
          >
            <PhPlus :size="16" weight="bold" />
          </button>
        </div>

        <div v-if="loadingBanks" class="space-y-3">
          <div v-for="i in 2" :key="i" class="h-20 bg-slate-50 rounded-xl animate-pulse"></div>
        </div>

        <div v-else-if="bankAccounts.length === 0" class="py-8 text-center bg-slate-50/60 rounded-xl border border-dashed border-slate-200">
          <PhBank :size="32" class="text-slate-300 mx-auto mb-2" />
          <p class="text-xs font-bold text-slate-700">Belum Ada Rekening Bank</p>
          <p class="text-[11px] text-slate-400 mt-0.5">Tambahkan rekening bank untuk menarik saldo.</p>
          <button
            @click="showAddBankModal = true"
            class="mt-3 px-3 py-1.5 bg-primary text-white text-xs font-bold rounded-lg shadow-xs"
          >
            + Tambah Rekening
          </button>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="bank in bankAccounts"
            :key="bank.id"
            class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 transition relative group"
          >
            <div class="flex items-start justify-between">
              <div>
                <span class="text-xs font-black text-slate-900 tracking-wider uppercase">{{ bank.nama_bank }}</span>
                <p class="text-xs font-bold text-slate-800 mt-0.5 tracking-wider">{{ bank.nomor_rekening }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">a.n. {{ bank.nama_pemilik }}</p>
              </div>

              <div class="flex items-center gap-1">
                <span v-if="bank.is_utama" class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                  Utama
                </span>
                <button
                  @click="deleteBank(bank.id)"
                  class="p-1 text-slate-400 hover:text-rose-600 transition opacity-0 group-hover:opacity-100 cursor-pointer"
                  title="Hapus Rekening"
                >
                  <PhTrash :size="14" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Tabs Riwayat Penarikan & Mutasi (8 cols) -->
      <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-slate-100 shadow-xs space-y-4">
        <!-- Tab Selector -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-3">
            <button
              @click="activeSubTab = 'penarikan'"
              class="text-xs sm:text-sm font-bold pb-1 transition cursor-pointer relative"
              :class="activeSubTab === 'penarikan' ? 'text-primary' : 'text-slate-400 hover:text-slate-700'"
            >
              <span>Riwayat Penarikan Dana</span>
              <span v-if="activeSubTab === 'penarikan'" class="absolute bottom-[-13px] left-0 right-0 h-0.5 bg-primary"></span>
            </button>
            <button
              @click="activeSubTab = 'mutasi'"
              class="text-xs sm:text-sm font-bold pb-1 transition cursor-pointer relative"
              :class="activeSubTab === 'mutasi' ? 'text-primary' : 'text-slate-400 hover:text-slate-700'"
            >
              <span>Buku Kas & Mutasi Saldo</span>
              <span v-if="activeSubTab === 'mutasi'" class="absolute bottom-[-13px] left-0 right-0 h-0.5 bg-primary"></span>
            </button>
          </div>
        </div>

        <!-- 2A. TAB PENARIKAN DANA -->
        <div v-if="activeSubTab === 'penarikan'" class="space-y-3">
          <!-- Filter & Sort Toolbar -->
          <div class="flex flex-wrap items-center justify-between gap-2.5 pb-1">
            <div class="flex items-center gap-2">
              <div class="relative">
                <select
                  v-model="filterWdStatus"
                  @change="onWdFilterChange"
                  class="appearance-none pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer shadow-2xs"
                >
                  <option value="all">Semua Status</option>
                  <option value="menunggu">Menunggu</option>
                  <option value="diproses">Diproses</option>
                  <option value="selesai">Selesai</option>
                  <option value="ditolak">Ditolak</option>
                </select>
                <PhCaretDown :size="12" weight="bold" class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" />
              </div>

              <div class="relative">
                <select
                  v-model="sortWdOption"
                  @change="onWdFilterChange"
                  class="appearance-none pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer shadow-2xs"
                >
                  <option value="latest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                  <option value="nominal_desc">Nominal Terbesar</option>
                  <option value="nominal_asc">Nominal Terkecil</option>
                </select>
                <PhCaretDown :size="12" weight="bold" class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" />
              </div>
            </div>
          </div>

          <div v-if="loadingWithdrawals" class="space-y-2">
            <div v-for="i in 3" :key="i" class="h-16 bg-slate-50 rounded-xl animate-pulse"></div>
          </div>

          <div v-else-if="withdrawals.length === 0" class="py-12 text-center text-xs text-slate-400">
            Belum ada riwayat penarikan dana.
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="wd in withdrawals"
              :key="wd.id"
              class="p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3"
            >
              <div>
                <div class="flex items-center gap-2">
                  <span class="text-xs font-bold text-slate-900">{{ wd.kode_penarikan }}</span>
                  <span
                    :class="[
                      'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase',
                      wd.status === 'selesai' ? 'bg-emerald-100 text-emerald-800' :
                      wd.status === 'ditolak' ? 'bg-rose-100 text-rose-800' :
                      'bg-amber-100 text-amber-800'
                    ]"
                  >
                    {{ wd.status }}
                  </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                  Ke {{ wd.rekening_bank?.nama_bank }} - {{ wd.rekening_bank?.nomor_rekening }} ({{ wd.rekening_bank?.nama_pemilik }})
                </p>
                <p v-if="wd.alasan_penolakan" class="text-xs text-rose-600 mt-1">
                  Alasan Ditolak: {{ wd.alasan_penolakan }}
                </p>
                <p v-if="wd.catatan_admin" class="text-xs text-slate-500 mt-0.5">
                  Catatan: {{ wd.catatan_admin }}
                </p>
              </div>

              <div class="text-left sm:text-right shrink-0">
                <span class="text-sm font-black text-slate-900 block">{{ formatRupiah(wd.nominal) }}</span>
                <span class="text-[11px] text-slate-400">{{ formatDate(wd.created_at) }}</span>
              </div>
            </div>

            <!-- Base Pagination Controls Penarikan -->
            <div v-if="!loadingWithdrawals && withdrawals.length > 0" class="pt-2">
              <BasePagination
                :current-page="paginationWithdrawals.current_page"
                :last-page="paginationWithdrawals.last_page"
                :total="paginationWithdrawals.total"
                :from="paginationWithdrawals.from"
                :to="paginationWithdrawals.to"
                :per-page="paginationWithdrawals.per_page"
                :per-page-options="[6, 10, 20, 50]"
                :loading="loadingWithdrawals"
                :card="false"
                item-name="penarikan dana"
                @change-page="handleWdPageChange"
                @change-per-page="handleWdPerPageChange"
              />
            </div>
          </div>
        </div>

        <!-- 2B. TAB MUTASI SALDO (LEDGER) -->
        <div v-else class="space-y-3">
          <!-- Filter & Sort Toolbar -->
          <div class="flex flex-wrap items-center justify-between gap-2.5 pb-1">
            <div class="flex items-center gap-2">
              <div class="relative">
                <select
                  v-model="filterMutTipe"
                  @change="onMutFilterChange"
                  class="appearance-none pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer shadow-2xs"
                >
                  <option value="all">Semua Tipe</option>
                  <option value="kredit">Pemasukan (+)</option>
                  <option value="debit">Penarikan (-)</option>
                </select>
                <PhCaretDown :size="12" weight="bold" class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" />
              </div>

              <div class="relative">
                <select
                  v-model="sortMutOption"
                  @change="onMutFilterChange"
                  class="appearance-none pl-3 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer shadow-2xs"
                >
                  <option value="latest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                  <option value="nominal_desc">Nominal Terbesar</option>
                  <option value="nominal_asc">Nominal Terkecil</option>
                </select>
                <PhCaretDown :size="12" weight="bold" class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400" />
              </div>
            </div>
          </div>

          <div v-if="loadingMutations" class="space-y-2">
            <div v-for="i in 3" :key="i" class="h-16 bg-slate-50 rounded-xl animate-pulse"></div>
          </div>

          <div v-else-if="mutations.length === 0" class="py-12 text-center text-xs text-slate-400">
            Belum ada catatan mutasi saldo.
          </div>

          <div v-else class="space-y-2.5">
            <div
              v-for="mut in mutations"
              :key="mut.id"
              class="p-3.5 rounded-xl border border-slate-100 hover:border-slate-200 transition flex items-center justify-between gap-3"
            >
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-9 h-9 rounded-xl flex items-center justify-center shrink-0',
                    mut.tipe === 'kredit' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'
                  ]"
                >
                  <PhArrowDownLeft v-if="mut.tipe === 'kredit'" :size="18" weight="bold" />
                  <PhArrowUpRight v-else :size="18" weight="bold" />
                </div>
                <div>
                  <h5 class="text-xs font-bold text-slate-900 leading-tight">{{ mut.deskripsi }}</h5>
                  <span class="text-[11px] text-slate-400">{{ formatDate(mut.created_at) }}</span>
                </div>
              </div>

              <div class="text-right shrink-0">
                <span
                  :class="[
                    'text-xs sm:text-sm font-black block',
                    mut.tipe === 'kredit' ? 'text-emerald-600' : 'text-rose-600'
                  ]"
                >
                  {{ mut.tipe === 'kredit' ? '+' : '-' }}{{ formatRupiah(mut.nominal) }}
                </span>
                <span class="text-[10px] text-slate-400">Saldo: {{ formatRupiah(mut.saldo_akhir) }}</span>
              </div>
            </div>

            <!-- Base Pagination Controls Mutasi -->
            <div v-if="!loadingMutations && mutations.length > 0" class="pt-2">
              <BasePagination
                :current-page="paginationMutations.current_page"
                :last-page="paginationMutations.last_page"
                :total="paginationMutations.total"
                :from="paginationMutations.from"
                :to="paginationMutations.to"
                :per-page="paginationMutations.per_page"
                :per-page-options="[6, 10, 20, 50]"
                :loading="loadingMutations"
                :card="false"
                item-name="mutasi saldo"
                @change-page="handleMutPageChange"
                @change-per-page="handleMutPerPageChange"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah Rekening Bank -->
    <div v-if="showAddBankModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-base font-bold text-slate-900">Tambah Rekening Bank</h3>
          <button @click="showAddBankModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form @submit.prevent="submitAddBank" class="space-y-3.5">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Bank</label>
            <select v-model="bankForm.nama_bank" required class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-slate-50">
              <option value="BCA">BCA (Bank Central Asia)</option>
              <option value="MANDIRI">Bank Mandiri</option>
              <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
              <option value="BNI">BNI (Bank Negara Indonesia)</option>
              <option value="BSI">BSI (Bank Syariah Indonesia)</option>
              <option value="CIMB">CIMB Niaga</option>
              <option value="JAGO">Bank Jago</option>
              <option value="SEABANK">SeaBank</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening</label>
            <input
              type="text"
              v-model="bankForm.nomor_rekening"
              required
              placeholder="Contoh: 1234567890"
              class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-primary focus:border-primary"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Rekening</label>
            <input
              type="text"
              v-model="bankForm.nama_pemilik"
              required
              placeholder="Sesuai buku tabungan"
              class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-primary focus:border-primary"
            />
          </div>

          <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 pt-1">
            <input type="checkbox" v-model="bankForm.is_utama" class="rounded text-primary focus:ring-primary" />
            <span>Jadikan rekening bank utama</span>
          </label>

          <div class="flex items-center gap-2 pt-2">
            <button
              type="button"
              @click="showAddBankModal = false"
              class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submittingBank"
              class="flex-1 py-2.5 bg-primary hover:bg-primary-container text-white text-xs font-bold rounded-xl transition disabled:opacity-50"
            >
              {{ submittingBank ? 'Menyimpan...' : 'Simpan Rekening' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Form Tarik Saldo -->
    <div v-if="showWithdrawModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-base font-bold text-slate-900">Tarik Saldo ke Rekening</h3>
          <button @click="showWithdrawModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>

        <form @submit.prevent="submitWithdrawal" class="space-y-3.5">
          <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200/80 flex items-center justify-between">
            <span class="text-xs text-emerald-800">Saldo Tersedia:</span>
            <span class="text-sm font-black text-emerald-800">{{ formatRupiah(summary.saldo_tersedia) }}</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Rekening Bank Tujuan</label>
            <select v-model="withdrawForm.rekening_bank_id" required class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-slate-50">
              <option v-for="b in bankAccounts" :key="b.id" :value="b.id">
                {{ b.nama_bank }} - {{ b.nomor_rekening }} (a.n {{ b.nama_pemilik }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Penarikan (Rp)</label>
            <input
              type="number"
              v-model.number="withdrawForm.nominal"
              :min="50000"
              :max="summary.saldo_tersedia"
              required
              class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-primary focus:border-primary font-bold text-slate-900"
            />
            <span class="text-[11px] text-slate-400 mt-1 block">Minimal Rp 50.000, Bebas Biaya Admin</span>
          </div>

          <div class="flex items-center gap-2 pt-2">
            <button
              type="button"
              @click="showWithdrawModal = false"
              class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submittingWd || withdrawForm.nominal > summary.saldo_tersedia || withdrawForm.nominal < 50000"
              class="flex-1 py-2.5 bg-primary hover:bg-primary-container text-white text-xs font-bold rounded-xl transition disabled:opacity-50"
            >
              {{ submittingWd ? 'Mengajukan...' : 'Ajukan Penarikan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {
  PhWallet,
  PhArrowDownLeft,
  PhArrowUpRight,
  PhBank,
  PhPlus,
  PhTrash,
  PhShieldCheck,
  PhClock,
  PhCaretDown,
} from '@phosphor-icons/vue'
import { useUiStore } from '../stores/ui'
import api from '../services/api'
import BasePagination from '../components/BasePagination.vue'

const ui = useUiStore()

const summary = reactive({
  saldo_tersedia: 0,
  total_pendapatan: 0,
  sedang_ditarik: 0,
})

const activeSubTab = ref('penarikan')
const bankAccounts = ref([])
const withdrawals = ref([])
const mutations = ref([])

const filterWdStatus = ref('all')
const sortWdOption = ref('latest')
const paginationWithdrawals = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
  per_page: 6,
})

const filterMutTipe = ref('all')
const sortMutOption = ref('latest')
const paginationMutations = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
  per_page: 6,
})

const loadingBanks = ref(false)
const loadingWithdrawals = ref(false)
const loadingMutations = ref(false)

const showAddBankModal = ref(false)
const submittingBank = ref(false)
const bankForm = reactive({
  nama_bank: 'BCA',
  nomor_rekening: '',
  nama_pemilik: '',
  is_utama: false,
})

const showWithdrawModal = ref(false)
const submittingWd = ref(false)
const withdrawForm = reactive({
  rekening_bank_id: null,
  nominal: 100000,
})

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const fetchSummary = async () => {
  try {
    const res = await api.get('/wallet/summary')
    Object.assign(summary, res.data.data)
  } catch (err) {
    // Abaikan
  }
}

const fetchBankAccounts = async () => {
  loadingBanks.value = true
  try {
    const res = await api.get('/wallet/bank-accounts')
    bankAccounts.value = res.data.data || []
    if (bankAccounts.value.length > 0 && !withdrawForm.rekening_bank_id) {
      withdrawForm.rekening_bank_id = bankAccounts.value[0].id
    }
  } catch (err) {
    // Abaikan
  } finally {
    loadingBanks.value = false
  }
}

const fetchWithdrawals = async (page = null) => {
  if (page) {
    paginationWithdrawals.current_page = page
  }
  loadingWithdrawals.value = true
  try {
    const params = {
      page: paginationWithdrawals.current_page,
      per_page: paginationWithdrawals.per_page,
    }
    if (filterWdStatus.value !== 'all') params.status = filterWdStatus.value
    if (sortWdOption.value) params.sort = sortWdOption.value

    const res = await api.get('/wallet/withdrawals', { params })
    withdrawals.value = res.data.data || []
    if (res.data?.meta) {
      paginationWithdrawals.current_page = res.data.meta.current_page || 1
      paginationWithdrawals.last_page = res.data.meta.last_page || 1
      paginationWithdrawals.total = res.data.meta.total || 0
      paginationWithdrawals.from = res.data.meta.from || 0
      paginationWithdrawals.to = res.data.meta.to || 0
      paginationWithdrawals.per_page = res.data.meta.per_page || paginationWithdrawals.per_page

      if (paginationWithdrawals.current_page > paginationWithdrawals.last_page && paginationWithdrawals.last_page >= 1) {
        paginationWithdrawals.current_page = paginationWithdrawals.last_page
        return await fetchWithdrawals()
      }
    }
  } catch (err) {
    // Abaikan
  } finally {
    loadingWithdrawals.value = false
  }
}

const handleWdPageChange = (page) => {
  paginationWithdrawals.current_page = page
  fetchWithdrawals()
}

const handleWdPerPageChange = (size) => {
  paginationWithdrawals.per_page = size
  paginationWithdrawals.current_page = 1
  fetchWithdrawals()
}

const onWdFilterChange = () => {
  paginationWithdrawals.current_page = 1
  fetchWithdrawals()
}

const fetchMutations = async (page = null) => {
  if (page) {
    paginationMutations.current_page = page
  }
  loadingMutations.value = true
  try {
    const params = {
      page: paginationMutations.current_page,
      per_page: paginationMutations.per_page,
    }
    if (filterMutTipe.value !== 'all') params.tipe = filterMutTipe.value
    if (sortMutOption.value) params.sort = sortMutOption.value

    const res = await api.get('/wallet/mutations', { params })
    mutations.value = res.data.data || []
    if (res.data?.meta) {
      paginationMutations.current_page = res.data.meta.current_page || 1
      paginationMutations.last_page = res.data.meta.last_page || 1
      paginationMutations.total = res.data.meta.total || 0
      paginationMutations.from = res.data.meta.from || 0
      paginationMutations.to = res.data.meta.to || 0
      paginationMutations.per_page = res.data.meta.per_page || paginationMutations.per_page

      if (paginationMutations.current_page > paginationMutations.last_page && paginationMutations.last_page >= 1) {
        paginationMutations.current_page = paginationMutations.last_page
        return await fetchMutations()
      }
    }
  } catch (err) {
    // Abaikan
  } finally {
    loadingMutations.value = false
  }
}

const handleMutPageChange = (page) => {
  paginationMutations.current_page = page
  fetchMutations()
}

const handleMutPerPageChange = (size) => {
  paginationMutations.per_page = size
  paginationMutations.current_page = 1
  fetchMutations()
}

const onMutFilterChange = () => {
  paginationMutations.current_page = 1
  fetchMutations()
}

const submitAddBank = async () => {
  submittingBank.value = true
  try {
    await api.post('/wallet/bank-accounts', bankForm)
    ui.success('Rekening bank berhasil ditambahkan!')
    showAddBankModal.value = false
    bankForm.nomor_rekening = ''
    bankForm.nama_pemilik = ''
    await fetchBankAccounts()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal menyimpan rekening.')
  } finally {
    submittingBank.value = false
  }
}

const deleteBank = async (id) => {
  const confirmed = await ui.confirm({
    title: 'Hapus Rekening Bank?',
    message: 'Apakah Anda yakin ingin menghapus rekening bank ini?',
    confirmText: 'Ya, Hapus',
    confirmType: 'danger',
  })
  if (!confirmed) return

  try {
    await api.delete(`/wallet/bank-accounts/${id}`)
    ui.success('Rekening bank berhasil dihapus.')
    await fetchBankAccounts()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal menghapus rekening.')
  }
}

const openWithdrawModal = () => {
  if (bankAccounts.value.length === 0) {
    ui.warning('Silakan tambahkan nomor rekening bank tujuan terlebih dahulu.')
    showAddBankModal.value = true
    return
  }
  withdrawForm.nominal = Math.min(summary.saldo_tersedia, 500000)
  showWithdrawModal.value = true
}

const submitWithdrawal = async () => {
  submittingWd.value = true
  try {
    await api.post('/wallet/withdrawals', withdrawForm)
    ui.success('Pengajuan penarikan dana berhasil dikirim! Menunggu transfer dari admin.')
    showWithdrawModal.value = false
    paginationWithdrawals.current_page = 1
    paginationMutations.current_page = 1
    await Promise.all([fetchSummary(), fetchWithdrawals(), fetchMutations()])
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal mengajukan penarikan dana.')
  } finally {
    submittingWd.value = false
  }
}

onMounted(() => {
  fetchSummary()
  fetchBankAccounts()
  fetchWithdrawals()
  fetchMutations()
})
</script>
