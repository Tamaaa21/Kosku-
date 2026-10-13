<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md mb-8">
      <div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Kelola Kamar Kos</h1>
        <p class="text-xs text-on-surface-variant mt-1">
          Kelola unit kamar beserta fasilitas langsung di masing-masing kamar dan pantau ketersediaan unit.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <button
          @click="openAddModal"
          :disabled="!selectedKosId"
          class="h-10 px-5 rounded-lg bg-primary-container text-white text-xs font-semibold flex items-center gap-2 shadow-md hover:bg-primary transition active:scale-[0.98] disabled:opacity-50"
        >
          <span class="material-symbols-outlined text-[18px]">add</span>
          <span>Tambah Kamar</span>
        </button>
      </div>
    </div>

    <!-- Alert / Toast -->
    <div
      v-if="successMsg"
      class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-medium border border-emerald-200 flex items-center justify-between"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
        <span>{{ successMsg }}</span>
      </div>
      <button @click="successMsg = ''" class="text-emerald-700 font-bold ml-4 text-base">&times;</button>
    </div>
    <div
      v-if="errorMsg"
      class="mb-6 p-4 rounded-xl bg-rose-50 text-rose-800 text-xs font-medium border border-rose-200 flex items-center justify-between"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
        <span>{{ errorMsg }}</span>
      </div>
      <button @click="errorMsg = ''" class="text-rose-700 font-bold ml-4 text-base">&times;</button>
    </div>

    <!-- 4 Stats Cards in Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-space-md mb-8">
      <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-slate-100 flex items-center gap-space-md">
        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary shrink-0">
          <span class="material-symbols-outlined text-[26px]">meeting_room</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant font-medium">Total Kamar</p>
          <p class="text-xl font-bold text-on-surface">{{ kamarList.length }} Kamar</p>
        </div>
      </div>

      <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-slate-100 flex items-center gap-space-md">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
          <span class="material-symbols-outlined text-[26px]">check_circle</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant font-medium">Kamar Siap Huni</p>
          <p class="text-xl font-bold text-emerald-600">{{ countTersedia }} Kamar</p>
        </div>
      </div>

      <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-slate-100 flex items-center gap-space-md">
        <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary-container shrink-0">
          <span class="material-symbols-outlined text-[26px]">person</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant font-medium">Kamar Terisi</p>
          <p class="text-xl font-bold text-on-surface">{{ countTerisi }} Kamar</p>
        </div>
      </div>

      <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-slate-100 flex items-center gap-space-md">
        <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center text-primary shrink-0">
          <span class="material-symbols-outlined text-[26px]">payments</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant font-medium">Estimasi Pendapatan</p>
          <p class="text-xl font-bold text-primary">{{ formatRupiah(estimatedRevenue) }}</p>
        </div>
      </div>
    </div>

    <!-- Kos Selector & Filter Strip -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-slate-100 p-space-md mb-6">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col sm:flex-row sm:items-center gap-space-sm w-full lg:w-auto">
          <!-- Property Selector Dropdown -->
          <div class="relative min-w-[260px]">
            <label class="block text-[11px] uppercase tracking-wider text-outline mb-1 font-semibold" for="kos-select">
              Pilih Properti Kos
            </label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-primary">cottage</span>
              <select
                id="kos-select"
                v-model="selectedKosId"
                @change="onKosChange"
                class="w-full h-11 pl-10 pr-9 bg-surface-container-low text-on-surface rounded-lg text-xs font-semibold appearance-none cursor-pointer focus:outline-none focus:bg-white focus:shadow-md transition"
              >
                <option v-if="myKosList.length === 0" disabled value="">
                  Belum ada properti kos terdaftar
                </option>
                <option v-for="kos in myKosList" :key="kos.id" :value="kos.id">
                  {{ kos.nama }} ({{ kos.alamat }})
                </option>
              </select>
              <span class="material-symbols-outlined absolute right-3 top-2.5 text-[20px] text-outline pointer-events-none">expand_more</span>
            </div>
          </div>

          <!-- Search Input -->
          <div class="relative flex-1 sm:w-64 pt-5 sm:pt-0">
            <label class="block text-[11px] uppercase tracking-wider text-outline mb-1 font-semibold">
              Cari Unit
            </label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-outline">search</span>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nomor kamar..."
                class="w-full h-11 pl-9 pr-3 bg-surface-container-low text-on-surface placeholder:text-outline rounded-lg text-xs focus:outline-none focus:bg-white focus:shadow-md transition"
              />
            </div>
          </div>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-1.5 bg-surface-container-low p-1 rounded-xl self-start lg:self-end">
          <button
            @click="filterStatus = 'all'"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-semibold transition',
              filterStatus === 'all'
                ? 'bg-surface-container-lowest text-primary shadow-sm'
                : 'text-on-surface-variant hover:text-on-surface'
            ]"
            type="button"
          >
            Semua <span class="ml-1 px-1.5 py-0.5 rounded-full bg-primary-fixed text-primary text-[10px]">{{ kamarList.length }}</span>
          </button>
          <button
            @click="filterStatus = 'tersedia'"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-semibold transition',
              filterStatus === 'tersedia'
                ? 'bg-surface-container-lowest text-tertiary shadow-sm'
                : 'text-on-surface-variant hover:text-on-surface'
            ]"
            type="button"
          >
            Tersedia <span class="ml-1 px-1.5 py-0.5 rounded-full bg-surface-variant text-tertiary text-[10px]">{{ countTersedia }}</span>
          </button>
          <button
            @click="filterStatus = 'terisi'"
            :class="[
              'px-4 py-2 rounded-lg text-xs font-semibold transition',
              filterStatus === 'terisi'
                ? 'bg-surface-container-lowest text-on-surface shadow-sm'
                : 'text-on-surface-variant hover:text-on-surface'
            ]"
            type="button"
          >
            Terisi <span class="ml-1 px-1.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container text-[10px]">{{ countTerisi }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Room Inventory Table Card -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="overflow-x-auto w-full">
        <table class="w-full text-left">
          <thead class="bg-surface-container-low/70 text-on-surface-variant text-[11px] uppercase tracking-wider font-semibold select-none">
            <tr>
              <th class="py-3.5 px-space-md w-16">No</th>
              <th class="py-3.5 px-space-md">Foto & Detail Kamar</th>
              <th class="py-3.5 px-space-md">Fasilitas Utama Kamar</th>
              <th class="py-3.5 px-space-md">Harga Sewa</th>
              <th class="py-3.5 px-space-md">Status</th>
              <th class="py-3.5 px-space-md text-right w-36">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-on-surface">
            <tr v-if="loading" class="text-center">
              <td colspan="6" class="py-12 text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[28px] text-primary">progress_activity</span>
                <p class="mt-2 text-xs">Memuat data kamar...</p>
              </td>
            </tr>

            <tr v-else-if="filteredKamarList.length === 0" class="text-center">
              <td colspan="6" class="py-12 text-slate-400">
                <span class="material-symbols-outlined text-[36px] text-slate-300">meeting_room</span>
                <p class="mt-2 text-xs font-semibold text-slate-600">Belum ada unit kamar di kos ini.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Silakan tambahkan kamar baru untuk mulai mengelola unit.</p>
              </td>
            </tr>

            <tr
              v-for="(kamar, index) in filteredKamarList"
              :key="kamar.id"
              class="hover:bg-surface-container-low/40 transition group"
            >
              <td class="py-4 px-space-md font-medium text-secondary">
                {{ index + 1 }}
              </td>

              <td class="py-4 px-space-md">
                <div class="flex items-center gap-space-md">
                  <div class="w-14 h-14 rounded-lg bg-surface-container-high overflow-hidden shadow-sm shrink-0">
                    <img
                      :src="getRoomImage(kamar)"
                      :alt="kamar.nama_kamar"
                      class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    />
                  </div>
                  <div class="min-w-0">
                    <div class="flex items-center gap-2">
                      <span class="text-sm font-semibold text-on-surface">{{ kamar.nama_kamar }}</span>
                      <span
                        v-if="kamar.ukuran"
                        class="px-2 py-0.5 rounded bg-surface-container text-primary text-[10px] font-semibold flex items-center gap-0.5"
                      >
                        <span class="material-symbols-outlined text-[12px]">aspect_ratio</span>
                        <span>{{ kamar.ukuran }}</span>
                      </span>
                    </div>
                    <div class="flex items-center gap-1.5 text-on-surface-variant text-[11px] mt-0.5">
                      <span class="material-symbols-outlined text-[16px] text-outline">apartment</span>
                      <span>{{ currentKos?.nama || 'Unit Kos' }} &bull; {{ (kamar.foto || []).length }} Foto</span>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Fasilitas Kamar (Integrated directly into room) -->
              <td class="py-4 px-space-md">
                <div v-if="(kamar.fasilitas_kamar || []).length > 0" class="flex flex-wrap gap-1.5 max-w-xs">
                  <span
                    v-for="amenity in getRoomAmenities(kamar)"
                    :key="amenity.name"
                    class="px-2 py-0.5 rounded-full bg-surface-container-low text-primary text-[10px] font-medium flex items-center gap-1 border border-slate-100"
                  >
                    <span class="material-symbols-outlined text-[13px]">{{ amenity.icon }}</span>
                    <span>{{ amenity.name }}</span>
                  </span>
                </div>
                <span v-else class="text-[11px] text-slate-400 italic">Standar kos</span>
              </td>

              <td class="py-4 px-space-md">
                <div>
                  <span class="text-sm font-bold text-primary">{{ formatRupiah(kamar.harga) }}</span>
                  <span class="text-[11px] text-on-surface-variant"> / bulan</span>
                </div>
                <div v-if="kamar.harga_mingguan" class="text-[10px] font-semibold text-amber-700 mt-0.5">
                  {{ formatRupiah(kamar.harga_mingguan) }} / minggu
                </div>
                <div v-if="kamar.harga_harian" class="text-[10px] font-semibold text-emerald-700 mt-0.5">
                  {{ formatRupiah(kamar.harga_harian) }} / hari
                </div>
              </td>

              <td class="py-4 px-space-md">
                <button
                  @click="toggleKamarStatus(kamar)"
                  title="Klik untuk ubah status cepat"
                  :class="[
                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold transition cursor-pointer hover:shadow-sm',
                    kamar.status === 'tersedia'
                      ? 'bg-[#DCFCE7] text-[#15803D]'
                      : 'bg-slate-100 text-slate-700'
                  ]"
                >
                  <span
                    :class="[
                      'w-2 h-2 rounded-full',
                      kamar.status === 'tersedia' ? 'bg-[#15803D]' : 'bg-slate-500'
                    ]"
                  ></span>
                  <span class="capitalize">{{ kamar.status }}</span>
                </button>
              </td>

              <td class="py-4 px-space-md text-right">
                <div class="flex items-center justify-end gap-1">
                  <button
                    @click="openEditModal(kamar)"
                    class="p-2 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition"
                    title="Edit Kamar"
                  >
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                  </button>
                  <button
                    @click="confirmDeleteKamar(kamar)"
                    class="p-2 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition"
                    title="Hapus Kamar"
                  >
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Info -->
      <div class="p-space-md bg-surface-container-lowest border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-space-md select-none">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <span class="font-bold text-on-surface">{{ filteredKamarList.length }}</span> kamar pada
          <strong class="text-primary">{{ currentKos?.nama || 'Properti' }}</strong>
        </span>
        <div class="flex items-center gap-2">
          <router-link
            to="/dashboard/pemilik/pemesanan"
            class="text-xs text-primary font-semibold hover:underline flex items-center gap-1"
          >
            <span>Lihat permintaan pemesanan masuk</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit Kamar -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto"
    >
      <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[90vh] overflow-hidden">
        <!-- 1. Fixed Header -->
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-slate-100 bg-surface-container-lowest shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[24px]">meeting_room</span>
            </div>
            <div>
              <h3 class="text-base sm:text-lg font-bold text-on-surface">
                {{ isEditing ? 'Edit Data Kamar' : 'Tambah Unit Kamar' }}
              </h3>
              <p class="text-xs text-on-surface-variant">
                Properti: <strong class="text-primary">{{ currentKos?.nama }}</strong>
              </p>
            </div>
          </div>
          <button
            @click="closeModal"
            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition shrink-0"
            type="button"
          >
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <!-- 2. Scrollable Body & 3. Fixed Footer inside form -->
        <form @submit.prevent="saveKamar" class="flex flex-col flex-1 min-h-0 overflow-hidden">
          <!-- Scrollable Body -->
          <div class="overflow-y-auto p-5 sm:p-6 space-y-4 flex-1">
            <!-- Nama Kamar & Ukuran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">
                  Nomor / Nama Kamar <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="formData.nama_kamar"
                  type="text"
                  required
                  placeholder="Contoh: Kamar 1, Kamar A-02"
                  class="h-11 px-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">
                  Ukuran Kamar (P x L)
                </label>
                <input
                  v-model="formData.ukuran"
                  type="text"
                  placeholder="Contoh: 3 x 4 m, 3.5 x 4 m"
                  class="h-11 px-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
              </div>
            </div>

            <!-- Durasi Sewa & Tarif Kamar -->
            <div class="p-4 bg-surface-container-low/40 rounded-xl border border-slate-200 space-y-3">
              <div>
                <label class="text-xs font-semibold text-on-surface flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-[16px] text-primary">payments</span>
                  <span>Tarif Sewa Riil & Durasi Unit Kamar</span>
                </label>
                <p class="text-[11px] text-on-surface-variant mt-0.5">
                  Tentukan tarif sewa riil dan durasi yang berlaku untuk unit ini. Tarif terendah dari kamar-kamar Anda otomatis menjadi harga "Mulai Dari" pada profil listing kos.
                </p>
              </div>

              <!-- Durasi Checkboxes -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition">
                  <input
                    type="checkbox"
                    value="bulanan"
                    v-model="formData.durasi_sewa"
                    class="rounded text-primary focus:ring-primary h-4 w-4"
                  />
                  <div>
                    <span class="text-xs font-semibold text-slate-800 block">Bulanan</span>
                    <span class="text-[10px] text-slate-400">Standar</span>
                  </div>
                </label>

                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition">
                  <input
                    type="checkbox"
                    value="mingguan"
                    v-model="formData.durasi_sewa"
                    class="rounded text-primary focus:ring-primary h-4 w-4"
                  />
                  <div>
                    <span class="text-xs font-semibold text-slate-800 block">Mingguan</span>
                    <span class="text-[10px] text-slate-400">Fleksibel</span>
                  </div>
                </label>

                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 transition">
                  <input
                    type="checkbox"
                    value="harian"
                    v-model="formData.durasi_sewa"
                    class="rounded text-primary focus:ring-primary h-4 w-4"
                  />
                  <div>
                    <span class="text-xs font-semibold text-slate-800 block">Harian</span>
                    <span class="text-[10px] text-slate-400">Transit</span>
                  </div>
                </label>
              </div>

              <!-- Tarif Inputs -->
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                <div class="flex flex-col gap-1">
                  <label class="text-[11px] font-semibold text-on-surface">
                    Tarif Bulanan (Rp) <span class="text-rose-500">*</span>
                  </label>
                  <input
                    v-model.number="formData.harga"
                    type="number"
                    min="100000"
                    step="25000"
                    required
                    placeholder="750000"
                    class="h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                  />
                </div>

                <div class="flex flex-col gap-1">
                  <label class="text-[11px] font-semibold text-on-surface flex items-center justify-between">
                    <span>Tarif Mingguan (Rp)</span>
                    <span v-if="formData.durasi_sewa.includes('mingguan')" class="text-[9px] font-bold text-amber-600">Aktif</span>
                  </label>
                  <input
                    v-model.number="formData.harga_mingguan"
                    type="number"
                    min="25000"
                    step="25000"
                    :disabled="!formData.durasi_sewa.includes('mingguan')"
                    placeholder="Contoh: 300000"
                    class="h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:bg-slate-100 disabled:opacity-60"
                  />
                </div>

                <div class="flex flex-col gap-1">
                  <label class="text-[11px] font-semibold text-on-surface flex items-center justify-between">
                    <span>Tarif Harian (Rp)</span>
                    <span v-if="formData.durasi_sewa.includes('harian')" class="text-[9px] font-bold text-amber-600">Aktif</span>
                  </label>
                  <input
                    v-model.number="formData.harga_harian"
                    type="number"
                    min="10000"
                    step="10000"
                    :disabled="!formData.durasi_sewa.includes('harian')"
                    placeholder="Contoh: 75000"
                    class="h-10 px-3 bg-white border border-slate-200 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:bg-slate-100 disabled:opacity-60"
                  />
                </div>
              </div>
            </div>

            <!-- Status Ketersediaan -->
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-on-surface">Status Ketersediaan</label>
              <div class="relative">
                <select
                  v-model="formData.status"
                  class="w-full h-11 pl-3.5 pr-9 bg-surface-container-low/40 rounded-lg text-xs text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer"
                >
                  <option value="tersedia">Tersedia (Siap Huni)</option>
                  <option value="terisi">Terisi</option>
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-[20px] pointer-events-none">arrow_drop_down</span>
              </div>
            </div>

            <!-- Unggah Foto Kamar (Maksimal 5 Foto, Maksimal 2MB per Foto) -->
            <div class="p-3.5 bg-surface-container-low/40 rounded-xl border border-slate-200">
              <ImageUploader
                v-model="formData.foto"
                label="Foto Kamar"
                :max-files="5"
                :max-size-mb="2"
              />
            </div>

            <!-- Fasilitas Terpadu Kamar -->
            <div class="flex flex-col gap-2 p-3.5 bg-surface-container-low/40 rounded-xl border border-slate-200">
              <div class="flex items-center justify-between">
                <div>
                  <label class="text-xs font-semibold text-on-surface flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-primary">bed</span>
                    <span>Fasilitas Kamar (Terintegrasi)</span>
                  </label>
                  <p class="text-[11px] text-on-surface-variant">
                    Centang fasilitas yang tersedia di dalam unit kamar ini:
                  </p>
                </div>
                <span class="text-[11px] font-semibold text-primary bg-primary/10 px-2 py-0.5 rounded-full">
                  {{ formData.fasilitas_kamar.length }} Fasilitas Dipilih
                </span>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                <label
                  v-for="f in defaultAmenities"
                  :key="f.name"
                  class="inline-flex items-center gap-2 p-2 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition"
                >
                  <input
                    type="checkbox"
                    :value="f.name"
                    v-model="formData.fasilitas_kamar"
                    class="rounded text-primary focus:ring-primary h-4 w-4"
                  />
                  <span class="material-symbols-outlined text-[16px] text-primary">{{ f.icon }}</span>
                  <span class="text-xs text-slate-700 font-medium">{{ f.name }}</span>
                </label>
              </div>

              <!-- Custom Room Amenity Input -->
              <div class="flex items-center gap-2 pt-1">
                <input
                  v-model="customAmenityInput"
                  type="text"
                  placeholder="Tambah fasilitas kamar lainnya..."
                  class="flex-1 h-9 px-3 bg-white rounded-lg text-xs text-on-surface border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20"
                  @keydown.enter.prevent="addCustomAmenity"
                />
                <button
                  type="button"
                  @click="addCustomAmenity"
                  :disabled="!customAmenityInput.trim()"
                  class="h-9 px-3.5 bg-primary-container text-white text-xs font-semibold rounded-lg hover:bg-primary transition disabled:opacity-50 flex items-center gap-1 shrink-0"
                >
                  <span class="material-symbols-outlined text-[15px]">add</span>
                  <span>Tambah</span>
                </button>
              </div>
            </div>
          </div>

          <!-- 3. Fixed Footer Actions -->
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-surface-container-low/50 shrink-0">
            <button
              type="button"
              @click="closeModal"
              class="h-10 px-5 rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container transition text-xs font-semibold"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="h-10 px-6 rounded-lg bg-primary-container text-white text-xs font-semibold hover:bg-primary shadow-sm transition flex items-center gap-1.5 disabled:opacity-60"
            >
              <span v-if="saving" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
              <span>{{ isEditing ? 'Simpan Perubahan' : 'Tambah Kamar' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import api from '../services/api'
import ImageUploader from '../components/ImageUploader.vue'

const route = useRoute()
const auth = useAuthStore()
const ui = useUiStore()

const loading = ref(false)
const saving = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

const myKosList = ref([])
const selectedKosId = ref('')
const kamarList = ref([])

const searchQuery = ref('')
const filterStatus = ref('all')

const showModal = ref(false)
const isEditing = ref(false)
const editKamarId = ref(null)

const defaultAmenities = [
  { name: 'Kasur', icon: 'bed' },
  { name: 'Kamar Mandi Dalam', icon: 'shower' },
  { name: 'AC', icon: 'ac_unit' },
  { name: 'WiFi', icon: 'wifi' },
  { name: 'Lemari Pakaian', icon: 'shelves' },
  { name: 'Meja Belajar', icon: 'desk' },
  { name: 'Water Heater', icon: 'hot_tub' },
  { name: 'Jendela / Ventilasi', icon: 'window' }
]

const customAmenityInput = ref('')

const formData = ref({
  nama_kamar: '',
  ukuran: '3 x 4 m',
  harga: 750000,
  harga_harian: null,
  harga_mingguan: null,
  durasi_sewa: ['bulanan'],
  status: 'tersedia',
  foto: [],
  fasilitas_kamar: ['Kasur', 'Kamar Mandi Dalam', 'AC', 'WiFi']
})

const addCustomAmenity = () => {
  const val = customAmenityInput.value.trim()
  if (!val) return
  if (!formData.value.fasilitas_kamar.includes(val)) {
    formData.value.fasilitas_kamar.push(val)
  }
  customAmenityInput.value = ''
}

const resolveImageUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  const baseBackend = import.meta.env.VITE_API_URL
    ? import.meta.env.VITE_API_URL.replace(/\/api\/v1\/?$/, '')
    : 'https://backend-kos.ddev.site'
  return `${baseBackend}${url.startsWith('/') ? '' : '/'}${url}`
}

const fetchKosList = async () => {
  try {
    const res = await api.get('/kos', {
      params: { pemilik_id: auth.user?.id, per_page: 50 }
    })
    myKosList.value = res.data?.data || []

    const queryKosId = Number(route.query.kos_id)
    if (queryKosId && myKosList.value.some((k) => k.id === queryKosId)) {
      selectedKosId.value = queryKosId
    } else if (myKosList.value.length > 0 && !selectedKosId.value) {
      selectedKosId.value = myKosList.value[0].id
    }

    if (selectedKosId.value) {
      await fetchKamar()
    }
  } catch (err) {
    errorMsg.value = 'Gagal memuat properti kos.'
  }
}

const fetchKamar = async () => {
  if (!selectedKosId.value) return
  loading.value = true
  try {
    const res = await api.get(`/kos/${selectedKosId.value}/kamar`)
    kamarList.value = res.data?.data || []
  } catch (err) {
    errorMsg.value = 'Gagal memuat kamar kos.'
  } finally {
    loading.value = false
  }
}

const onKosChange = () => {
  fetchKamar()
}

onMounted(() => {
  fetchKosList()
})

const currentKos = computed(() => {
  return myKosList.value.find((k) => k.id === Number(selectedKosId.value)) || null
})

const filteredKamarList = computed(() => {
  return kamarList.value
    .filter((kamar) => {
      const matchSearch =
        !searchQuery.value ||
        kamar.nama_kamar.toLowerCase().includes(searchQuery.value.toLowerCase())

      const matchStatus =
        filterStatus.value === 'all' || kamar.status === filterStatus.value

      return matchSearch && matchStatus
    })
    .sort((a, b) => a.id - b.id)
})

const countTersedia = computed(() => {
  return kamarList.value.filter((k) => k.status === 'tersedia').length
})

const countTerisi = computed(() => {
  return kamarList.value.filter((k) => k.status === 'terisi').length
})

const estimatedRevenue = computed(() => {
  return kamarList.value.reduce((acc, k) => acc + (k.harga || 0), 0)
})

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val)
}

