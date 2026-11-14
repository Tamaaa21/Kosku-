/**
 * Kostara API Endpoints Catalog
 * Comprehensive list of all Kostara v1 REST API endpoints.
 */

export const apiCategories = [
  'Semua',
  'Autentikasi & Akun',
  'Katalog Kos',
  'Kamar Kos',
  'Pemesanan / Booking',
  'Ulasan & Rating',
  'Fasilitas Properti',
  'Verifikasi Pemilik',
  'Hero Banner Slider',
  'Panel Admin & Moderasi',
  'Media & Berkas',
]

export const apiEndpoints = [
  // =========================================================================
  // 1. AUTENTIKASI & AKUN
  // =========================================================================
  {
    id: 'auth-register',
    category: 'Autentikasi & Akun',
    name: 'Pendaftaran Akun Baru',
    method: 'POST',
    path: '/auth/register',
    auth: 'public',
    description: 'Mendaftarkan akun baru untuk pencari kos atau pemilik kos.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama: 'Budi Santoso',
      email: 'budi.santoso@example.com',
      telepon: '081234567899',
      role: 'pencari',
      password: 'password123',
      password_confirmation: 'password123'
    }, null, 2),
    docs: {
      notes: 'Role yang diizinkan: "pencari" atau "pemilik". Password minimal 8 karakter.',
      responseExample: '{\n  "message": "Registrasi berhasil.",\n  "token": "1|sanctum_token...",\n  "user": { "id": 4, "nama": "Budi Santoso", "role": "pencari" }\n}'
    }
  },
  {
    id: 'auth-login',
    category: 'Autentikasi & Akun',
    name: 'Login Pengguna',
    method: 'POST',
    path: '/auth/login',
    auth: 'public',
    description: 'Autentikasi dengan email & password untuk memperoleh Bearer token Sanctum.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      email: 'admin@kos.test',
      password: 'password'
    }, null, 2),
    docs: {
      notes: 'Akun bawaan seeder: admin@kos.test, pemilik@kos.test, pencari@kos.test (password: password)',
      responseExample: '{\n  "message": "Login berhasil.",\n  "token": "2|sanctum_token...",\n  "user": { "id": 1, "nama": "Administrator", "role": "admin" }\n}'
    }
  },
  {
    id: 'auth-forgot-password',
    category: 'Autentikasi & Akun',
    name: 'Lupa Password (Kirim Link Reset)',
    method: 'POST',
    path: '/auth/forgot-password',
    auth: 'public',
    description: 'Mengirimkan tautan reset password ke email terdaftar pengguna.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      email: 'pencari@kos.test'
    }, null, 2),
    docs: {
      notes: 'Email harus terdaftar di basis data Kostara.',
      responseExample: '{\n  "message": "Tautan reset kata sandi telah dikirim ke email Anda."\n}'
    }
  },
  {
    id: 'auth-reset-password',
    category: 'Autentikasi & Akun',
    name: 'Reset Password',
    method: 'POST',
    path: '/auth/reset-password',
    auth: 'public',
    description: 'Mereset kata sandi menggunakan token yang diterima via email.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      token: 'contoh_token_reset',
      email: 'pencari@kos.test',
      password: 'passwordBaru123',
      password_confirmation: 'passwordBaru123'
    }, null, 2),
    docs: {
      notes: 'Token reset didapatkan dari tautan email reset kata sandi.',
      responseExample: '{\n  "message": "Kata sandi Anda telah berhasil direset."\n}'
    }
  },
  {
    id: 'auth-me',
    category: 'Autentikasi & Akun',
    name: 'Profil Saya (Me)',
    method: 'GET',
    path: '/auth/me',
    auth: 'auth',
    description: 'Mengambil data profil pengguna yang sedang login berdasarkan token Sanctum.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Memerlukan header Authorization: Bearer <token>.',
      responseExample: '{\n  "data": {\n    "id": 1,\n    "nama": "Administrator",\n    "email": "admin@kos.test",\n    "role": "admin",\n    "telepon": "081234567890",\n    "foto": null\n  }\n}'
    }
  },
  {
    id: 'auth-update-profile',
    category: 'Autentikasi & Akun',
    name: 'Perbarui Data Profil',
    method: 'PUT',
    path: '/me',
    auth: 'auth',
    description: 'Memperbarui nama dan nomor telepon akun saat ini.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama: 'Budi Santoso Diperbarui',
      telepon: '081299887766'
    }, null, 2),
    docs: {
      notes: 'Nomor telepon harus valid (10-15 digit).',
      responseExample: '{\n  "message": "Profil berhasil diperbarui.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'auth-update-avatar',
    category: 'Autentikasi & Akun',
    name: 'Unggah Foto Profil (Avatar)',
    method: 'POST',
    path: '/me/avatar',
    auth: 'auth',
    description: 'Mengunggah gambar foto profil pengguna saat ini (multipart/form-data).',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'form-data',
    defaultBody: '',
    docs: {
      notes: 'Field berkas form: "avatar" (file gambar jpg, png, webp, max 2MB).',
      responseExample: '{\n  "message": "Foto profil berhasil diperbarui.",\n  "foto_url": "http://.../storage/avatars/..."\n}'
    }
  },
  {
    id: 'auth-update-password',
    category: 'Autentikasi & Akun',
    name: 'Ganti Kata Sandi',
    method: 'PUT',
    path: '/me/password',
    auth: 'auth',
    description: 'Mengubah kata sandi akun pengguna saat ini.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      current_password: 'password',
      password: 'passwordBaru123',
      password_confirmation: 'passwordBaru123'
    }, null, 2),
    docs: {
      notes: 'current_password harus sesuai dengan password saat ini.',
      responseExample: '{\n  "message": "Kata sandi berhasil diperbarui."\n}'
    }
  },
  {
    id: 'auth-logout',
    category: 'Autentikasi & Akun',
    name: 'Logout Pengguna',
    method: 'POST',
    path: '/auth/logout',
    auth: 'auth',
    description: 'Mencabut dan menghapus Bearer token aktif pengguna saat ini.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Token yang dicabut tidak dapat digunakan kembali.',
      responseExample: '{\n  "message": "Berhasil keluar."\n}'
    }
  },

  // =========================================================================
  // 2. KATALOG KOS / PROPERTI
  // =========================================================================
  {
    id: 'kos-index',
    category: 'Katalog Kos',
    name: 'Daftar Kos (Pencarian & Filter)',
    method: 'GET',
    path: '/kos',
    auth: 'public',
    description: 'Mengambil daftar kos dengan parameter filter kata kunci, kota, harga, tipe, radius lokasi, dan urutan.',
    pathParams: [],
    queryParams: [
      { key: 'search', value: '', enabled: false, description: 'Kata kunci nama/alamat kos' },
      { key: 'kota', value: 'Yogyakarta', enabled: false, description: 'Filter kota kos' },
      { key: 'tipe', value: 'campur', enabled: false, description: 'Tipe penghuni: putra, putri, campur' },
      { key: 'min_price', value: '500000', enabled: false, description: 'Harga sewa minimum' },
      { key: 'max_price', value: '2500000', enabled: false, description: 'Harga sewa maksimum' },
      { key: 'latitude', value: '-7.7956', enabled: false, description: 'Titik koordinat Latitude' },
      { key: 'longitude', value: '110.3695', enabled: false, description: 'Titik koordinat Longitude' },
      { key: 'radius', value: '10', enabled: false, description: 'Radius pencarian dalam km' },
      { key: 'sort', value: 'terbaru', enabled: true, description: 'Urutan: terbaru, harga_termurah, harga_termahal, rating, jarak' },
      { key: 'per_page', value: '10', enabled: true, description: 'Jumlah item per halaman' },
      { key: 'page', value: '1', enabled: true, description: 'Nomor halaman' },
    ],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Jika latitude & longitude dikirim, sistem menghitung jarak riil (jarak_km) menggunakan formula Haversine.',
      responseExample: '{\n  "data": [ ... ],\n  "links": { ... },\n  "meta": { "total": 12, "per_page": 10 }\n}'
    }
  },
  {
    id: 'kos-show',
    category: 'Katalog Kos',
    name: 'Detail Kos',
    method: 'GET',
    path: '/kos/{id}',
    auth: 'public',
    description: 'Mengambil informasi lengkap kos meliputi kamar, fasilitas, pemilik, koordinat, dan statistik ulasan.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Ganti {id} dengan ID kos yang terdaftar.',
      responseExample: '{\n  "data": {\n    "id": 1,\n    "nama": "Kos Putri Melati",\n    "harga": 850000,\n    "alamat": "Jl. Kaliurang KM 5",\n    "fasilitas": [ ... ],\n    "kamar": [ ... ]\n  }\n}'
    }
  },
  {
    id: 'kos-store',
    category: 'Katalog Kos',
    name: 'Tambah Kos Baru',
    method: 'POST',
    path: '/kos',
    auth: 'verified_owner',
    description: 'Membuat data properti kos baru (khusus Mitra Pemilik yang telah terverifikasi).',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama: 'Kos Harmoni Residence',
      alamat: 'Jl. Gejayan No. 45, Caturtunggal, Sleman',
      deskripsi: 'Kos eksklusif nyaman dan tenang dekat berbagai kampus ternama.',
      jenis: 'campur',
      harga: 1200000,
      harga_harian: 100000,
      harga_mingguan: 450000,
      durasi_sewa: ['bulanan', 'harian'],
      latitude: -7.7712,
      longitude: 110.3871,
      fasilitas_ids: [1, 2],
      peraturan: [
        'Dilarang merokok di dalam kamar',
        'Tamu menginap wajib lapor pengelola'
      ],
      foto: [
        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800'
      ]
    }, null, 2),
    docs: {
      notes: 'Hanya dapat dipanggil oleh role "pemilik" dengan status_verifikasi = "verified".',
      responseExample: '{\n  "message": "Kos berhasil ditambahkan.",\n  "data": { "id": 5, "nama": "Kos Harmoni Residence" }\n}'
    }
  },
  {
    id: 'kos-update',
    category: 'Katalog Kos',
    name: 'Perbarui Kos',
    method: 'PUT',
    path: '/kos/{id}',
    auth: 'verified_owner',
    description: 'Mengubah informasi data properti kos (khusus pemilik properti atau Admin).',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama: 'Kos Putri Melati Indah (Renovasi)',
      deskripsi: 'Deskripsi kos telah diperbarui dengan fasilitas lengkap.',
      jenis: 'putri',
      harga: 950000
    }, null, 2),
    docs: {
      notes: 'Mendukung pembaruan parsial kolom kos yang ingin diubah.',
      responseExample: '{\n  "message": "Data kos berhasil diperbarui.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'kos-destroy',
    category: 'Katalog Kos',
    name: 'Hapus Kos',
    method: 'DELETE',
    path: '/kos/{id}',
    auth: 'verified_owner',
    description: 'Menghapus kos beserta seluruh unit kamar yang terkait.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Tindakan permanen. Seluruh relasi kamar dan booking terkait akan terdampak.',
      responseExample: '{\n  "message": "Kos berhasil dihapus."\n}'
    }
  },

  // =========================================================================
  // 3. KAMAR KOS (ROOMS)
  // =========================================================================
  {
    id: 'rooms-index',
    category: 'Kamar Kos',
    name: 'Daftar Kamar per Kos',
    method: 'GET',
    path: '/kos/{id}/rooms',
    auth: 'public',
    description: 'Mengambil seluruh tipe dan unit kamar yang tersedia pada suatu kos.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Mengembalikan daftar kamar beserta status ketersediaan (tersedia / terisi).',
      responseExample: '{\n  "data": [\n    {\n      "id": 1,\n      "nama_kamar": "Kamar A1",\n      "harga": 850000,\n      "status": "tersedia"\n    }\n  ]\n}'
    }
  },
  {
    id: 'rooms-store',
    category: 'Kamar Kos',
    name: 'Tambah Kamar Baru',
    method: 'POST',
    path: '/kos/{id}/rooms',
    auth: 'verified_owner',
    description: 'Menambahkan unit atau tipe kamar baru ke dalam kos tertentu.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama_kamar: 'Kamar Deluxe 101',
      harga: 1250000,
      harga_harian: 120000,
      harga_mingguan: 500000,
      durasi_sewa: ['bulanan', 'harian'],
      status: 'tersedia',
      ukuran: '3x4 m',
      fasilitas_kamar: [
        'Kamar Mandi Dalam',
        'AC',
        'Kasur Springbed',
        'Meja Belajar'
      ],
      foto: [
        'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=800'
      ]
    }, null, 2),
    docs: {
      notes: 'Status default adalah "tersedia". Harga bulanan wajib diisi.',
      responseExample: '{\n  "message": "Kamar berhasil ditambahkan.",\n  "data": { "id": 10, "nama_kamar": "Kamar Deluxe 101" }\n}'
    }
  },
  {
    id: 'rooms-update',
    category: 'Kamar Kos',
    name: 'Perbarui Data Kamar',
    method: 'PUT',
    path: '/rooms/{id}',
    auth: 'verified_owner',
    description: 'Mengubah harga, status, ukuran, fasilitas, atau foto kamar tertentu.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kamar' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama_kamar: 'Kamar A1 Superior',
      harga: 900000,
      status: 'tersedia',
      ukuran: '3.5x4 m'
    }, null, 2),
    docs: {
      notes: 'Path route menggunakan /rooms/{id}.',
      responseExample: '{\n  "message": "Kamar berhasil diperbarui.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'rooms-destroy',
    category: 'Kamar Kos',
    name: 'Hapus Kamar',
    method: 'DELETE',
    path: '/rooms/{id}',
    auth: 'verified_owner',
    description: 'Menghapus data kamar dari kos.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kamar' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Kamar yang memiliki pemesanan aktif tidak boleh dihapus sembarangan.',
      responseExample: '{\n  "message": "Kamar berhasil dihapus."\n}'
    }
  },

  // =========================================================================
  // 4. PEMESANAN / BOOKING
  // =========================================================================
  {
    id: 'bookings-store',
    category: 'Pemesanan / Booking',
    name: 'Pengajuan Pemesanan Kamar',
    method: 'POST',
    path: '/bookings',
    auth: 'pencari',
    description: 'Mengajukan sewa kamar kos dengan proteksi pessimistic locking (anti double-booking).',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      kamar_id: 1,
      tanggal_pemesanan: new Date().toISOString().split('T')[0],
      durasi_tipe: 'bulanan',
      durasi_jumlah: 1
    }, null, 2),
    docs: {
      notes: 'Khusus role "pencari". tanggal_pemesanan harus hari ini atau di masa depan.',
      responseExample: '{\n  "message": "Pemesanan berhasil diajukan.",\n  "data": {\n    "id": 8,\n    "status": "menunggu",\n    "total_biaya": 850000\n  }\n}'
    }
  },
  {
    id: 'bookings-index',
    category: 'Pemesanan / Booking',
    name: 'Daftar Riwayat Pemesanan',
    method: 'GET',
    path: '/bookings',
    auth: 'auth',
    description: 'Mengambil daftar riwayat pesanan (pencari melihat miliknya, pemilik melihat pesanan kosnya, admin melihat semua).',
    pathParams: [],
    queryParams: [
      { key: 'status', value: '', enabled: false, description: 'Filter: menunggu, disetujui, ditolak, dibatalkan, selesai' },
      { key: 'page', value: '1', enabled: true, description: 'Nomor halaman' },
      { key: 'per_page', value: '10', enabled: true, description: 'Jumlah item per halaman' },
    ],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Secara otomatis disaring berdasarkan hak akses pengguna saat ini.',
      responseExample: '{\n  "data": [ ... ],\n  "meta": { "total": 5 }\n}'
    }
  },
  {
    id: 'bookings-show',
    category: 'Pemesanan / Booking',
    name: 'Detail Pemesanan',
    method: 'GET',
    path: '/bookings/{id}',
    auth: 'auth',
    description: 'Melihat rincian pemesanan spesifik termasuk identitas penyewa, pemilik, dan unit kamar.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Pemesanan' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Hanya dapat diakses oleh penyewa bersangkutan, pemilik properti, atau admin.',
      responseExample: '{\n  "data": { "id": 1, "status": "disetujui", "total_biaya": 850000 }\n}'
    }
  },
  {
    id: 'bookings-status',
    category: 'Pemesanan / Booking',
    name: 'Ubah Status Pemesanan',
    method: 'PATCH',
    path: '/bookings/{id}/status',
    auth: 'verified_owner',
    description: 'Menyetujui atau menolak permohonan pemesanan kamar kos.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Pemesanan' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      status: 'disetujui'
    }, null, 2),
    docs: {
      notes: 'Pilihan status: "disetujui", "ditolak", "dibatalkan".',
      responseExample: '{\n  "message": "Status pemesanan berhasil diperbarui.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'bookings-export',
    category: 'Pemesanan / Booking',
    name: 'Ekspor Data Pemesanan (CSV Stream)',
    method: 'GET',
    path: '/bookings/export',
    auth: 'verified_owner',
    description: 'Mengunduh rekapitulasi data pemesanan dalam format file CSV streaming dengan UTF-8 BOM.',
    pathParams: [],
    queryParams: [
      { key: 'status', value: '', enabled: false, description: 'Filter status pesanan tertentu' },
    ],
    headers: [
      { key: 'Accept', value: 'text/csv', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus Pemilik Kos dan Admin. Hasilnya berupa file stream .csv yang langsung kompatibel dengan MS Excel.',
      responseExample: 'CSV Data File Stream (Content-Type: text/csv)'
    }
  },

  // =========================================================================
  // 5. ULASAN & RATING (REVIEWS)
  // =========================================================================
  {
    id: 'reviews-index',
    category: 'Ulasan & Rating',
    name: 'Daftar Ulasan per Kos',
    method: 'GET',
    path: '/kos/{id}/reviews',
    auth: 'public',
    description: 'Melihat seluruh testimoni, rating bintang, dan ulasan penyewa untuk kos tertentu.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [
      { key: 'per_page', value: '10', enabled: true, description: 'Jumlah item per halaman' },
      { key: 'page', value: '1', enabled: true, description: 'Nomor halaman' },
    ],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Menyertakan informasi profil pengguna yang memberikan ulasan.',
      responseExample: '{\n  "data": [\n    {\n      "id": 1,\n      "rating": 5,\n      "komentar": "Kamar bersih dan ibu kos ramah sekali!",\n      "pengguna": { "nama": "Rizky" }\n    }\n  ]\n}'
    }
  },
  {
    id: 'reviews-eligibility',
    category: 'Ulasan & Rating',
    name: 'Cek Kelayakan Mengulas Kos',
    method: 'GET',
    path: '/kos/{id}/reviews/eligibility',
    auth: 'pencari',
    description: 'Mengecek apakah penyewa saat ini memenuhi syarat untuk mengulas kos (harus pernah memesan dan disetujui).',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Mengembalikan eligible: true/false serta existing_review jika sudah pernah mengulas.',
      responseExample: '{\n  "eligible": true,\n  "has_reviewed": false,\n  "existing_review": null\n}'
    }
  },
  {
    id: 'reviews-store',
    category: 'Ulasan & Rating',
    name: 'Kirim Ulasan & Rating',
    method: 'POST',
    path: '/kos/{id}/reviews',
    auth: 'pencari',
    description: 'Mengirim ulasan rating (1-5) dan testimoni pengalaman tinggal di kos.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      rating: 5,
      komentar: 'Lingkungan sangat nyaman, dekat warung makan dan WiFi super kencang. Sangat direkomendasikan!'
    }, null, 2),
    docs: {
      notes: 'Rating bernilai 1 sampai 5. Komentar minimal 5 karakter.',
      responseExample: '{\n  "message": "Ulasan berhasil dikirimkan.",\n  "data": { ... }\n}'
    }
  },

  // =========================================================================
  // 6. FASILITAS PROPERTI
  // =========================================================================
  {
    id: 'facilities-index',
    category: 'Fasilitas Properti',
    name: 'Daftar Semua Fasilitas',
    method: 'GET',
    path: '/facilities',
    auth: 'public',
    description: 'Mengambil master data fasilitas umum yang dapat dipilih oleh pemilik kos.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Menyertakan kos_count (jumlah kos yang menggunakan fasilitas ini).',
      responseExample: '{\n  "data": [\n    { "id": 1, "nama": "WiFi Cepat", "deskripsi": "Akses internet 100Mbps" },\n    { "id": 2, "nama": "Parkir Mobil", "deskripsi": "Area parkir luas" }\n  ]\n}'
    }
  },
  {
    id: 'facilities-store',
    category: 'Fasilitas Properti',
    name: 'Tambah Fasilitas Baru',
    method: 'POST',
    path: '/facilities',
    auth: 'pemilik',
    description: 'Menambahkan fasilitas baru ke dalam katalog (bisa dilakukan pemilik/admin).',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama: 'Dapur Bersama Modern',
      deskripsi: 'Dilengkapi kulkas, microwave, dan kompor gas.'
    }, null, 2),
    docs: {
      notes: 'Nama fasilitas harus unik dan maksimal 100 karakter.',
      responseExample: '{\n  "message": "Fasilitas berhasil ditambahkan.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'facilities-update',
    category: 'Fasilitas Properti',
    name: 'Perbarui Fasilitas (Admin)',
    method: 'PUT',
    path: '/facilities/{id}',
    auth: 'admin',
    description: 'Mengubah nama atau deskripsi fasilitas terdaftar (khusus Administrator).',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Fasilitas' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nama: 'WiFi Berkecepatan Tinggi',
      deskripsi: 'Akses internet fiber optic hingga 150 Mbps.'
    }, null, 2),
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "message": "Fasilitas berhasil diperbarui.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'facilities-destroy',
    category: 'Fasilitas Properti',
    name: 'Hapus Fasilitas (Admin)',
    method: 'DELETE',
    path: '/facilities/{id}',
    auth: 'admin',
    description: 'Menghapus fasilitas dari basis data (khusus Administrator).',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Fasilitas' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "message": "Fasilitas berhasil dihapus."\n}'
    }
  },

  // =========================================================================
  // 7. VERIFIKASI KEPEMILIKAN PEMILIK
  // =========================================================================
  {
    id: 'verification-show',
    category: 'Verifikasi Pemilik',
    name: 'Status Verifikasi Pemilik',
    method: 'GET',
    path: '/pemilik/verification',
    auth: 'pemilik',
    description: 'Mengecek status pengajuan verifikasi KTP dan bukti kepemilikan kos.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus role "pemilik". Mengembalikan status: unverified, pending, verified, rejected.',
      responseExample: '{\n  "data": {\n    "status_verifikasi": "verified",\n    "is_verified": true,\n    "nik": "3471012345670001"\n  }\n}'
    }
  },
  {
    id: 'verification-submit',
    category: 'Verifikasi Pemilik',
    name: 'Kirim Dokumen Verifikasi',
    method: 'POST',
    path: '/pemilik/verification',
    auth: 'pemilik',
    description: 'Mengajukan dokumen identitas NIK, foto KTP, dan bukti kepemilikan untuk dimoderasi admin.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      nik: '3201123456780005',
      foto_ktp: 'https://images.unsplash.com/photo-1544717305-2782549b5136?w=800',
      bukti_properti: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=800',
      foto_properti: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800',
      catatan_verifikasi: 'Pengajuan sertifikat kepemilikan kos Melati Gejayan.'
    }, null, 2),
    docs: {
      notes: 'NIK harus berupa angka 16-20 digit.',
      responseExample: '{\n  "message": "Dokumen verifikasi kepemilikan berhasil diajukan dan sedang menunggu peninjauan tim verifikator.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'verification-upload',
    category: 'Verifikasi Pemilik',
    name: 'Unggah Berkas Dokumen Tunggal',
    method: 'POST',
    path: '/pemilik/verification/upload',
    auth: 'pemilik',
    description: 'Mengunggah file KTP / Bukti Properti (multipart/form-data) untuk mendapatkan URL publik.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'form-data',
    defaultBody: '',
    docs: {
      notes: 'Field berkas form: "file" (image/pdf, max 3MB).',
      responseExample: '{\n  "message": "Berkas berhasil diunggah.",\n  "url": "http://.../storage/verification/..."\n}'
    }
  },

  // =========================================================================
  // 8. HERO BANNER SLIDER
  // =========================================================================
  {
    id: 'banners-public',
    category: 'Hero Banner Slider',
    name: 'Banner Publik (Beranda)',
    method: 'GET',
    path: '/hero-banners',
    auth: 'public',
    description: 'Mengambil seluruh slide banner aktif untuk tampilan beranda aplikasi.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Slide diurutkan berdasarkan kolom urutan ascending.',
      responseExample: '{\n  "data": [\n    {\n      "id": 1,\n      "judul": "Promo Sewa Kost Hemat",\n      "subjudul": "Diskon hingga 20%",\n      "gambar_url": "..."\n    }\n  ]\n}'
    }
  },
  {
    id: 'banners-admin-index',
    category: 'Hero Banner Slider',
    name: 'Daftar Semua Banner (Admin)',
    method: 'GET',
    path: '/admin/hero-banners',
    auth: 'admin',
    description: 'Mengambil seluruh hero banner termasuk yang non-aktif untuk pengelolaan admin.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "data": [ ... ]\n}'
    }
  },
  {
    id: 'banners-admin-store',
    category: 'Hero Banner Slider',
    name: 'Tambah Banner Baru (Admin)',
    method: 'POST',
    path: '/admin/hero-banners',
    auth: 'admin',
    description: 'Membuat slide banner baru untuk beranda Kostara.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      judul: 'Eksplorasi Kos Dekat Kampus Idaman',
      subjudul: 'Ratusan pilihan kos terverifikasi dengan jaminan fasilitas lengkap.',
      badge_text: 'Spesial Mahasiswa Baru',
      gambar_url: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200',
      urutan: 1,
      is_active: true
    }, null, 2),
    docs: {
      notes: 'gambar_url wajib diisi. urutan berupa angka integer.',
      responseExample: '{\n  "message": "Banner slider berhasil ditambahkan.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'banners-admin-show',
    category: 'Hero Banner Slider',
    name: 'Detail Banner (Admin)',
    method: 'GET',
    path: '/admin/hero-banners/{id}',
    auth: 'admin',
    description: 'Melihat detail data satu banner slider.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Banner' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "data": { "id": 1, "judul": "..." }\n}'
    }
  },
  {
    id: 'banners-admin-update',
    category: 'Hero Banner Slider',
    name: 'Perbarui Banner (Admin)',
    method: 'PUT',
    path: '/admin/hero-banners/{id}',
    auth: 'admin',
    description: 'Mengubah judul, teks, gambar, atau urutan banner slider.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Banner' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      judul: 'Hunian Eksklusif Terbaik di Indonesia',
      subjudul: 'Kenyamanan maksimal serasa di rumah sendiri.',
      gambar_url: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200',
      urutan: 2,
      is_active: true
    }, null, 2),
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "message": "Banner slider berhasil diperbarui."\n}'
    }
  },
  {
    id: 'banners-admin-status',
    category: 'Hero Banner Slider',
    name: 'Ubah Status Aktif Banner (Admin)',
    method: 'PATCH',
    path: '/admin/hero-banners/{id}/status',
    auth: 'admin',
    description: 'Mengaktifkan atau menonaktifkan tampilan banner pada beranda.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Banner' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      is_active: false
    }, null, 2),
    docs: {
      notes: 'is_active bernilai true atau false.',
      responseExample: '{\n  "message": "Status banner slider berhasil diubah."\n}'
    }
  },
  {
    id: 'banners-admin-destroy',
    category: 'Hero Banner Slider',
    name: 'Hapus Banner (Admin)',
    method: 'DELETE',
    path: '/admin/hero-banners/{id}',
    auth: 'admin',
    description: 'Menghapus banner slider dari sistem.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Banner' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "message": "Banner slider berhasil dihapus."\n}'
    }
  },

  // =========================================================================
  // 9. PANEL ADMIN & MODERASI
  // =========================================================================
  {
    id: 'admin-stats',
    category: 'Panel Admin & Moderasi',
    name: 'Statistik Ringkasan Sistem (Admin)',
    method: 'GET',
    path: '/admin/stats',
    auth: 'admin',
    description: 'Mengambil metrik ringkasan platform (total kos, user, pemesanan, verifikasi pending).',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Menyajikan data agregat untuk dashboard analitik.',
      responseExample: '{\n  "total_pengguna": 45,\n  "total_kos": 18,\n  "total_pemesanan": 32,\n  "verifikasi_pending": 3\n}'
    }
  },
  {
    id: 'admin-users-index',
    category: 'Panel Admin & Moderasi',
    name: 'Daftar Seluruh Pengguna (Admin)',
    method: 'GET',
    path: '/admin/users',
    auth: 'admin',
    description: 'Melihat seluruh akun pengguna dengan filter peran (role), status aktif, dan kata kunci.',
    pathParams: [],
    queryParams: [
      { key: 'role', value: '', enabled: false, description: 'Filter role: admin, pemilik, pencari' },
      { key: 'aktif', value: '1', enabled: false, description: 'Filter status aktif: 1 atau 0' },
      { key: 'q', value: '', enabled: false, description: 'Pencarian nama, email, atau no telepon' },
      { key: 'sort', value: 'unit_terbanyak', enabled: false, description: 'Urutan: unit_terbanyak atau terbaru' },
      { key: 'page', value: '1', enabled: true, description: 'Nomor halaman' },
      { key: 'per_page', value: '10', enabled: true, description: 'Jumlah item per halaman' },
    ],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Menyertakan jumlah kos dan pemesanan per pengguna.',
      responseExample: '{\n  "data": [ ... ],\n  "meta": { "total": 15 }\n}'
    }
  },
  {
    id: 'admin-users-show',
    category: 'Panel Admin & Moderasi',
    name: 'Detail Pengguna (Admin)',
    method: 'GET',
    path: '/admin/users/{id}',
    auth: 'admin',
    description: 'Melihat profil lengkap salah satu pengguna sistem.',
    pathParams: [
      { key: 'id', value: '2', description: 'ID Pengguna' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "data": { "id": 2, "nama": "Pemilik Kos", "role": "pemilik" }\n}'
    }
  },
  {
    id: 'admin-users-status',
    category: 'Panel Admin & Moderasi',
    name: 'Ubah Status Aktif Akun Pengguna',
    method: 'PATCH',
    path: '/admin/users/{id}/status',
    auth: 'admin',
    description: 'Mengaktifkan atau memblokir sementara akun pengguna.',
    pathParams: [
      { key: 'id', value: '2', description: 'ID Pengguna' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      aktif: true
    }, null, 2),
    docs: {
      notes: 'Pengguna non-aktif tidak dapat masuk ke sistem.',
      responseExample: '{\n  "message": "Status pengguna berhasil diperbarui.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'admin-verifications-index',
    category: 'Panel Admin & Moderasi',
    name: 'Daftar Pengajuan Verifikasi (Admin)',
    method: 'GET',
    path: '/admin/verifications',
    auth: 'admin',
    description: 'Melihat antrean verifikasi KTP dan kepemilikan kos mitra pemilik.',
    pathParams: [],
    queryParams: [
      { key: 'status', value: '', enabled: false, description: 'Filter: unverified, pending, verified, rejected' },
      { key: 'q', value: '', enabled: false, description: 'Pencarian nama atau NIK pemilik' },
      { key: 'page', value: '1', enabled: true, description: 'Nomor halaman' },
    ],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "data": [ ... ],\n  "meta": { "total": 3 }\n}'
    }
  },
  {
    id: 'admin-verifications-review',
    category: 'Panel Admin & Moderasi',
    name: 'Putusan Verifikasi Pemilik',
    method: 'PATCH',
    path: '/admin/verifications/{id}/status',
    auth: 'admin',
    description: 'Menyetujui (verified) atau menolak (rejected) pengajuan kepemilikan mitra.',
    pathParams: [
      { key: 'id', value: '2', description: 'ID Pengguna (Role Pemilik)' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
      { key: 'Content-Type', value: 'application/json', enabled: true },
    ],
    bodyType: 'json',
    defaultBody: JSON.stringify({
      status: 'verified',
      alasan_penolakan: null
    }, null, 2),
    docs: {
      notes: 'Jika ditolak, status = "rejected" dan alasan_penolakan wajib diisi.',
      responseExample: '{\n  "message": "Verifikasi kepemilikan berhasil disetujui. Fitur kelola kos telah aktif.",\n  "data": { ... }\n}'
    }
  },
  {
    id: 'admin-kos-index',
    category: 'Panel Admin & Moderasi',
    name: 'Daftar Moderasi Kos (Admin)',
    method: 'GET',
    path: '/admin/kos',
    auth: 'admin',
    description: 'Melihat seluruh listing kos di platform untuk kebutuhan moderasi konten.',
    pathParams: [],
    queryParams: [
      { key: 'q', value: '', enabled: false, description: 'Kata kunci pencarian kos atau nama pemilik' },
      { key: 'page', value: '1', enabled: true, description: 'Nomor halaman' },
      { key: 'per_page', value: '10', enabled: true, description: 'Jumlah item per halaman' },
    ],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "data": [ ... ]\n}'
    }
  },
  {
    id: 'admin-kos-destroy',
    category: 'Panel Admin & Moderasi',
    name: 'Moderasi Hapus Kos (Admin)',
    method: 'DELETE',
    path: '/admin/kos/{id}',
    auth: 'admin',
    description: 'Menghapus kos yang melanggar aturan kebijakan Kostara.',
    pathParams: [
      { key: 'id', value: '1', description: 'ID Kos' },
    ],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'none',
    defaultBody: '',
    docs: {
      notes: 'Khusus hak akses Administrator.',
      responseExample: '{\n  "message": "Kos berhasil dihapus oleh administrator."\n}'
    }
  },

  // =========================================================================
  // 10. MEDIA & BERKAS
  // =========================================================================
  {
    id: 'uploads-images',
    category: 'Media & Berkas',
    name: 'Unggah Multi Gambar (Images)',
    method: 'POST',
    path: '/uploads/images',
    auth: 'pemilik',
    description: 'Mengunggah hingga 5 gambar sekaligus (JPG, PNG, WEBP, max 2MB per gambar) untuk foto kos atau kamar.',
    pathParams: [],
    queryParams: [],
    headers: [
      { key: 'Accept', value: 'application/json', enabled: true },
    ],
    bodyType: 'form-data',
    defaultBody: '',
    docs: {
      notes: 'Field form: "images[]" (array file). Mengembalikan array URL gambar yang berhasil disimpan.',
      responseExample: '{\n  "message": "Gambar berhasil diunggah.",\n  "urls": [\n    "http://.../storage/uploads/img1.webp",\n    "http://.../storage/uploads/img2.webp"\n  ]\n}'
    }
  },
]
