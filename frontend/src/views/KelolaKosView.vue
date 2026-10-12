<template>
  <div class="max-w-7xl mx-auto px-margin py-space-lg pt-20">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md mb-8">
      <div>
        <div class="flex items-center gap-1.5 text-primary text-xs font-bold uppercase tracking-wider mb-1">
          <span class="material-symbols-outlined text-[16px]">domain</span>
          <span>Manajemen Properti</span>
        </div>
        <h1 class="text-2xl font-bold text-on-surface tracking-tight">Data Kos</h1>
        <p class="text-xs text-on-surface-variant mt-0.5">
          Kelola informasi kos, ketersediaan unit, dan detail operasional properti Anda di seluruh Indonesia.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <router-link
          to="/dashboard/pemilik"
          class="h-10 px-4 rounded-lg bg-surface-container text-on-surface text-xs font-semibold flex items-center gap-1.5 hover:bg-surface-container-high transition shadow-sm"
        >
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          <span>Dashboard</span>
        </router-link>
        <button
          @click="openAddModal"
          class="h-10 px-5 rounded-lg bg-primary-container text-white text-xs font-semibold flex items-center gap-2 shadow-md hover:bg-primary transition active:scale-[0.98]"
        >
          <span class="material-symbols-outlined text-[18px]">add</span>
          <span>Tambah Kos Baru</span>
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

    <!-- Quick Stat KPI Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md mb-8">
      <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-slate-100 flex items-center justify-between">
        <div class="flex flex-col">
          <span class="text-xs text-on-surface-variant font-medium">Total Properti Kos</span>
          <span class="text-2xl font-black text-on-surface mt-1">
            {{ myKosList.length }} <span class="text-xs font-normal text-secondary">Unit</span>
          </span>
        </div>
        <div class="w-11 h-11 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
          <span class="material-symbols-outlined text-[24px]">apartment</span>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-slate-100 flex items-center justify-between">
        <div class="flex flex-col">
          <span class="text-xs text-on-surface-variant font-medium">Total Kamar Terisi</span>
          <span class="text-2xl font-black text-on-surface mt-1">
            {{ totalKamarTerisi }} <span class="text-xs font-normal text-secondary">/ {{ totalKamar }} Kamar</span>
          </span>
        </div>
        <div class="w-11 h-11 rounded-lg bg-surface-container-high flex items-center justify-center text-tertiary">
          <span class="material-symbols-outlined text-[24px]">bed</span>
        </div>
      </div>

      <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-slate-100 flex items-center justify-between">
        <div class="flex flex-col">
          <span class="text-xs text-on-surface-variant font-medium">Rata-rata Okupansi</span>
          <span class="text-2xl font-black text-primary mt-1">{{ occupancyRate }}%</span>
        </div>
        <div class="w-11 h-11 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-container">
          <span class="material-symbols-outlined text-[24px]">donut_large</span>
        </div>
      </div>
    </div>

    <!-- Filter & Search Toolbar Container -->
    <div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row gap-space-sm items-stretch md:items-center justify-between">
      <div class="relative flex-1 min-w-0">
        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama kos atau alamat..."
          class="w-full h-11 pl-10 pr-space-md bg-surface-container-low/40 rounded-lg text-xs text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/20"
        />
      </div>

      <div class="flex flex-wrap items-center gap-space-sm">
        <div class="relative min-w-[150px] flex-1 sm:flex-initial">
          <select
            v-model="selectedJenis"
            class="w-full h-11 pl-3.5 pr-9 bg-surface-container-low/40 rounded-lg text-xs text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer"
          >
            <option value="">Semua Jenis</option>
            <option value="putri">Putri</option>
            <option value="putra">Putra</option>
            <option value="campur">Campur</option>
          </select>
          <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-[20px] pointer-events-none">arrow_drop_down</span>
        </div>

        <button
          @click="resetFilters"
          title="Reset Filter"
          class="h-11 px-3.5 rounded-lg bg-surface-container-high hover:bg-surface-container text-on-surface flex items-center justify-center transition"
        >
          <span class="material-symbols-outlined text-[20px]">refresh</span>
        </button>
      </div>
    </div>

    <!-- Main Property Table Card -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-8">
      <div class="overflow-x-auto w-full">
        <table class="w-full text-left">
          <thead class="bg-surface-container-low/80 text-on-surface-variant text-[11px] uppercase tracking-wider font-semibold select-none">
            <tr>
              <th class="py-3.5 px-space-md w-14 text-center">No</th>
              <th class="py-3.5 px-space-sm w-24">Foto</th>
              <th class="py-3.5 px-space-md">Nama Kos</th>
              <th class="py-3.5 px-space-md">Alamat</th>
              <th class="py-3.5 px-space-md">Jenis</th>
              <th class="py-3.5 px-space-md text-center">Kamar</th>
              <th class="py-3.5 px-space-md text-center w-52">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs text-on-surface">
            <tr v-if="loading" class="text-center">
              <td colspan="7" class="py-12 text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[28px] text-primary">progress_activity</span>
                <p class="mt-2 text-xs">Memuat data kos Anda...</p>
              </td>
            </tr>

            <tr v-else-if="filteredKosList.length === 0" class="text-center">
              <td colspan="7" class="py-12 text-slate-400">
                <span class="material-symbols-outlined text-[36px] text-slate-300">apartment</span>
                <p class="mt-2 text-xs font-semibold text-slate-600">Belum ada properti kos yang sesuai.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Silakan tambahkan kos baru atau sesuaikan kata kunci pencarian.</p>
              </td>
            </tr>

            <tr
              v-for="(kos, index) in filteredKosList"
              :key="kos.id"
              class="hover:bg-surface-container-low/40 transition group"
            >
              <td class="py-4 px-space-md text-center font-medium text-secondary">
                {{ index + 1 }}
              </td>

              <td class="py-4 px-space-sm">
                <div class="w-16 h-14 rounded-lg overflow-hidden bg-surface-container-high shrink-0 shadow-sm relative">
                  <img
                    :src="getImageForKos(kos)"
                    :alt="kos.nama"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                  />
                </div>
              </td>

              <td class="py-4 px-space-md min-w-[200px]">
                <div class="flex flex-col">
                  <div class="flex items-center gap-1.5">
                    <span class="text-sm font-semibold text-on-surface group-hover:text-primary transition">
                      {{ kos.nama }}
                    </span>
                    <span class="inline-flex items-center text-tertiary" title="Terverifikasi">
                      <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </span>
                  </div>
                  <span class="text-[11px] text-on-surface-variant truncate max-w-xs mt-0.5">
                    {{ kos.deskripsi || 'Tidak ada deskripsi khusus' }}
                  </span>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] text-primary bg-surface-container-high px-2 py-0.5 rounded-full font-semibold">
                      {{ kos.kamar?.length || 0 }} Kamar
                    </span>
                    <span class="text-[10px] text-secondary font-medium">
                      Mulai {{ formatRupiah(kos.harga) }}/bln
                    </span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-space-md">
                <div class="flex flex-col">
                  <span class="text-xs font-medium text-on-surface">{{ kos.alamat }}</span>
                  <span class="text-[11px] text-secondary">Indonesia</span>
                </div>
              </td>

              <td class="py-4 px-space-md">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold capitalize',
                    kos.jenis === 'putri' ? 'bg-[#FCE7F3] text-[#BE185D]' :
                    kos.jenis === 'putra' ? 'bg-[#E0F2FE] text-[#0369A1]' :
                    'bg-[#EDE9FE] text-[#6D28D9]'
                  ]"
                >
                  {{ kos.jenis }}
                </span>
              </td>

              <td class="py-4 px-space-md text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-[#DCFCE7] text-[#15803D]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                  {{ getAvailableKamarCount(kos) }} Tersedia
                </span>
              </td>

              <td class="py-4 px-space-md text-center">
                <div class="inline-flex items-center justify-center gap-1.5">
                  <router-link
                    :to="`/dashboard/pemilik/kamar?kos_id=${kos.id}`"
                    class="h-8 px-2.5 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition flex items-center gap-1 text-[11px] font-semibold"
                    title="Kelola Kamar Kos Ini"
                  >
                    <span class="material-symbols-outlined text-[16px]">meeting_room</span>
                    <span>Kamar</span>
                  </router-link>

                  <router-link
                    :to="`/kos/${kos.id}`"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container transition"
                    title="Lihat Halaman Publik"
                  >
                    <span class="material-symbols-outlined text-[19px]">visibility</span>
                  </router-link>

                  <button
                    @click="openEditModal(kos)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container transition"
                    title="Ubah Data Kos"
                  >
                    <span class="material-symbols-outlined text-[19px]">edit</span>
                  </button>

                  <button
                    @click="confirmDeleteKos(kos)"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition"
                    title="Hapus Kos"
                  >
                    <span class="material-symbols-outlined text-[19px]">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination / Count Footer -->
      <div class="p-space-md bg-surface-container-lowest border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-space-md select-none">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <span class="font-bold text-on-surface">{{ filteredKosList.length }}</span> dari
          <span class="font-bold text-on-surface">{{ myKosList.length }}</span> properti kos
        </span>
        <div class="flex items-center gap-2">
          <router-link
            to="/dashboard/pemilik/kamar"
            class="text-xs text-primary font-semibold hover:underline flex items-center gap-1"
          >
            <span>Kelola unit kamar masing-masing</span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah / Edit Kos -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto"
    >
      <div class="bg-surface-container-lowest rounded-2xl max-w-3xl w-full shadow-2xl border border-slate-100 my-auto flex flex-col max-h-[90vh] overflow-hidden">
        <!-- 1. Fixed Header -->
        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-slate-100 bg-surface-container-lowest shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[24px]">
                {{ isEditing ? 'edit_note' : 'add_business' }}
              </span>
            </div>
            <div>
              <h3 class="text-base sm:text-lg font-bold text-on-surface">
                {{ isEditing ? 'Edit Data Properti Kos' : 'Tambah Properti Kos Baru' }}
              </h3>
              <p class="text-xs text-on-surface-variant">
                Lengkapi formulir informasi properti, galeri foto, titik lokasi peta, dan tata tertib kos.
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
        <form @submit.prevent="saveKos" class="flex flex-col flex-1 min-h-0 overflow-hidden">
          <!-- Scrollable Body -->
          <div class="overflow-y-auto p-5 sm:p-6 space-y-5 flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Nama Kos -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">
                  Nama Kos <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="formData.nama"
                  type="text"
                  required
                  placeholder="Contoh: Kos Melati Indah"
                  class="h-11 px-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <!-- Jenis Kos -->
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">
                  Kategori Penghuni <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                  <select
                    v-model="formData.jenis"
                    required
                    class="w-full h-11 pl-3.5 pr-9 bg-surface-container-low/40 rounded-lg text-xs text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20"
                  >
                    <option value="putri">Putri (Khusus Wanita)</option>
                    <option value="putra">Putra (Khusus Pria)</option>
                    <option value="campur">Campur</option>
                  </select>
                  <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-outline text-[20px] pointer-events-none">arrow_drop_down</span>
                </div>
              </div>
            </div>

            <!-- Alamat Lengkap -->
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-on-surface">
                Alamat Lengkap (Beserta Kota & Provinsi) <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="formData.alamat"
                type="text"
                required
                placeholder="Contoh: Jl. Melati No. 12, Kelurahan, Kecamatan, Sleman, Yogyakarta"
                class="h-11 px-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
              />
            </div>

            <!-- Peta Lokasi Responsif -->
            <div class="p-3 bg-surface-container-low/40 rounded-xl border border-slate-200">
              <LocationPickerMap
                ref="locationPickerRef"
                :latitude="formData.latitude"
                :longitude="formData.longitude"
                :address="formData.alamat"
                @update:latitude="(val) => (formData.latitude = val)"
                @update:longitude="(val) => (formData.longitude = val)"
              />
            </div>

            <!-- Harga Mulai & Deskripsi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">
                  Harga Mulai / Bulan (Rp) <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model.number="formData.harga"
                  type="number"
                  min="100000"
                  step="50000"
                  required
                  placeholder="750000"
                  class="h-11 px-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
              </div>

              <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-on-surface">Deskripsi Singkat</label>
                <input
                  v-model="formData.deskripsi"
                  type="text"
                  placeholder="Lingkungan tenang, aman, dekat kampus..."
                  class="h-11 px-3.5 bg-surface-container-low/40 rounded-lg text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20"
                />
              </div>
            </div>

            <!-- Unggah Foto Kos (Maksimal 5 Foto, Maksimal 2MB per Foto) -->
            <div class="p-3.5 bg-surface-container-low/40 rounded-xl border border-slate-200">
              <ImageUploader
                v-model="formData.foto"
                label="Galeri Foto Kos"
                :max-files="5"
                :max-size-mb="2"
              />
            </div>

            <!-- Tata Tertib / Peraturan Kos -->
            <div class="flex flex-col gap-2 p-3.5 bg-surface-container-low/40 rounded-xl border border-slate-200">
              <div class="flex items-center justify-between">
                <div>
                  <label class="text-xs font-semibold text-on-surface flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-primary">policy</span>
                    <span>Tata Tertib & Peraturan Kos</span>
                  </label>
                  <p class="text-[11px] text-on-surface-variant">
                    Pilih aturan umum atau tambahkan peraturan khusus untuk penghuni kos Anda.
                  </p>
                </div>
                <span class="text-[11px] font-semibold text-primary bg-primary/10 px-2 py-0.5 rounded-full">
                  {{ formData.peraturan.length }} Aturan Ditentukan
                </span>
              </div>

              <!-- Standard Rules Chips -->
              <div class="flex flex-wrap gap-1.5 pt-1">
                <button
                  v-for="rule in standardRules"
                  :key="rule"
                  type="button"
                  @click="toggleRule(rule)"
                  :class="[
                    'px-2.5 py-1 rounded-full text-[11px] font-medium transition flex items-center gap-1 border',
                    formData.peraturan.includes(rule)
                      ? 'bg-primary text-white border-primary shadow-xs'
                      : 'bg-white text-slate-700 border-slate-200 hover:border-primary/50'
                  ]"
                >
                  <span class="material-symbols-outlined text-[13px]">
                    {{ formData.peraturan.includes(rule) ? 'check' : 'add' }}
                  </span>
                  <span>{{ rule }}</span>
                </button>
              </div>

              <!-- Custom Rule Input -->
              <div class="flex items-center gap-2 pt-2">
                <input
                  v-model="customRuleInput"
                  type="text"
                  placeholder="Ketik peraturan khusus lainnya..."
                  class="flex-1 h-9 px-3 bg-white rounded-lg text-xs text-on-surface border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20"
                  @keydown.enter.prevent="addCustomRule"
                />
                <button
                  type="button"
                  @click="addCustomRule"
                  :disabled="!customRuleInput.trim()"
                  class="h-9 px-3.5 bg-primary-container text-white text-xs font-semibold rounded-lg hover:bg-primary transition disabled:opacity-50 flex items-center gap-1 shrink-0"
                >
                  <span class="material-symbols-outlined text-[15px]">add</span>
                  <span>Tambah Aturan</span>
                </button>
              </div>

              <!-- Selected Rules Tag List -->
              <div v-if="formData.peraturan.length > 0" class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-200/80">
                <span
                  v-for="(rule, idx) in formData.peraturan"
                  :key="idx"
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-medium"
                >
                  <span class="material-symbols-outlined text-emerald-600 text-[13px]">verified_user</span>
                  <span>{{ rule }}</span>
                  <button
                    type="button"
                    @click="removeRule(idx)"
                    class="text-emerald-700 hover:text-emerald-950 font-bold ml-1 text-xs"
                    title="Hapus peraturan ini"
                  >
                    &times;
                  </button>
                </span>
              </div>
            </div>

            <!-- Fasilitas Utama -->
            <div class="flex flex-col gap-1.5 pt-1">
              <div class="flex items-center justify-between">
                <label class="text-xs font-semibold text-on-surface">Fasilitas Properti yang Disediakan</label>
                <button
                  type="button"
                  @click="showAddCustomFasilitas = !showAddCustomFasilitas"
                  class="text-[11px] font-bold text-primary hover:underline flex items-center gap-1"
                >
                  <span class="material-symbols-outlined text-[15px]">{{ showAddCustomFasilitas ? 'remove' : 'add' }}</span>
                  {{ showAddCustomFasilitas ? 'Tutup' : 'Tambah Fasilitas Lain' }}
                </button>
              </div>

              <!-- Inline Add Custom Facility Input -->
              <div v-if="showAddCustomFasilitas" class="p-3 bg-surface-container-low/60 rounded-xl border border-slate-200 space-y-2 mt-1">
                <span class="text-[11px] text-slate-500 font-medium">Daftarkan fasilitas baru yang belum ada di katalog:</span>
                <div class="flex items-center gap-2">
                  <input
                    v-model="customFasilitasName"
                    type="text"
                    placeholder="Contoh: Dapur Pribadi, CCTV 24 Jam, Water Heater..."
                    class="flex-1 h-9 px-3 bg-white rounded-lg text-xs text-on-surface border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary/20"
                    @keydown.enter.prevent="createCustomFasilitas"
                  />
                  <button
                    type="button"
                    @click="createCustomFasilitas"
                    :disabled="creatingCustomFasilitas || !customFasilitasName.trim()"
                    class="h-9 px-3.5 bg-primary-container text-white text-xs font-semibold rounded-lg hover:bg-primary transition disabled:opacity-50 flex items-center gap-1 shrink-0"
                  >
                    <span v-if="creatingCustomFasilitas" class="material-symbols-outlined animate-spin text-[14px]">progress_activity</span>
                    <span>Tambah</span>
                  </button>
                </div>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                <label
                  v-for="item in availableFasilitas"
                  :key="item.id"
                  class="inline-flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition"
                >
                  <input
                    type="checkbox"
                    :value="item.id"
                    v-model="formData.fasilitas_ids"
                    class="rounded text-primary focus:ring-primary h-4 w-4"
                  />
                  <span class="text-xs font-medium text-slate-700">{{ item.nama }}</span>
                </label>
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
              <span>{{ isEditing ? 'Simpan Perubahan' : 'Simpan Kos Baru' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import ImageUploader from '../components/ImageUploader.vue'
import LocationPickerMap from '../components/LocationPickerMap.vue'

const auth = useAuthStore()

const loading = ref(false)
const saving = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

const myKosList = ref([])
const availableFasilitas = ref([])
const searchQuery = ref('')
const selectedJenis = ref('')

const showModal = ref(false)
const isEditing = ref(false)
const editKosId = ref(null)
const locationPickerRef = ref(null)

const standardRules = [
  'Dilarang merokok di dalam kamar',
  'Batas jam malam pukul 23.00 WIB',
  'Tamu lawan jenis dilarang menginap',
  'Wajib menjaga kebersihan bersama',
  'Dilarang membawa hewan peliharaan',
  'Dilarang membuat kegaduhan / suara bising',
  'Hemat penggunaan listrik dan air',
  'Tamu menginap wajib izin pengelola'
]

const customRuleInput = ref('')

const formData = ref({
  nama: '',
  jenis: 'putri',
  alamat: '',
  deskripsi: '',
  harga: 750000,
  latitude: null,
  longitude: null,
  peraturan: [],
  foto: [],
  fasilitas_ids: []
})

const toggleRule = (rule) => {
  const idx = formData.value.peraturan.indexOf(rule)
  if (idx > -1) {
    formData.value.peraturan.splice(idx, 1)
  } else {
    formData.value.peraturan.push(rule)
  }
}

const addCustomRule = () => {
  const text = customRuleInput.value.trim()
  if (!text) return
  if (!formData.value.peraturan.includes(text)) {
    formData.value.peraturan.push(text)
  }
  customRuleInput.value = ''
}

const removeRule = (idx) => {
  formData.value.peraturan.splice(idx, 1)
}

const resolveImageUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  const baseBackend = import.meta.env.VITE_API_URL
    ? import.meta.env.VITE_API_URL.replace(/\/api\/v1\/?$/, '')
    : 'https://backend-kos.ddev.site'
  return `${baseBackend}${url.startsWith('/') ? '' : '/'}${url}`
}

const fetchKos = async () => {
  loading.value = true
  try {
    const res = await api.get('/kos', {
      params: { pemilik_id: auth.user?.id, per_page: 50 }
    })
    myKosList.value = res.data?.data || []
  } catch (err) {
    errorMsg.value = 'Gagal memuat daftar kos Anda.'
  } finally {
    loading.value = false
  }
}

const fetchFasilitas = async () => {
  try {
    const res = await api.get('/fasilitas')
    availableFasilitas.value = res.data?.data || []
  } catch (err) {
    console.error('Failed to load fasilitas:', err)
  }
}

const showAddCustomFasilitas = ref(false)
const customFasilitasName = ref('')
const creatingCustomFasilitas = ref(false)

const createCustomFasilitas = async () => {
  const name = customFasilitasName.value.trim()
  if (!name) return

  const existing = availableFasilitas.value.find(
    (f) => f.nama.toLowerCase() === name.toLowerCase()
  )
  if (existing) {
    if (!formData.value.fasilitas_ids.includes(existing.id)) {
      formData.value.fasilitas_ids.push(existing.id)
    }
    customFasilitasName.value = ''
    showAddCustomFasilitas.value = false
    return
  }

  creatingCustomFasilitas.value = true
  try {
    const res = await api.post('/fasilitas', { nama: name })
    const newFas = res.data?.data
    if (newFas) {
      availableFasilitas.value.push(newFas)
      formData.value.fasilitas_ids.push(newFas.id)
      customFasilitasName.value = ''
      showAddCustomFasilitas.value = false
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menambahkan fasilitas baru.')
  } finally {
    creatingCustomFasilitas.value = false
  }
}

onMounted(() => {
  fetchKos()
  fetchFasilitas()
})

const filteredKosList = computed(() => {
  return myKosList.value.filter((kos) => {
    const matchSearch =
      !searchQuery.value ||
      kos.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      kos.alamat.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchJenis =
      !selectedJenis.value || kos.jenis.toLowerCase() === selectedJenis.value.toLowerCase()

    return matchSearch && matchJenis
  })
})

const totalKamar = computed(() => {
  return myKosList.value.reduce((acc, k) => acc + (k.kamar?.length || 0), 0)
})

const totalKamarTerisi = computed(() => {
  return myKosList.value.reduce((acc, k) => {
    const terisi = (k.kamar || []).filter((r) => r.status === 'terisi').length
    return acc + terisi
  }, 0)
})

const occupancyRate = computed(() => {
  if (totalKamar.value === 0) return 0
  return Math.round((totalKamarTerisi.value / totalKamar.value) * 100)
})

const getAvailableKamarCount = (kos) => {
  if (!kos.kamar) return 0
  return kos.kamar.filter((r) => r.status === 'tersedia').length
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val)
}

const getImageForKos = (kos) => {
  if (kos.foto && kos.foto.length > 0) {
    return resolveImageUrl(kos.foto[0])
  }
  if (kos.foto_utama) return kos.foto_utama
  const placeholders = [
    'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=600&q=80'
  ]
  return placeholders[(kos.id || 0) % placeholders.length]
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedJenis.value = ''
  fetchKos()
}

const openAddModal = () => {
  isEditing.value = false
  editKosId.value = null
  formData.value = {
    nama: '',
    jenis: 'putri',
    alamat: '',
    deskripsi: '',
    harga: 750000,
    latitude: null,
    longitude: null,
    peraturan: [
      'Dilarang merokok di dalam kamar',
      'Batas jam malam pukul 23.00 WIB',
      'Wajib menjaga kebersihan bersama'
    ],
    foto: [],
    fasilitas_ids: []
  }
  showModal.value = true
  nextTick(() => {
    setTimeout(() => locationPickerRef.value?.invalidateSize(), 300)
  })
}

const openEditModal = (kos) => {
  isEditing.value = true
  editKosId.value = kos.id
  formData.value = {
    nama: kos.nama,
    jenis: kos.jenis,
    alamat: kos.alamat,
    deskripsi: kos.deskripsi || '',
    harga: kos.harga,
    latitude: kos.latitude || null,
    longitude: kos.longitude || null,
    peraturan: Array.isArray(kos.peraturan) ? [...kos.peraturan] : [],
    foto: Array.isArray(kos.foto) ? [...kos.foto] : [],
    fasilitas_ids: (kos.fasilitas || []).map((f) => f.id)
  }
  showModal.value = true
  nextTick(() => {
    setTimeout(() => locationPickerRef.value?.invalidateSize(), 300)
  })
}

const closeModal = () => {
  showModal.value = false
}

const saveKos = async () => {
  saving.value = true
  errorMsg.value = ''
  successMsg.value = ''

  try {
    if (isEditing.value) {
      await api.put(`/kos/${editKosId.value}`, formData.value)
      successMsg.value = 'Data kos berhasil diperbarui!'
    } else {
      await api.post('/kos', formData.value)
      successMsg.value = 'Properti kos baru berhasil ditambahkan!'
    }
    showModal.value = false
    await fetchKos()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan data kos. Pastikan isian valid.'
  } finally {
    saving.value = false
  }
}

const confirmDeleteKos = async (kos) => {
  if (confirm(`Apakah Anda yakin ingin menghapus properti "${kos.nama}"? Seluruh kamar dan data terkait akan ikut terhapus.`)) {
    try {
      await api.delete(`/kos/${kos.id}`)
      successMsg.value = `Properti "${kos.nama}" berhasil dihapus.`
      await fetchKos()
    } catch (err) {
      errorMsg.value = err.response?.data?.message || 'Gagal menghapus properti kos.'
    }
  }
}
</script>