const getRoomImage = (kamar) => {
  if (kamar.foto && kamar.foto.length > 0) {
    return resolveImageUrl(kamar.foto[0])
  }
  const images = [
    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=400&q=80',
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=400&q=80',
    'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=400&q=80'
  ]
  return images[(kamar.id || 0) % images.length]
}

const amenityIconMap = {
  'Kasur': 'bed',
  'KM Dalam': 'shower',
  'Kamar Mandi Dalam': 'shower',
  'AC': 'ac_unit',
  'WiFi': 'wifi',
  'Lemari': 'shelves',
  'Lemari Pakaian': 'shelves',
  'Meja Belajar': 'desk',
  'Water Heater': 'hot_tub',
  'Jendela': 'window',
  'Jendela / Ventilasi': 'window'
}

const getRoomAmenities = (kamar) => {
  const list = kamar.fasilitas_kamar || []
  return list.map((name) => ({
    name,
    icon: amenityIconMap[name] || 'check_circle'
  }))
}

const toggleKamarStatus = async (kamar) => {
  const newStatus = kamar.status === 'tersedia' ? 'terisi' : 'tersedia'
  try {
    await api.put(`/kamar/${kamar.id}`, {
      status: newStatus
    })
    kamar.status = newStatus
    successMsg.value = `Status ${kamar.nama_kamar} diubah menjadi ${newStatus}.`
  } catch (err) {
    errorMsg.value = 'Gagal memperbarui status kamar.'
  }
}

