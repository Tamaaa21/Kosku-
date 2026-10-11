<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Dashboard Pemilik Kos</h1>
        <p class="text-xs text-on-surface-variant mt-1">
          Kelola properti kos, inventaris kamar, dan konfirmasi pemesanan dari calon penyewa.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          @click="openCreateKosModal"
          class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm"
        >
          <span class="material-symbols-outlined text-base">add</span>
          Tambah Kos Baru
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

    <!-- Main Navigation Tabs -->
    <div class="flex items-center gap-3 mb-6 border-b border-slate-200 pb-3">
      <button
        @click="activeMainTab = 'kos'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2',
          activeMainTab === 'kos'
            ? 'bg-slate-900 text-white shadow-sm'
            : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        Properti Kos Saya ({{ kosList.length }})
      </button>

      <button
        @click="activeMainTab = 'pemesanan'"
        :class="[
          'px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2 relative',
          activeMainTab === 'pemesanan'
            ? 'bg-slate-900 text-white shadow-sm'
            : 'text-slate-600 hover:bg-slate-100'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        Kotak Masuk Pemesanan
        <span
          v-if="pendingBookingsCount > 0"
          class="bg-amber-500 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full ml-1"
        >
          {{ pendingBookingsCount }}
        </span>
      </button>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 1: DAFTAR KOS SAYA -->
    <!-- ================================================================= -->
    <div v-if="activeMainTab === 'kos'">
      <!-- Loading Skeleton -->
      <div v-if="loadingKos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="h-64 bg-white border border-slate-200 rounded-3xl animate-pulse"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="kosList.length === 0" class="text-center py-16 bg-white rounded-3xl border border-slate-200">
        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-800">Belum Ada Properti Kos</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
          Mulai tambahkan kos Anda sekarang agar dapat ditemukan oleh ribuan calon penyewa di sekitar lokasi Anda.
        </p>
        <button
          @click="openCreateKosModal"
          class="mt-4 text-xs font-bold bg-emerald-600 text-white px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition"
        >
          + Tambah Kos Sekarang
        </button>
      </div>

      <!-- Kos Cards Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="kos in kosList"
          :key="kos.id"
          class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <span
                :class="[
                  'text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-md border tracking-wider',
                  kos.jenis === 'putra' ? 'bg-blue-50 text-blue-700 border-blue-200' :
                  kos.jenis === 'putri' ? 'bg-pink-50 text-pink-700 border-pink-200' :
                  'bg-purple-50 text-purple-700 border-purple-200'
                ]"
              >
                Kos {{ kos.jenis }}
              </span>
              <span class="text-xs text-slate-400 font-medium">ID #{{ kos.id }}</span>
            </div>

            <h3 class="text-lg font-black text-slate-900 mb-1">
              <router-link :to="`/kos/${kos.id}`" class="hover:text-emerald-600 transition">
                {{ kos.nama }}
              </router-link>
            </h3>

            <p class="text-xs text-slate-500 line-clamp-2 mb-3">
              {{ kos.alamat }}
            </p>

            <!-- Geolocation Badge -->
            <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mb-3 bg-slate-50 p-2 rounded-xl">
              <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span v-if="kos.latitude && kos.longitude" class="font-mono">
                {{ parseFloat(kos.latitude).toFixed(4) }}, {{ parseFloat(kos.longitude).toFixed(4) }}
              </span>
              <span v-else class="text-amber-600">Koordinat belum diset</span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600 py-2 border-y border-slate-100">
              <span>Mulai dari:</span>
              <span class="font-extrabold text-slate-900 text-sm">{{ formatRupiah(kos.harga) }}<span class="text-slate-400 text-xs font-normal">/bln</span></span>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600 pt-2">
              <span>Jumlah Kamar:</span>
              <span class="font-bold text-slate-800">{{ kos.kamar?.length || 0 }} unit</span>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
            <button
              @click="openRoomManager(kos)"
              class="flex-1 text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 py-2 rounded-xl transition text-center"
            >
              Kelola Kamar
            </button>
            <button
              @click="openEditKosModal(kos)"
              class="text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-xl transition"
              title="Edit Data Kos"
            >
              Edit
            </button>
            <button
              @click="deleteKos(kos.id)"
              class="text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-2 rounded-xl transition"
              title="Hapus Kos"
            >
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: KOTAK MASUK PEMESANAN -->
    <!-- ================================================================= -->
    <div v-else-if="activeMainTab === 'pemesanan'">
      <!-- Loading Skeleton -->
      <div v-if="loadingBookings" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-28 bg-white border border-slate-200 rounded-2xl animate-pulse"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="bookingsList.length === 0" class="text-center py-16 bg-white rounded-3xl border border-slate-200">
        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-800">Kotak Masuk Pemesanan Kosong</h3>
        <p class="text-xs text-slate-500 mt-1">Belum ada penyewa yang mengajukan permohonan kamar pada kos Anda.</p>
      </div>

      <!-- Bookings List -->
      <div v-else class="space-y-4">
        <div
          v-for="b in bookingsList"
          :key="b.id"
          class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6"
        >
          <div class="flex-1 space-y-2">
            <div class="flex items-center gap-3">
              <span
                :class="[
                  'text-[11px] font-extrabold uppercase px-2.5 py-1 rounded-md border tracking-wider',
                  statusBadgeClass(b.status)
                ]"
              >
                {{ b.status }}
              </span>
              <span class="text-xs text-slate-400">Order #{{ b.id }}</span>
              <span class="text-xs text-slate-400">&bull; {{ formatDate(b.created_at) }}</span>
            </div>

            <div class="flex items-center gap-2">
              <h4 class="text-base font-bold text-slate-900">{{ b.kos?.nama }}</h4>
              <span class="text-xs font-semibold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-100">
                Kamar: {{ b.kamar?.nama_kamar }}
              </span>
            </div>

            <!-- Pencari Info -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
              <div>
                <span class="text-slate-400 block">Nama Pemesan:</span>
                <span class="font-bold text-slate-800">{{ b.pengguna?.nama || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 block">Kontak / WhatsApp:</span>
                <span class="font-bold text-slate-800">{{ b.pengguna?.no_hp || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 block">Rencana Masuk:</span>
                <span class="font-bold text-slate-800">{{ formatDateOnly(b.tanggal_pemesanan) }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:items-end justify-between border-t md:border-t-0 pt-4 md:pt-0 gap-3">
            <div class="text-left sm:text-right">
              <span class="text-xs text-slate-400 block">Tarif Sewa:</span>
              <span class="text-lg font-black text-slate-900">{{ formatRupiah(b.kamar?.harga || b.kos?.harga) }}</span>
            </div>

            <div v-if="b.status === 'menunggu'" class="flex items-center gap-2">
              <button
                @click="updateBookingStatus(b.id, 'disetujui')"
                :disabled="actionBookingLoading === b.id"
                class="text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition shadow-sm"
              >
                Setujui
              </button>
              <button
                @click="updateBookingStatus(b.id, 'ditolak')"
                :disabled="actionBookingLoading === b.id"
                class="text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-3 py-2 rounded-xl transition"
              >
                Tolak
              </button>
            </div>
            <div v-else class="text-xs text-slate-400 italic">
              Status telah diperbarui
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: KELOLA KAMAR -->
    <!-- ================================================================= -->
    <div
      v-if="roomManagerKos"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <div>
            <h3 class="text-lg font-black text-slate-900">Kelola Kamar Kos</h3>
            <p class="text-xs text-slate-500">{{ roomManagerKos.nama }}</p>
          </div>
          <button @click="closeRoomManager" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
        </div>

        <!-- Add Room Form -->
        <form @submit.prevent="createRoom" class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6">
          <span class="text-xs font-bold text-slate-700 block mb-3">+ Tambah Unit Kamar Baru</span>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">Nama/Nomor Kamar</label>
              <input
                type="text"
                v-model="roomForm.nama_kamar"
                placeholder="Cth: Kamar A-01"
                required
                class="w-full text-xs rounded-xl border-slate-200 p-2.5"
              />
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">Harga Bulanan (Rp)</label>
              <input
                type="number"
                v-model="roomForm.harga"
                placeholder="Cth: 850000"
                required
                class="w-full text-xs rounded-xl border-slate-200 p-2.5"
              />
            </div>
            <div class="flex items-end">
              <button
                type="submit"
                :disabled="roomActionLoading"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-xl transition disabled:opacity-50"
              >
                {{ roomActionLoading ? 'Menyimpan...' : 'Tambah Kamar' }}
              </button>
            </div>
          </div>
        </form>

        <!-- Room List -->
        <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
          <div v-if="loadingRooms" class="text-center py-6 text-xs text-slate-400 animate-pulse">Memuat kamar...</div>
          <div v-else-if="currentRooms.length === 0" class="text-center py-6 text-xs text-slate-400">
            Belum ada kamar yang didaftarkan untuk kos ini.
          </div>
          <div
            v-for="kamar in currentRooms"
            :key="kamar.id"
            class="flex items-center justify-between p-3 rounded-2xl border border-slate-200 bg-white hover:border-slate-300 transition"
          >
            <div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-900">{{ kamar.nama_kamar }}</span>
                <span
                  :class="[
                    'text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md border',
                    kamar.status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'
                  ]"
                >
                  {{ kamar.status }}
                </span>
              </div>
              <span class="text-xs text-slate-500 font-semibold">{{ formatRupiah(kamar.harga) }} / bulan</span>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="toggleRoomStatus(kamar)"
                class="text-xs font-semibold px-2.5 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 transition"
                :title="kamar.status === 'tersedia' ? 'Ubah ke Terisi' : 'Ubah ke Tersedia'"
              >
                {{ kamar.status === 'tersedia' ? 'Tandai Terisi' : 'Tandai Tersedia' }}
              </button>
              <button
                @click="deleteRoom(kamar.id)"
                class="text-xs font-bold text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg transition"
                title="Hapus Kamar"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
          <button
            @click="closeRoomManager"
            class="px-4 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL: TAMBAH / EDIT KOS -->
    <!-- ================================================================= -->
    <div
      v-if="showKosModal"
      class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
      <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <h3 class="text-lg font-black text-slate-900">
            {{ isEditingKos ? 'Edit Data Kos' : 'Tambah Kos Baru' }}
          </h3>
          <button @click="showKosModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
        </div>

        <form @submit.prevent="saveKos" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Kos</label>
            <input
              type="text"
              v-model="kosForm.nama"
              required
              class="w-full text-sm rounded-xl border-slate-200 p-2.5"
              placeholder="Cth: Kos Melati Asri"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Kos</label>
              <select
                v-model="kosForm.jenis"
                required
                class="w-full text-sm rounded-xl border-slate-200 p-2.5 bg-white"
              >
                <option value="putra">Putra</option>
                <option value="putri">Putri</option>
                <option value="campur">Campur</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Acuan / Bulan (Rp)</label>
              <input
                type="number"
                v-model="kosForm.harga"
                required
                min="0"
                class="w-full text-sm rounded-xl border-slate-200 p-2.5"
                placeholder="Cth: 850000"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat Lengkap</label>
            <textarea
              v-model="kosForm.alamat"
              required
              rows="2"
              class="w-full text-sm rounded-xl border-slate-200 p-2.5"
              placeholder="Cth: Jl. Kaliurang KM 5 No. 12, Sleman, Yogyakarta"
            ></textarea>
            <p class="text-[11px] text-emerald-700 mt-1 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Sistem akan otomatis mencari koordinat (Latitude & Longitude) dari alamat ini melalui Geocoding OpenStreetMap.
            </p>
          </div>

          <!-- Opsional Manual Coordinates -->
          <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-2xl border border-slate-200">
            <div>
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">Latitude (Opsional)</label>
              <input
                type="number"
                step="any"
                v-model="kosForm.latitude"
                class="w-full text-xs rounded-xl border-slate-200 p-2 bg-white"
                placeholder="-7.770000"
              />
            </div>
            <div>
              <label class="block text-[11px] font-semibold text-slate-500 mb-1">Longitude (Opsional)</label>
              <input
                type="number"
                step="any"
                v-model="kosForm.longitude"
                class="w-full text-xs rounded-xl border-slate-200 p-2 bg-white"
                placeholder="110.370000"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Deskripsi & Peraturan</label>
            <textarea
              v-model="kosForm.deskripsi"
              rows="3"
              class="w-full text-sm rounded-xl border-slate-200 p-2.5"
              placeholder="Ceritakan fasilitas umum, jam malam, dan keunggulan kos Anda..."
            ></textarea>
          </div>

          <!-- Fasilitas Checkboxes -->
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-2">Fasilitas yang Disediakan</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-36 overflow-y-auto p-1">
              <label
                v-for="fas in availableFasilitas"
                :key="fas.id"
                class="flex items-center gap-2 text-xs text-slate-700 bg-slate-50 hover:bg-slate-100 p-2 rounded-xl border border-slate-200 cursor-pointer select-none"
              >
                <input
                  type="checkbox"
                  :value="fas.id"
                  v-model="kosForm.fasilitas_ids"
                  class="rounded text-emerald-600 focus:ring-emerald-500"
                />
                <span class="truncate">{{ fas.nama }}</span>
              </label>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showKosModal = false"
              class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="savingKos"
              class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition disabled:opacity-50 shadow-sm"
            >
              {{ savingKos ? 'Menyimpan...' : (isEditingKos ? 'Perbarui Kos' : 'Simpan Kos') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const auth = useAuthStore()

const activeMainTab = ref('kos')
const kosList = ref([])
const bookingsList = ref([])
const availableFasilitas = ref([])

const loadingKos = ref(false)
const loadingBookings = ref(false)
const savingKos = ref(false)
const errorMsg = ref(null)
const successMsg = ref(null)

// Room Manager Modal State
const roomManagerKos = ref(null)
const currentRooms = ref([])
const loadingRooms = ref(false)
const roomActionLoading = ref(false)
const roomForm = reactive({
  nama_kamar: '',
  harga: '',
})

// Kos Modal State
const showKosModal = ref(false)
const isEditingKos = ref(false)
const editingKosId = ref(null)
const kosForm = reactive({
  nama: '',
  alamat: '',
  deskripsi: '',
  jenis: 'campur',
  harga: '',
  latitude: '',
  longitude: '',
  fasilitas_ids: [],
})

const actionBookingLoading = ref(null)

const pendingBookingsCount = computed(() => {
  return bookingsList.value.filter((b) => b.status === 'menunggu').length
})

const fetchMyKos = async () => {
  loadingKos.value = true
  try {
    const res = await api.get('/kos', {
      params: { pemilik_id: auth.user?.id },
    })
    kosList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat daftar kos.'
  } finally {
    loadingKos.value = false
  }
}

const fetchBookings = async () => {
  loadingBookings.value = true
  try {
    const res = await api.get('/pemesanan')
    bookingsList.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat pemesanan.'
  } finally {
    loadingBookings.value = false
  }
}

const fetchFasilitas = async () => {
  try {
    const res = await api.get('/fasilitas')
    availableFasilitas.value = res.data.data || []
  } catch (err) {
    // Abaikan
  }
}

const openCreateKosModal = () => {
  isEditingKos.value = false
  editingKosId.value = null
  kosForm.nama = ''
  kosForm.alamat = ''
  kosForm.deskripsi = ''
  kosForm.jenis = 'campur'
  kosForm.harga = ''
  kosForm.latitude = ''
  kosForm.longitude = ''
  kosForm.fasilitas_ids = []
  showKosModal.value = true
}

const openEditKosModal = (kos) => {
  isEditingKos.value = true
  editingKosId.value = kos.id
  kosForm.nama = kos.nama
  kosForm.alamat = kos.alamat
  kosForm.deskripsi = kos.deskripsi || ''
  kosForm.jenis = kos.jenis
  kosForm.harga = kos.harga
  kosForm.latitude = kos.latitude || ''
  kosForm.longitude = kos.longitude || ''
  kosForm.fasilitas_ids = (kos.fasilitas || []).map((f) => f.id)
  showKosModal.value = true
}

const saveKos = async () => {
  savingKos.value = true
  errorMsg.value = null
  successMsg.value = null

  const payload = {
    nama: kosForm.nama,
    alamat: kosForm.alamat,
    deskripsi: kosForm.deskripsi,
    jenis: kosForm.jenis,
    harga: Number(kosForm.harga),
    latitude: kosForm.latitude !== '' ? Number(kosForm.latitude) : null,
    longitude: kosForm.longitude !== '' ? Number(kosForm.longitude) : null,
    fasilitas_ids: kosForm.fasilitas_ids,
  }

  try {
    if (isEditingKos.value) {
      await api.put(`/kos/${editingKosId.value}`, payload)
      successMsg.value = 'Data kos berhasil diperbarui!'
    } else {
      await api.post('/kos', payload)
      successMsg.value = 'Kos baru berhasil ditambahkan beserta geocoding alamat!'
    }
    showKosModal.value = false
    await fetchMyKos()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan data kos.'
  } finally {
    savingKos.value = false
  }
}

const deleteKos = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus properti kos ini beserta seluruh kamarnya?')) return
  try {
    await api.delete(`/kos/${id}`)
    successMsg.value = 'Kos berhasil dihapus.'
    await fetchMyKos()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menghapus kos.'
  }
}

// Room Management
const openRoomManager = async (kos) => {
  roomManagerKos.value = kos
  roomForm.nama_kamar = ''
  roomForm.harga = kos.harga
  await fetchRooms(kos.id)
}

const closeRoomManager = () => {
  roomManagerKos.value = null
  currentRooms.value = []
  fetchMyKos()
}

const fetchRooms = async (kosId) => {
  loadingRooms.value = true
  try {
    const res = await api.get(`/kos/${kosId}/kamar`)
    currentRooms.value = res.data.data || []
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memuat kamar.'
  } finally {
    loadingRooms.value = false
  }
}

const createRoom = async () => {
  roomActionLoading.value = true
  try {
    await api.post(`/kos/${roomManagerKos.value.id}/kamar`, {
      nama_kamar: roomForm.nama_kamar,
      harga: Number(roomForm.harga),
      status: 'tersedia',
    })
    roomForm.nama_kamar = ''
    await fetchRooms(roomManagerKos.value.id)
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menambah kamar.')
  } finally {
    roomActionLoading.value = false
  }
}

const toggleRoomStatus = async (kamar) => {
  const nextStatus = kamar.status === 'tersedia' ? 'terisi' : 'tersedia'
  try {
    await api.put(`/kamar/${kamar.id}`, {
      status: nextStatus,
    })
    await fetchRooms(roomManagerKos.value.id)
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal mengubah status kamar.')
  }
}

const deleteRoom = async (id) => {
  if (!confirm('Hapus unit kamar ini?')) return
  try {
    await api.delete(`/kamar/${id}`)
    await fetchRooms(roomManagerKos.value.id)
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menghapus kamar.')
  }
}

// Booking Management
const updateBookingStatus = async (id, status) => {
  actionBookingLoading.value = id
  try {
    await api.patch(`/pemesanan/${id}/status`, { status })
    successMsg.value = `Pemesanan berhasil di-${status}!`
    await fetchBookings()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal memperbarui status pemesanan.'
  } finally {
    actionBookingLoading.value = null
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

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const formatDate = (isoStr) => {
  if (!isoStr) return '-'
  return new Date(isoStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

onMounted(() => {
  fetchMyKos()
  fetchBookings()
  fetchFasilitas()
})
</script>
