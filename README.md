# KosKu &mdash; Platform Pencarian & Sewa Kos Indonesia

<p align="center">
  <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtAMGcNImL28Wkcksd1pB136MBTXtxAeUZZdLwny2E0TfcGGRkdrMA_JR6pVnktaHUu8Ffua13FdN3HdgZd29vhkeFDWM4WJtQJFMuZndi8o1HB2GXyn8ikhIgK45eAlZmqqiEVYkjMe7_m-Cvw0nxPY6cKLGKCrWp2Ch2dtBIlIqQb5pKjNVS1WaRhbnQPFyDftN7_uvVGXkv3Z_JUnsJpPxReDktVkZYw0eJ1YRW7caBLYvFdhslDw" alt="KosKu Banner" width="100%" style="border-radius: 12px; max-height: 380px; object-fit: cover;" />
</p>

<p align="center">
  <strong>Platform Terintegrasi untuk Pencari Kos dan Mitra Pemilik Kos di Seluruh Indonesia</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker Ready" />
  <img src="https://img.shields.io/badge/Sanctum-Token_Auth-blue?style=for-the-badge" alt="Sanctum Auth" />
  <img src="https://img.shields.io/badge/Tests-58_Passed-success?style=for-the-badge" alt="Tests" />
</p>

---

## 📌 Tentang KosKu

**KosKu** adalah aplikasi web modern yang menjembatani pencari hunian sewa (mahasiswa, pekerja, keluarga) dengan mitra pemilik kos di seluruh wilayah Indonesia. Dibangun dengan arsitektur decoupled **Laravel 11 REST API** pada sisi backend dan **Vue 3 SPA (Single Page Application)** pada sisi frontend.

Sistem dirancang responsif, cepat, aman, dan mudah digunakan dengan integrasi komunikasi WhatsApp langsung serta panduan rute navigasi Google Maps.

---

## 🚀 Fitur Unggulan

### 1. 👥 Navigasi Berbasis Peran (*Role-Based Access Control*)
Antarmuka beradaptasi secara otomatis sesuai hak akses pengguna:
- **Pencari Kos & Publik**: Bilah navigasi atas (*top navbar*) dengan menu jelajah, katalog kos, peta interaktif, dan riwayat pesanan.
- **Mitra Pemilik Kos**: Bilah samping (*responsive sidebar drawer*) khusus pengelola yang memuat ringkasan performa properti, manajemen kamar, dan daftar pemesanan masuk.
- **Administrator**: Panel kontrol sistem untuk pengawasan seluruh transaksi dan data kos.

### 2. 🔍 Pencarian & Peta Lokasi Nasional
- **Jangkauan Seluruh Indonesia**: Eksplorasi kos di kota-kota besar (Jakarta, Yogyakarta, Bandung, Surabaya, Semarang, Bali, dll.).
- **Filter Fleksibel**: Pencarian berdasarkan nama, alamat, jenis kos (Putri, Putra, Campur), rentang harga sewa bulanan, dan fasilitas.
- **Peta Interaktif Leaflet**: Visualisasi sebaran kos berbasis OpenStreetMap dengan *pinpoint* akurat, *clustering*, dan *popup preview*.

### 3. 💬 Komunikasi WhatsApp Terintegrasi
- **Tanya Pemilik via WhatsApp**: Tombol instan pada halaman detail kos dengan template pesan otomatis (*nama kos, tipe kamar, dan tautan halaman*).
- **Hubungi Calon Penyewa**: Pemilik kos dapat langsung menyapa pemohon sewa melalui 1-klik tombol WhatsApp di daftar pesanan.
- **Floating Customer Service (CS)**: Widget bantuan mengambang di pojok kanan bawah untuk layanan cepat seputar kendala akun, transaksi, dan panduan sewa.

### 4. 🗺️ Navigasi GPS "Buka di Google Maps"
- Tombol **"Buka di Google Maps"** pada tab lokasi detail kos dan popup marker peta interaktif.
- Membuka aplikasi Google Maps bawaan pada ponsel dengan koordinat GPS presisi untuk panduan rute *turn-by-turn*.

### 5. 🔔 Sistem Notifikasi Real-time
- Dropdown notifikasi dinamis dengan indikator hitungan belum dibaca (*unread counter*).
- Pembaruan status pemesanan untuk pencari (Menunggu, Disetujui, Ditolak).
- Pemberitahuan reservasi masuk baru untuk mitra pemilik kos.

### 6. 🏠 Manajemen Properti & Kamar (Pemilik)
- Pengelolaan data kos lengkap: fasilitas, alamat, koordinat peta, dan deskripsi.
- Pengelolaan unit kamar: penamaan, penetapan harga, dan status ketersediaan (*tersedia / terisi*).
- Tindak lanjut pemesanan: konfirmasi persetujuan atau penolakan dengan sinkronisasi status otomatis.