const openAddModal = () => {
  isEditing.value = false
  editKamarId.value = null
  formData.value = {
    nama_kamar: `Kamar ${kamarList.value.length + 1}`,
    ukuran: '3 x 4 m',
    harga: currentKos.value?.harga || 750000,
    harga_harian: currentKos.value?.harga_harian || null,
    harga_mingguan: currentKos.value?.harga_mingguan || null,
    durasi_sewa: Array.isArray(currentKos.value?.durasi_sewa) && currentKos.value.durasi_sewa.length > 0
      ? [...currentKos.value.durasi_sewa]
      : ['bulanan'],
    status: 'tersedia',
    foto: [],
    fasilitas_kamar: ['Kasur', 'Kamar Mandi Dalam', 'AC', 'WiFi']
  }
  showModal.value = true
}

const openEditModal = (kamar) => {
  isEditing.value = true
  editKamarId.value = kamar.id
  formData.value = {
    nama_kamar: kamar.nama_kamar,
    ukuran: kamar.ukuran || '3 x 4 m',
    harga: kamar.harga,
    harga_harian: kamar.harga_harian ?? null,
    harga_mingguan: kamar.harga_mingguan ?? null,
    durasi_sewa: Array.isArray(kamar.durasi_sewa) && kamar.durasi_sewa.length > 0
      ? [...kamar.durasi_sewa]
      : (Array.isArray(currentKos.value?.durasi_sewa) && currentKos.value.durasi_sewa.length > 0 ? [...currentKos.value.durasi_sewa] : ['bulanan']),
    status: kamar.status,
    foto: Array.isArray(kamar.foto) ? [...kamar.foto] : [],
    fasilitas_kamar: Array.isArray(kamar.fasilitas_kamar) && kamar.fasilitas_kamar.length > 0
      ? [...kamar.fasilitas_kamar]
      : ['Kasur', 'Kamar Mandi Dalam', 'AC', 'WiFi']
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveKamar = async () => {
  saving.value = true
  errorMsg.value = ''
  successMsg.value = ''

  try {
    if (isEditing.value) {
      await api.put(`/kamar/${editKamarId.value}`, formData.value)
      successMsg.value = 'Data kamar berhasil diperbarui!'
    } else {
      await api.post(`/kos/${selectedKosId.value}/kamar`, formData.value)
      successMsg.value = 'Unit kamar baru berhasil ditambahkan!'
    }
    showModal.value = false
    await fetchKamar()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan data kamar.'
  } finally {
    saving.value = false
  }
}

const confirmDeleteKamar = async (kamar) => {
  const confirmed = await ui.confirm({
    title: 'Hapus Unit Kamar?',
    message: `Apakah Anda yakin ingin menghapus unit "${kamar.nama_kamar}"? Tindakan ini tidak dapat dibatalkan.`,
    confirmText: 'Ya, Hapus Unit',
    cancelText: 'Batal',
    confirmType: 'danger'
  })

  if (!confirmed) return

  try {
    await api.delete(`/kamar/${kamar.id}`)
    ui.success(`Unit ${kamar.nama_kamar} berhasil dihapus.`)
    successMsg.value = `Unit ${kamar.nama_kamar} berhasil dihapus.`
    await fetchKamar()
  } catch (err) {
    ui.error(err.response?.data?.message || 'Gagal menghapus unit kamar.')
    errorMsg.value = err.response?.data?.message || 'Gagal menghapus unit kamar.'
  }
}
</script>