---

## 🛠️ Arsitektur & Teknologi

| Bagian | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 11.x | PHP 8.3 dengan arsitektur RESTful API & Form Requests |
| **Authentication** | Laravel Sanctum | Otentikasi token berbasis Bearer Token yang aman |
| **Database** | MariaDB / MySQL / SQLite | Didukung Eloquent ORM, Migrations, dan Seeders |
| **Frontend Framework** | Vue 3 (Composition API) | SPA reaktif dengan `<script setup>` |
| **Build Tool** | Vite 5.x | Hot Module Replacement (HMR) & build produksi optimal |
| **State Management** | Pinia | Manajemen sesi login, peran, dan notifikasi |
| **Styling** | Tailwind CSS 3 | Desain sistem KosKu modern, konsisten, dan responsif |
| **Peta Digital** | Leaflet.js | Visualisasi peta interaktif & OpenStreetMap Nominatim |
| **Container & Deploy** | Docker & Compose | Multi-container: PHP 8.3-FPM, Nginx (SPA Reverse Proxy), MariaDB 11.4 |
| **Code Formatter & Test** | Laravel Pint & PHPUnit | 58 Automated Feature Tests & standar PSR-12 |

---

## 📁 Struktur Direktori Utama

```
backend-kos/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/V1/    # Controller REST API (Auth, Kos, Kamar, Pemesanan, Fasilitas)
│   │   ├── Requests/              # Validasi FormRequest (Auth, Kos, Kamar, Pemesanan)
│   │   └── Resources/             # Eloquent API Resources (Kos, Kamar, Pengguna, dll.)
│   ├── Models/                    # Model Eloquent (Pengguna, Kos, Kamar, Pemesanan, Fasilitas)
│   └── Services/                  # Business services (GeocodingService, dll.)
├── database/
│   ├── factories/                 # Factory data uji otomatis
│   ├── migrations/                # Skema tabel database
│   └── seeders/                   # Data awal (Administrator, Pemilik, Pencari, Kos Melati)
├── frontend/
│   ├── src/
│   │   ├── assets/                # CSS kustom & stylesheet Tailwind
│   │   ├── components/            # Komponen UI (NavbarPencari, SidebarPemilik, FloatingCS, Maps, dll.)
│   │   ├── router/                # Konfigurasi Vue Router & route guards
│   │   ├── services/              # Axios HTTP client terkonfigurasi
│   │   ├── stores/                # Pinia store (auth.js, notification.js)
│   │   ├── utils/                 # Helper fungsi (whatsapp.js, maps, formatting)
│   │   └── views/                 # Halaman view (Home, Katalog, Detail, Dashboard, Profil, Peta)
│   └── package.json
├── routes/
│   └── api.php                    # Rute API v1 terproteksi Sanctum
└── tests/
    └── Feature/                   # Pengujian fungsional (Auth, RBAC, Kos, Booking, Geocoding)
```

---

## ⚙️ Panduan Instalasi & Menjalankan Proyek

### 🐳 Metode 1: Menggunakan Docker (Direkomendasikan untuk Deploy & Server)

Cara paling praktis menjalankan seluruh stack aplikasi (**Backend Laravel 11**, **Frontend Vue 3 SPA**, dan **Database MariaDB 11.4**) tanpa perlu menginstal PHP, Node.js, atau database di komputer lokal/server:

```bash
# 1. Masuk ke direktori repositori
cd backend-kos

# 2. Salin template konfigurasi Docker
cp .env.docker.example .env

# 3. Build image dan jalankan seluruh container di background
docker compose up -d --build

# 4. Pantau log inisialisasi awal (migrasi database & seeder otomatis)
docker compose logs -f app
```

Setelah kontainer aktif, aplikasi langsung dapat diakses di:
👉 **`http://localhost:8080`** (atau `http://<IP_SERVER>:8080`)

#### 💡 Perintah Operasional Docker:
```bash
# Melihat status kontainer aktif
docker compose ps

# Menjalankan perintah Artisan di dalam kontainer app
docker compose exec app php artisan <perintah>

# Menjalankan migrasi database ulang & seeder secara manual
docker compose exec app php artisan migrate --seed --force

# Backup data MariaDB ke file SQL
docker compose exec db mariadb-dump -u kosku -psecret_password_kosku kosku > backup.sql

# Update kode terbaru dari repo
git pull origin main && docker compose up -d --build

# Menghentikan seluruh kontainer
docker compose down
```

---

### 💻 Metode 2: Instalasi Manual / Lokal (Tanpa Docker)

#### 1. Prasyarat Sistem
- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x dan **npm**
- **DDEV** (opsional) atau Web Server lokal (Apache/Nginx + MariaDB/MySQL)

---

#### 2. Pengaturan Backend (Laravel)

```bash
# 1. Masuk ke direktori utama
cd backend-kos

# 2. Pasang dependensi PHP
composer install

# 3. Salin file lingkungan
cp .env.example .env

# 4. Generate Application Key
php artisan key:generate

# 5. Jalankan migrasi dan seeder database
php artisan migrate --seed

# 6. Jalankan server backend (jika tanpa DDEV)
php artisan serve
```

> *Jika menggunakan **DDEV**:*
> ```bash
> ddev start
> ddev artisan migrate --seed
> ```

---

#### 3. Pengaturan Frontend (Vue 3)

```bash
# 1. Masuk ke folder frontend
cd frontend

# 2. Pasang dependensi JavaScript
npm install

# 3. Jalankan server pengembang (HMR)
npm run dev

# Atau lakukan build untuk produksi
npm run build
```

Setelah dijalankan, buka peramban Anda di `http://localhost:5173` (atau domain DDEV Anda).

---

## 🔑 Kredensial Akun Bawaan (Hasil Seeder)

Semua akun menggunakan kata sandi bawaan: **`password`**

| Peran (*Role*) | Email | Kata Sandi | Keterangan |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@kos.test` | `password` | Akses penuh panel kontrol & master fasilitas |
| **Mitra Pemilik** | `pemilik@kos.test` | `password` | Pengelola Kos Melati Harmoni & kamar |
| **Pencari Kos** | `pencari@kos.test` | `password` | Akun pengguna untuk reservasi hunian sewa |

---

## 📡 Daftar Endpoint REST API Utama (`/api/v1`)

### Autentikasi & Akun
- `POST /api/v1/register` &mdash; Registrasi pengguna baru (`pencari` atau `pemilik`)
- `POST /api/v1/login` &mdash; Masuk dan mendapatkan Sanctum Bearer Token
- `POST /api/v1/logout` &mdash; Cabut token sesi aktif
- `GET /api/v1/me` &mdash; Informasi profil pengguna terotentikasi
- `PUT /api/v1/me` &mdash; Pembaruan nama dan nomor WhatsApp/telepon

### Properti Kos & Kamar
- `GET /api/v1/kos` &mdash; Daftar kos dengan filter pencarian (`q`, `jenis`, `harga_min`, `harga_max`, `fasilitas`)
- `GET /api/v1/kos/{id}` &mdash; Rincian detail kos, pemilik, kamar, dan fasilitas
- `POST /api/v1/kos` &mdash; Tambah properti kos baru *(Khusus Pemilik)*
- `PUT /api/v1/kos/{id}` &mdash; Perbarui data kos *(Pemilik & Admin)*
- `DELETE /api/v1/kos/{id}` &mdash; Hapus data kos *(Pemilik & Admin)*
- `POST /api/v1/kos/{id}/kamar` &mdash; Tambah unit kamar ke properti *(Khusus Pemilik)*
- `PUT /api/v1/kamar/{id}` &mdash; Perbarui data kamar *(Pemilik & Admin)*
- `DELETE /api/v1/kamar/{id}` &mdash; Hapus unit kamar *(Pemilik & Admin)*

### Alur Pemesanan (*Booking*)
- `POST /api/v1/pemesanan` &mdash; Ajukan pemesanan kamar baru *(Khusus Pencari)*
- `GET /api/v1/pemesanan` &mdash; Riwayat pesanan (pencari) atau pesanan masuk (pemilik/admin)
- `GET /api/v1/pemesanan/{id}` &mdash; Rincian reservasi pemesanan
- `PATCH /api/v1/pemesanan/{id}/status` &mdash; Update status (`disetujui`, `ditolak`, `dibatalkan`)

### Fasilitas & Administrasi
- `GET /api/v1/fasilitas` &mdash; Master data fasilitas kos
- `POST /api/v1/fasilitas` &mdash; Tambah fasilitas baru *(Khusus Admin)*
- `GET /api/v1/admin/pengguna` &mdash; Manajemen pengguna *(Khusus Admin)*

---

## 🧪 Pengujian Otomatis (*Automated Testing*)

Proyek ini dilengkapi dengan rangkaian pengujian fitur (*feature tests*) yang mencakup alur otentikasi, validasi hak akses per peran (RBAC), transaksi sewa, integritas database, geocoding, dan pencarian:

```bash
# Menjalankan seluruh pengujian (52 tests)
php artisan test --compact

# Atau melalui DDEV
ddev artisan test --compact
```

---

## 📄 Lisensi

Proyek ini dikembangkan di bawah lisensi terbuka [MIT License](LICENSE).
